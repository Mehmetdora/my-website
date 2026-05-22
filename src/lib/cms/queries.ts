import { categories, lifePosts, notes, posts, projects, tags } from "./mock-data";
import type { LifePost, Note } from "@/types/content";
import type { Category, Paginated, Tag } from "@/types/common";
import type { Post } from "@/types/post";
import type { Project } from "@/types/project";

const pageSize = 9;

function paginate<T>(items: T[], page = 1, limit = pageSize): Paginated<T> {
  const safePage = Math.max(1, page);
  const totalPages = Math.max(1, Math.ceil(items.length / limit));
  const start = (safePage - 1) * limit;
  return {
    docs: items.slice(start, start + limit),
    page: safePage,
    totalPages,
    totalDocs: items.length,
  };
}

const visible = <T extends { visibility?: string }>(item: T) => item.visibility !== "private";
const listed = <T extends { visibility?: string }>(item: T) => item.visibility === undefined || item.visibility === "public";

export async function getSiteSettings() {
  return {
    currentFocus: ["STM32 UART DMA", "FreeRTOS task yapısı", "ESP32 sensor dashboard"],
    techStack: ["C", "C++", "STM32", "ESP32", "Arduino", "FreeRTOS", "UART", "SPI", "I2C", "CAN", "TypeScript", "Next.js", "PostgreSQL", "Git", "Linux"],
  };
}

export async function getTags(): Promise<Tag[]> {
  return tags;
}

export async function getCategories(): Promise<Category[]> {
  return categories;
}

export async function getLatestPosts(limit = 3): Promise<Post[]> {
  return posts.filter(listed).slice(0, limit);
}

export async function getPublishedPosts(params: { page?: number; category?: string; tag?: string; q?: string } = {}) {
  const q = params.q?.toLocaleLowerCase("tr-TR");
  const filtered = posts.filter(listed).filter((post) => {
    const matchCategory = !params.category || post.category?.slug === params.category;
    const matchTag = !params.tag || post.tags.some((tag) => tag.slug === params.tag);
    const matchQuery = !q || `${post.title} ${post.summary ?? ""}`.toLocaleLowerCase("tr-TR").includes(q);
    return matchCategory && matchTag && matchQuery;
  });
  return paginate(filtered, params.page);
}

export async function getPostBySlug(slug: string): Promise<Post | null> {
  return posts.find((post) => post.slug === slug && visible(post)) ?? null;
}

export async function getRelatedPosts(post: Post, limit = 3): Promise<Post[]> {
  return posts
    .filter((candidate) => candidate.slug !== post.slug && listed(candidate))
    .filter((candidate) => candidate.category?.slug === post.category?.slug || candidate.tags.some((tag) => post.tags.some((own) => own.slug === tag.slug)))
    .slice(0, limit);
}

export async function getFeaturedProjects(limit = 3): Promise<Project[]> {
  return projects.filter(listed).slice(0, limit);
}

export async function getProjects(params: { page?: number; status?: string; technology?: string; difficulty?: string } = {}) {
  const filtered = projects.filter(listed).filter((project) => {
    const matchStatus = !params.status || project.status === params.status;
    const matchTech = !params.technology || project.technologies.some((tech) => tech.toLocaleLowerCase("tr-TR") === params.technology);
    const matchDifficulty = !params.difficulty || project.difficultyLevel === params.difficulty;
    return matchStatus && matchTech && matchDifficulty;
  });
  return paginate(filtered, params.page);
}

export async function getProjectBySlug(slug: string): Promise<Project | null> {
  return projects.find((project) => project.slug === slug && visible(project)) ?? null;
}

export async function getRelatedProjects(project: Project, limit = 3): Promise<Project[]> {
  return projects
    .filter((candidate) => candidate.slug !== project.slug && listed(candidate))
    .filter((candidate) => candidate.technologies.some((tech) => project.technologies.includes(tech)))
    .slice(0, limit);
}

export async function getLifePosts(params: { page?: number } = {}) {
  return paginate(lifePosts.filter(listed), params.page);
}

export async function getLatestLifePosts(limit = 3): Promise<LifePost[]> {
  return lifePosts.filter(listed).slice(0, limit);
}

export async function getLifePostBySlug(slug: string): Promise<LifePost | null> {
  return lifePosts.find((post) => post.slug === slug && visible(post)) ?? null;
}

export async function getNotes(params: { page?: number } = {}) {
  return paginate(notes, params.page, 12);
}

export async function getLatestNotes(limit = 5): Promise<Note[]> {
  return notes.slice(0, limit);
}

export async function getNoteBySlug(slug: string): Promise<Note | null> {
  return notes.find((note) => note.slug === slug) ?? null;
}

export async function searchContent(q = "", type?: string) {
  const query = q.toLocaleLowerCase("tr-TR").trim();
  if (query.length < 2) return [];

  const results = [
    ...posts.filter(listed).map((item) => ({ type: "Yazı", title: item.title, excerpt: item.summary ?? "", url: `/blog/${item.slug}`, date: item.publishedAt, tags: item.tags })),
    ...projects.filter(listed).map((item) => ({ type: "Proje", title: item.title, excerpt: item.shortDescription ?? "", url: `/projects/${item.slug}`, date: item.startDate, tags: item.tags })),
    ...lifePosts.filter(listed).map((item) => ({ type: "Kişisel", title: item.title, excerpt: item.excerpt ?? "", url: `/life/${item.slug}`, date: item.publishedAt, tags: item.tags })),
    ...notes.map((item) => ({ type: "Not", title: item.content.slice(0, 58), excerpt: item.content, url: `/notes/${item.slug}`, date: item.createdAt, tags: item.tags })),
  ];

  return results.filter((result) => {
    const matchType = !type || type === "all" || result.type.toLocaleLowerCase("tr-TR") === type;
    const matchQuery = `${result.title} ${result.excerpt} ${result.tags.map((tag) => tag.name).join(" ")}`.toLocaleLowerCase("tr-TR").includes(query);
    return matchType && matchQuery;
  });
}

export async function getContentByTag(slug: string) {
  return {
    posts: posts.filter((post) => listed(post) && post.tags.some((tag) => tag.slug === slug)),
    projects: projects.filter((project) => listed(project) && project.tags.some((tag) => tag.slug === slug)),
    notes: notes.filter((note) => note.tags.some((tag) => tag.slug === slug)),
  };
}

export async function getContentByCategory(slug: string) {
  return posts.filter((post) => listed(post) && post.category?.slug === slug);
}
