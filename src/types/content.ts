import type { Media, RichBlock, Tag, Visibility } from "./common";

export type LifePost = {
  id: string;
  title: string;
  slug: string;
  excerpt?: string;
  content: RichBlock[];
  images: Media[];
  publishedAt: string;
  category?: "sport" | "music" | "friends" | "family" | "hobby" | "daily";
  location?: string;
  mood?: string;
  activity?: string;
  soundtrack?: string;
  people?: string[];
  color?: "cyan" | "violet" | "rose" | "amber" | "emerald";
  tags: Tag[];
  visibility: Visibility;
};

export type Note = {
  id: string;
  slug: string;
  content: string;
  createdAt: string;
  tags: Tag[];
  relatedProject?: string;
  relatedPost?: string;
};
