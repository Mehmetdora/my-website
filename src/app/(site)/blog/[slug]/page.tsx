import { notFound } from "next/navigation";
import Link from "next/link";
import { Container } from "@/components/layout/Container";
import { Badge } from "@/components/shared/Badge";
import { ImageWithFallback } from "@/components/shared/ImageWithFallback";
import { RichTextRenderer } from "@/components/shared/RichTextRenderer";
import { TagList } from "@/components/shared/TagList";
import { PostCard } from "@/components/blog/PostCard";
import { formatDate } from "@/lib/utils/date";
import { getPostBySlug, getRelatedPosts } from "@/lib/cms/queries";
import { createMetadata } from "@/lib/seo/metadata";

export async function generateMetadata({ params }: { params: Promise<{ slug: string }> }) {
  const { slug } = await params;
  const post = await getPostBySlug(slug);
  if (!post) return createMetadata({ title: "Yazı bulunamadı" });
  return createMetadata({ title: post.seoTitle ?? post.title, description: post.seoDescription ?? post.summary, path: `/blog/${post.slug}`, image: post.coverImage?.url });
}

export default async function PostDetailPage({ params }: { params: Promise<{ slug: string }> }) {
  const { slug } = await params;
  const post = await getPostBySlug(slug);
  if (!post || post.visibility === "private") notFound();
  const related = await getRelatedPosts(post);

  return (
    <Container size="wide" className="py-12">
      <Link href="/blog" className="text-sm font-medium text-accent hover:underline">Blog&apos;a dön</Link>
      <header className="mt-8 max-w-4xl">
        {post.category ? <Badge>{post.category.name}</Badge> : null}
        <h1 className="mt-4 text-4xl font-semibold tracking-normal sm:text-5xl">{post.title}</h1>
        {post.summary ? <p className="mt-4 text-lg leading-8 text-soft">{post.summary}</p> : null}
        <div className="mt-5 flex flex-wrap gap-3 text-sm text-soft">
          <span>{formatDate(post.publishedAt)}</span>
          {post.updatedAt ? <span>Güncellendi: {formatDate(post.updatedAt)}</span> : null}
          <span>{post.readingTime ?? 1} dk okuma</span>
        </div>
        <div className="mt-5"><TagList tags={post.tags} /></div>
      </header>
      <div className="mt-10"><ImageWithFallback image={post.coverImage} title={post.title} priority /></div>
      <div className="mt-12 grid gap-10 lg:grid-cols-[minmax(0,1fr)_260px]">
        <article><RichTextRenderer content={post.content} /></article>
        <aside className="hidden lg:block">
          <div className="sticky top-24 rounded-lg border border-border bg-panel p-4">
            <p className="text-sm font-semibold">İçindekiler</p>
            <div className="mt-3 grid gap-2 text-sm text-soft">
              {post.content.filter((block) => block.type === "heading").map((block) => block.type === "heading" ? (
                <a key={block.text} href={`#${block.text.toLocaleLowerCase("tr-TR").replaceAll(" ", "-")}`} className="hover:text-accent">{block.text}</a>
              ) : null)}
            </div>
          </div>
        </aside>
      </div>
      {related.length ? (
        <section className="mt-16">
          <h2 className="text-2xl font-semibold">İlgili yazılar</h2>
          <div className="mt-6 grid gap-5 md:grid-cols-3">{related.map((item) => <PostCard key={item.id} post={item} />)}</div>
        </section>
      ) : null}
    </Container>
  );
}
