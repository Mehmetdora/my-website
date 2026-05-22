import { Container } from "@/components/layout/Container";
import { PostCard } from "@/components/blog/PostCard";
import { EmptyState } from "@/components/shared/EmptyState";
import { PageHeader } from "@/components/shared/PageHeader";
import { Pagination } from "@/components/shared/Pagination";
import { getCategories, getPublishedPosts, getTags } from "@/lib/cms/queries";
import { createMetadata } from "@/lib/seo/metadata";
import BlogFilters from "@/components/blog/BlogFilters";

export const metadata = createMetadata({ title: "Blog", description: "Embedded sistemler, C/C++, mikrodenetleyiciler ve teknik notlar.", path: "/blog" });

export default async function BlogPage({ searchParams }: { searchParams: Promise<{ page?: string; category?: string; tag?: string; q?: string }> }) {
  const params = await searchParams;
  // Ensure single values, not arrays
  const page = Array.isArray(params.page) ? Number(params.page[0]) : Number(params.page ?? 1);
  const category = Array.isArray(params.category) ? params.category[0] : params.category;
  const tag = Array.isArray(params.tag) ? params.tag[0] : params.tag;
  const q = Array.isArray(params.q) ? params.q[0] : params.q;

  // makeHref moved into client-side BlogFilters component

  const [result, categories, tags] = await Promise.all([
    getPublishedPosts({ page, category, tag, q }),
    getCategories(),
    getTags(),
  ]);
  const activeCategory = category;
  const activeTag = tag;

  return (
    <div className="bg-[#0a0f1e]">
      <PageHeader title="Blog" description="Embedded sistemler, C/C++, mikrodenetleyiciler ve öğrendiğim teknik notlar." />
      <Container size="wide" className="pb-16">
        <BlogFilters categories={categories} tags={tags} activeCategory={activeCategory} activeTag={activeTag} q={q} />
        {result.docs.length ? (
          <div className="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
            {result.docs.map((post) => <PostCard key={post.id} post={post} />)}
          </div>
        ) : <EmptyState title="Yazı bulunamadı" description="Seçili filtrelere uygun yayınlanmış yazı yok." />}
        <Pagination currentPage={result.page} totalPages={result.totalPages} basePath="/blog" />
      </Container>
    </div>
  );
}
