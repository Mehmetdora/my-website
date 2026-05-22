import { ImageWithFallback } from "@/components/shared/ImageWithFallback";
import type { Media } from "@/types/common";

export function LifeImageCarousel({ images, title }: { images: Media[]; title: string }) {
  if (!images.length) return null;

  if (images.length === 1) {
    return <ImageWithFallback image={images[0]} title={title} priority className="rounded-2xl" />;
  }

  return (
    <div className="overflow-x-auto pb-3">
      <div className="flex snap-x snap-mandatory gap-4">
        {images.map((image, index) => (
          <div key={image.id} className="min-w-[86%] snap-center sm:min-w-[68%] lg:min-w-[58%]">
            <ImageWithFallback image={image} title={`${title} fotoğraf ${index + 1}`} priority={index === 0} className="rounded-2xl" />
          </div>
        ))}
      </div>
    </div>
  );
}
