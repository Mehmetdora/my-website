import { CalendarDays, MapPin } from "lucide-react";
import { formatDate } from "@/lib/utils/date";
import type { LifePost } from "@/types/content";
import { ImageWithFallback } from "@/components/shared/ImageWithFallback";

export function LifePostCard({ post }: { post: LifePost }) {
  return (
    <article className="group block overflow-hidden rounded-2xl border border-white/10 bg-[#101827] transition hover:-translate-y-1 hover:border-rose-300/60">
      <div className="p-3">
        <ImageWithFallback image={post.images[0]} title={post.title} className="rounded-xl transition duration-300 group-hover:scale-[1.015]" />
      </div>
      <div className="p-5">
        <div className="flex flex-wrap items-center gap-x-4 gap-y-2 text-xs font-semibold text-slate-500">
          <span className="inline-flex items-center gap-2"><CalendarDays size={14} className="text-rose-300" /> {formatDate(post.publishedAt)}</span>
          {post.location ? <span className="inline-flex items-center gap-2"><MapPin size={14} className="text-rose-300" /> {post.location}</span> : null}
        </div>
        {post.excerpt ? <p className="mt-4 text-lg font-normal leading-8 text-slate-200">{post.excerpt}</p> : null}
      </div>
    </article>
  );
}
