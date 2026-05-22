import type { Media } from "@/types/common";

type PayloadMediaLike = {
  id?: string;
  url?: string;
  alt?: string;
  width?: number;
  height?: number;
  mimeType?: string;
};

export function mapPayloadMediaToMedia(media?: PayloadMediaLike | null): Media | undefined {
  if (!media?.url) return undefined;
  return {
    id: media.id ?? media.url,
    url: media.url,
    alt: media.alt,
    width: media.width,
    height: media.height,
    mimeType: media.mimeType,
  };
}
