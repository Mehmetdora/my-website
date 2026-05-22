"use client";

import { useRouter } from "next/navigation";
import type { Category, Tag } from "@/types/common";
import { Badge } from "@/components/shared/Badge";
import { cn } from "@/lib/utils/cn";

interface Props {
  categories: Category[];
  tags: Tag[];
  activeCategory?: string | null;
  activeTag?: string | null;
  q?: string | null;
}

export default function BlogFilters({ categories, tags, activeCategory, activeTag, q }: Props) {
  const router = useRouter();

  const makeHref = (opts: { category?: string | null; tag?: string | null; q?: string | null; page?: number } = {}) => {
    const sp = new URLSearchParams();
    if (opts.category) sp.set("category", opts.category);
    if (opts.tag) sp.set("tag", opts.tag);
    if (opts.q) sp.set("q", opts.q);
    if (opts.page && opts.page > 1) sp.set("page", String(opts.page));
    const qs = sp.toString();
    return qs ? `/blog?${qs}` : "/blog";
  };

  return (
    <div className="mb-8 rounded-2xl border border-white/10 bg-[#101827] p-5">
      <div className="flex flex-wrap items-center gap-2">
        <button type="button" onClick={() => router.push(makeHref({ q }))} aria-label="Tüm blog yazılarını göster" className="inline-block">
          <Badge className={cn(
            "border-white/10 bg-white/5 text-slate-300 transition hover:border-cyan-300/60 hover:text-white",
            !activeCategory && !activeTag && "border-[#5DF8D8] bg-[#5DF8D8]/12 text-[#5DF8D8]",
          )}>
            Tümü
          </Badge>
        </button>

        {categories.map((cat) => {
          const selected = activeCategory === cat.slug;
          const href = makeHref({ category: cat.slug, tag: activeTag ?? null, q });
          return (
            <button key={cat.slug} type="button" onClick={() => router.push(href)} aria-current={selected ? "true" : undefined} className="inline-block">
              <Badge className={cn(
                "border-white/10 bg-white/5 text-slate-300 transition hover:border-cyan-300/60 hover:text-white",
                selected && "border-[#6FD1D7] bg-[#6FD1D7]/12 text-[#6FD1D7] shadow-[0_0_0_1px_rgba(111,209,215,0.18)]",
              )}>
                {cat.name}
              </Badge>
            </button>
          );
        })}
      </div>

      <div className="mt-4 flex flex-wrap items-center gap-2">
        {tags.map((tagItem) => {
          const selected = activeTag === tagItem.slug;
          const href = selected ? makeHref({ category: activeCategory ?? null, q }) : makeHref({ category: activeCategory ?? null, tag: tagItem.slug, q });

          return (
            <button key={tagItem.slug} type="button" onClick={() => router.push(href)} aria-current={selected ? "true" : undefined} className="inline-block">
              <Badge className={cn(
                "border-white/10 bg-white/5 text-slate-300 transition hover:border-[#5DF8D8]/70 hover:text-white",
                selected && "border-[#5DF8D8] bg-[#5DF8D8]/14 text-[#5DF8D8] shadow-[0_0_0_1px_rgba(93,248,216,0.2)]",
              )}>
                #{tagItem.name}
              </Badge>
            </button>
          );
        })}
      </div>
    </div>
  );
}
