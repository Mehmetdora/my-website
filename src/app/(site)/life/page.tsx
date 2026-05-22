import { Container } from "@/components/layout/Container";
import { LifeFeed } from "@/components/life/LifeFeed";
import { EmptyState } from "@/components/shared/EmptyState";
import { Pagination } from "@/components/shared/Pagination";
import { getLifePosts } from "@/lib/cms/queries";
import { createMetadata } from "@/lib/seo/metadata";

export const metadata = createMetadata({
  title: "Life",
  description: "Mehmet Dora'nın sosyal hayatı, hobileri, müzikleri, ailesi ve arkadaşlarıyla ilgili kişisel paylaşımları.",
  path: "/life",
});

export default async function LifePage({ searchParams }: { searchParams: Promise<{ page?: string }> }) {
  const params = await searchParams;
  const result = await getLifePosts({ page: Number(params.page ?? 1) });

  return (
    <div className="bg-[#0a0f1e]">
      <section className="relative overflow-hidden border-b border-white/10">
        <div className="absolute left-1/2 top-0 h-80 w-80 -translate-x-1/2 rounded-full bg-rose-500/16 blur-3xl" />
        <Container size="wide" className="relative py-16 lg:py-20">
          <div className="max-w-4xl">
            <span className="section-label">Personal feed</span>
            <h1 className="mt-4 text-[clamp(2.7rem,6vw,5.4rem)] font-extrabold leading-[1.02] tracking-normal text-white">
              Life outside the systems.
            </h1>
            <p className="mt-6 max-w-3xl text-base leading-8 text-slate-400 sm:text-lg">
              Burası iş ve embedded projeler dışındaki hayatımdan rasgele günlük anları paylaştığım tek yer(ona göre 😎).
            </p>
          </div>
        </Container>
      </section>

      <Container size="wide" className="py-14">
        <div className="mb-8">
          <span className="section-label">Timeline</span>
          <h2 className="mt-3 text-3xl font-extrabold text-white sm:text-4xl">Son paylaşımlar</h2>
        </div>

        {result.docs.length ? (
          <LifeFeed posts={result.docs} />
        ) : (
          <EmptyState title="Henüz kişisel paylaşım yok" description="Bu alan sosyal hayat, müzik, spor ve hobiler için hazırlanıyor." />
        )}
        <Pagination currentPage={result.page} totalPages={result.totalPages} basePath="/life" />
      </Container>
    </div>
  );
}
