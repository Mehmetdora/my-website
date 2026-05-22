import { notFound } from "next/navigation";
import Link from "next/link";
import { CalendarDays, MapPin } from "lucide-react";
import { Container } from "@/components/layout/Container";
import { LifeImageCarousel } from "@/components/life/LifeImageCarousel";
import { RichTextRenderer } from "@/components/shared/RichTextRenderer";
import { formatDate } from "@/lib/utils/date";
import { getLifePostBySlug } from "@/lib/cms/queries";
import { createMetadata } from "@/lib/seo/metadata";

export async function generateMetadata({ params }: { params: Promise<{ slug: string }> }) {
  const { slug } = await params;
  const post = await getLifePostBySlug(slug);
  return createMetadata({ title: post?.title ?? "Kişisel paylaşım", description: post?.excerpt, path: `/life/${slug}`, image: post?.images[0]?.url });
}

export default async function LifeDetailPage({ params }: { params: Promise<{ slug: string }> }) {
  const { slug } = await params;
  const post = await getLifePostBySlug(slug);
  if (!post || post.visibility === "private") notFound();
  return (
    <div className="bg-[#0a0f1e]">
      <Container size="wide" className="py-12">
        <Link href="/life" className="text-sm font-bold text-rose-300 hover:underline">Life feed&apos;e dön</Link>
        <article className="mt-8 overflow-hidden rounded-3xl border border-white/10 bg-[#101827] p-4 sm:p-6 lg:p-8">
          <LifeImageCarousel images={post.images} title={post.title} />

          <header className="mx-auto mt-8 max-w-4xl">
            <div className="flex flex-wrap items-center gap-x-5 gap-y-2 text-sm font-semibold text-slate-500">
              <span className="inline-flex items-center gap-2"><CalendarDays size={16} className="text-rose-300" /> {formatDate(post.publishedAt)}</span>
              {post.location ? <span className="inline-flex items-center gap-2"><MapPin size={16} className="text-rose-300" /> {post.location}</span> : null}
            </div>
            {post.excerpt ? <h1 className="mt-5 text-2xl font-normal leading-10 text-slate-100 sm:text-3xl">{post.excerpt}</h1> : null}
          </header>

          <div className="mx-auto mt-10 max-w-4xl rounded-2xl border border-white/10 bg-[#0a0f1e]/70 p-6">
            <RichTextRenderer content={post.content} />
          </div>
        </article>
      </Container>
    </div>
  );
}
