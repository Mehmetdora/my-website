import { Container } from "@/components/layout/Container";
import { PostCard } from "@/components/blog/PostCard";
import { EmptyState } from "@/components/shared/EmptyState";
import { PageHeader } from "@/components/shared/PageHeader";
import { getContentByCategory } from "@/lib/cms/queries";
import { createMetadata } from "@/lib/seo/metadata";

export async function generateMetadata({ params }: { params: Promise<{ slug: string }> }) {
  const { slug } = await params;
  return createMetadata({ title: slug, description: `${slug} kategorisindeki yazılar.`, path: `/categories/${slug}` });
}

export default async function CategoryPage({ params }: { params: Promise<{ slug: string }> }) {
  const { slug } = await params;
  const posts = await getContentByCategory(slug);
  return (
    <>
      <PageHeader title={slug} description="Bu kategoriye ait teknik yazılar." />
      <Container size="wide" className="pb-16">
        {posts.length ? <div className="grid gap-5 md:grid-cols-3">{posts.map((post) => <PostCard key={post.id} post={post} />)}</div> : <EmptyState title="Bu kategoriye ait içerik yok" />}
      </Container>
    </>
  );
}
