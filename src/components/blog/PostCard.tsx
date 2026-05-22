import Link from "next/link";
import { Clock } from "lucide-react";
import { formatDate } from "@/lib/utils/date";
import type { Post } from "@/types/post";
import { Badge } from "@/components/shared/Badge";
import { ImageWithFallback } from "@/components/shared/ImageWithFallback";
import { TagList } from "@/components/shared/TagList";

export function PostCard({ post }: { post: Post }) {
  return (
    <Link href={`/blog/${post.slug}`} className="group block h-full rounded-lg border border-border bg-panel p-3 shadow-soft transition hover:-translate-y-0.5 hover:border-accent">
      <ImageWithFallback image={post.coverImage} title={post.title} />
      <div className="p-2">
        <div className="mb-3 mt-2 flex flex-wrap items-center gap-2">
          {post.category ? <Badge>{post.category.name}</Badge> : null}
          <span className="inline-flex items-center gap-1 text-xs text-soft"><Clock size={13} /> {post.readingTime ?? 1} dk</span>
        </div>
        <h2 className="text-xl font-semibold tracking-normal group-hover:text-accent">{post.title}</h2>
        {post.summary ? <p className="mt-2 line-clamp-3 text-sm leading-6 text-soft">{post.summary}</p> : null}
        <div className="mt-4">
          <TagList tags={post.tags.slice(0, 3)} />
        </div>
        <p className="mt-4 text-xs text-soft">{formatDate(post.publishedAt)}</p>
      </div>
    </Link>
  );
}
