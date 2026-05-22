import Image from "next/image";
import type { Media } from "@/types/common";

export function ImageWithFallback({ image, title, priority = false, className = "" }: { image?: Media; title: string; priority?: boolean; className?: string }) {
  if (!image?.url) {
    return (
      <div className={`flex aspect-[16/10] items-center justify-center rounded-lg border border-border bg-muted ${className}`}>
        <span className="font-mono text-sm text-soft">{title}</span>
      </div>
    );
  }

  return (
    <Image
      src={image.url}
      alt={image.alt ?? title}
      width={image.width ?? 1200}
      height={image.height ?? 800}
      priority={priority}
      className={`aspect-[16/10] rounded-lg object-cover ${className}`}
    />
  );
}
