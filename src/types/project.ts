import type { Media, RichBlock, Tag, Visibility } from "./common";

export type Project = {
  id: string;
  title: string;
  slug: string;
  shortDescription?: string;
  longDescription?: RichBlock[];
  coverImage?: Media;
  gallery?: Media[];
  technologies: string[];
  hardwareUsed: string[];
  softwareUsed: string[];
  status: "planned" | "in-progress" | "completed" | "archived";
  difficultyLevel?: "beginner" | "intermediate" | "advanced";
  visibility: Visibility;
  tags: Tag[];
  githubUrl?: string;
  demoVideoUrl?: string;
  startDate?: string;
  endDate?: string;
};
