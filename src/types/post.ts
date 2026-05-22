import type { Category, Media, RichBlock, Status, Tag, Visibility } from "./common";

export type Post = {
  id: string;
  title: string;
  slug: string;
  summary?: string;
  content: RichBlock[];
  coverImage?: Media;
  category?: Category;
  tags: Tag[];
  status: Extract<Status, "published">;
  visibility: Visibility;
  publishedAt: string;
  updatedAt?: string;
  readingTime?: number;
  seoTitle?: string;
  seoDescription?: string;
  githubUrl?: string;
  mediumUrl?: string;
  telegramUrl?: string;
};
