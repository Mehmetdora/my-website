export type Status = "draft" | "published" | "archived";

export type Visibility = "public" | "hidden" | "private";

export type Media = {
  id: string;
  url: string;
  alt?: string;
  width?: number;
  height?: number;
  mimeType?: string;
};

export type Tag = {
  id: string;
  name: string;
  slug: string;
};

export type Category = {
  id: string;
  name: string;
  slug: string;
  description?: string;
};

export type Paginated<T> = {
  docs: T[];
  page: number;
  totalPages: number;
  totalDocs: number;
};

export type RichBlock =
  | { type: "heading"; level: 2 | 3 | 4; text: string }
  | { type: "paragraph"; text: string }
  | { type: "quote"; text: string }
  | { type: "list"; ordered?: boolean; items: string[] }
  | { type: "code"; language: string; filename?: string; code: string }
  | { type: "callout"; tone: "info" | "warning" | "success" | "danger" | "hardware" | "code"; title?: string; text: string }
  | { type: "image"; image: Media; caption?: string };
