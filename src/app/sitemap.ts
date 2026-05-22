import type { MetadataRoute } from "next";
import { siteConfig } from "@/config/site";
import { getLatestLifePosts, getLatestPosts, getProjects, getNotes } from "@/lib/cms/queries";

export default async function sitemap(): Promise<MetadataRoute.Sitemap> {
  const [posts, projects, life, notes] = await Promise.all([
    getLatestPosts(100),
    getProjects({}),
    getLatestLifePosts(100),
    getNotes({}),
  ]);

  const staticRoutes = ["", "/about", "/blog", "/projects", "/life", "/notes", "/search", "/contact", "/cv"].map((path) => ({
    url: `${siteConfig.url}${path}`,
  }));

  return [
    ...staticRoutes,
    ...posts.map((post) => ({ url: `${siteConfig.url}/blog/${post.slug}`, lastModified: post.updatedAt ?? post.publishedAt })),
    ...projects.docs.map((project) => ({ url: `${siteConfig.url}/projects/${project.slug}`, lastModified: project.endDate ?? project.startDate })),
    ...life.map((post) => ({ url: `${siteConfig.url}/life/${post.slug}`, lastModified: post.publishedAt })),
    ...notes.docs.map((note) => ({ url: `${siteConfig.url}/notes/${note.slug}`, lastModified: note.createdAt })),
  ];
}
