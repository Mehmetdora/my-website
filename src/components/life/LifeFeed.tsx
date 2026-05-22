"use client";

import { CalendarDays, MapPin, X } from "lucide-react";
import { useEffect, useState } from "react";
import { formatDate } from "@/lib/utils/date";
import type { LifePost } from "@/types/content";
import { ImageWithFallback } from "@/components/shared/ImageWithFallback";

export function LifeFeed({ posts }: { posts: LifePost[] }) {
  const [activePost, setActivePost] = useState<LifePost | null>(null);

  useEffect(() => {
    function onKeyDown(event: KeyboardEvent) {
      if (event.key === "Escape") setActivePost(null);
    }

    if (activePost) {
      document.body.style.overflow = "hidden";
      window.addEventListener("keydown", onKeyDown);
    }

    return () => {
      document.body.style.overflow = "";
      window.removeEventListener("keydown", onKeyDown);
    };
  }, [activePost]);

  return (
    <>
      <div className="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
        {posts.map((post) => (
          <button
            key={post.id}
            type="button"
            onClick={() => setActivePost(post)}
            className="group block overflow-hidden rounded-2xl border border-white/10 bg-[#101827] text-left transition hover:-translate-y-1 hover:border-rose-300/60"
          >
            <div className="p-3">
              <ImageWithFallback image={post.images[0]} title={post.title} className="rounded-xl transition duration-300 group-hover:scale-[1.015]" />
            </div>
            <div className="p-5">
              <PostMeta post={post} size="sm" />
              {post.excerpt ? <p className="mt-4 text-lg font-normal leading-8 text-slate-200">{post.excerpt}</p> : null}
            </div>
          </button>
        ))}
      </div>

      {activePost ? <LifePreview post={activePost} onClose={() => setActivePost(null)} /> : null}
    </>
  );
}

function LifePreview({ post, onClose }: { post: LifePost; onClose: () => void }) {
  return (
    <div
      className="fixed inset-0 z-50 flex items-center justify-center bg-black/82 p-3 backdrop-blur-sm"
      role="dialog"
      aria-modal="true"
      onClick={(e) => {
        if (e.target === e.currentTarget) onClose();
      }}
    >
      <button
        type="button"
        aria-label="Paylaşımı kapat"
        onClick={onClose}
        className="absolute right-4 top-4 z-10 inline-flex h-11 w-11 items-center justify-center rounded-full border border-white/15 bg-[#101827]/90 text-white transition hover:border-rose-300 hover:text-rose-200"
      >
        <X size={20} />
      </button>

      <article className="grid max-h-[calc(100vh-1.5rem)] w-full max-w-5xl grid-rows-[minmax(0,1fr)_auto] overflow-hidden rounded-3xl border border-white/10 bg-[#101827] shadow-[0_30px_100px_-45px_rgb(244_114_182)]">
        <PreviewImages post={post} />
        <footer className="border-t border-white/10 bg-[#101827] p-4 sm:p-5">
          <PostMeta post={post} size="md" />
          {post.excerpt ? <p className="mt-3 text-base font-normal leading-7 text-slate-100 sm:text-lg">{post.excerpt}</p> : null}
        </footer>
      </article>
    </div>
  );
}

function PreviewImages({ post }: { post: LifePost }) {
  if (!post.images.length) {
    return (
      <div className="min-h-0 bg-[#0a0f1e] p-3">
        <ImageWithFallback title={post.title} className="h-full rounded-2xl" />
      </div>
    );
  }

  return (
    <div className="min-h-0 overflow-x-auto bg-black">
      <div className="flex h-full snap-x snap-mandatory">
        {post.images.map((image, index) => (
          <div key={image.id} className="h-full min-w-full snap-center">
            {/* eslint-disable-next-line @next/next/no-img-element */}
            <img src={image.url} alt={image.alt ?? `${post.title} fotoğraf ${index + 1}`} className="h-full w-full object-cover" />
          </div>
        ))}
      </div>
    </div>
  );
}

function PostMeta({ post, size }: { post: LifePost; size: "sm" | "md" }) {
  const iconSize = size === "sm" ? 14 : 16;
  const className = size === "sm" ? "text-xs" : "text-sm";

  return (
    <div className={`flex flex-wrap items-center gap-x-4 gap-y-2 font-semibold text-slate-500 ${className}`}>
      <span className="inline-flex items-center gap-2">
        <CalendarDays size={iconSize} className="text-rose-300" /> {formatDate(post.publishedAt)}
      </span>
      {post.location ? (
        <span className="inline-flex items-center gap-2">
          <MapPin size={iconSize} className="text-rose-300" /> {post.location}
        </span>
      ) : null}
    </div>
  );
}
