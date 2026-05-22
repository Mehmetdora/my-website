"use client";

import { X } from "lucide-react";
import { useState } from "react";
import type { Media } from "@/types/common";
import { ImageWithFallback } from "@/components/shared/ImageWithFallback";

export function GalleryGrid({ media }: { media: Media[] }) {
  const [active, setActive] = useState<Media | null>(null);

  return (
    <>
      <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        {media.map((item) => (
          <button key={item.id} type="button" onClick={() => setActive(item)} className="focus-ring rounded-lg text-left">
            <ImageWithFallback image={item} title={item.alt ?? "Galeri görseli"} />
          </button>
        ))}
      </div>
      {active ? (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4" role="dialog" aria-modal="true">
          <button type="button" aria-label="Görseli kapat" onClick={() => setActive(null)} className="absolute right-4 top-4 inline-flex h-10 w-10 items-center justify-center rounded-md bg-white/10 text-white hover:bg-white/20">
            <X size={20} />
          </button>
          {/* eslint-disable-next-line @next/next/no-img-element */}
          <img src={active.url} alt={active.alt ?? "Galeri görseli"} className="max-h-[82vh] max-w-[92vw] rounded-lg object-contain" />
        </div>
      ) : null}
    </>
  );
}
