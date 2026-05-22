"use client";
import { useRouter } from "next/navigation";
import type { Tag } from "@/types/common";
import { Badge } from "./Badge";

export function TagList({ tags }: { tags: Tag[] }) {
  const router = useRouter();
  if (!tags.length) return null;

  const handleKey = (e: React.KeyboardEvent, slug: string) => {
    if (e.key === "Enter" || e.key === " ") {
      e.preventDefault();
      router.push(`/tags/${slug}`);
    }
  };

  return (
    <div className="flex flex-wrap gap-2">
      {tags.map((tag) => (
        <span
          key={tag.slug}
          role="link"
          tabIndex={0}
          onClick={() => router.push(`/tags/${tag.slug}`)}
          onKeyDown={(e) => handleKey(e, tag.slug)}
        >
          <Badge>#{tag.name}</Badge>
        </span>
      ))}
    </div>
  );
}
