import Link from "next/link";
import { Search } from "lucide-react";
import { Container } from "@/components/layout/Container";
import { Badge } from "@/components/shared/Badge";
import { EmptyState } from "@/components/shared/EmptyState";
import { PageHeader } from "@/components/shared/PageHeader";
import { TagList } from "@/components/shared/TagList";
import { formatDate } from "@/lib/utils/date";
import { searchContent } from "@/lib/cms/queries";
import { createMetadata } from "@/lib/seo/metadata";

export const metadata = createMetadata({ title: "Arama", description: "Site içi içerik araması.", path: "/search" });

export default async function SearchPage({ searchParams }: { searchParams: Promise<{ q?: string; type?: string }> }) {
  const params = await searchParams;
  const query = params.q ?? "";
  const results = await searchContent(query, params.type);

  return (
    <>
      <PageHeader title="Arama" description="Yazı, proje, not ve kişisel paylaşımlar içinde ara." />
      <Container className="pb-16">
        <form action="/search" className="flex gap-2">
          <label className="sr-only" htmlFor="q">Arama</label>
          <input id="q" name="q" defaultValue={query} minLength={2} placeholder="stm32, uart, esp32..." className="focus-ring min-h-12 flex-1 rounded-md border border-border bg-panel px-4" />
          <button type="submit" aria-label="Ara" className="focus-ring inline-flex h-12 w-12 items-center justify-center rounded-md bg-accent text-white">
            <Search size={19} />
          </button>
        </form>
        <div className="mt-8 grid gap-4">
          {query.length < 2 ? <EmptyState title="Aramak için en az 2 karakter yaz" description="Örneğin stm32, uart veya freertos." /> : null}
          {query.length >= 2 && !results.length ? <EmptyState title="Sonuç bulunamadı" description="Farklı bir kelime veya etiket deneyebilirsin." /> : null}
          {results.map((result) => (
            <Link key={`${result.type}-${result.url}`} href={result.url} className="rounded-lg border border-border bg-panel p-5 transition hover:border-accent">
              <div className="flex flex-wrap items-center gap-2">
                <Badge>{result.type}</Badge>
                {result.date ? <span className="text-xs text-soft">{formatDate(result.date)}</span> : null}
              </div>
              <h2 className="mt-3 text-xl font-semibold">{result.title}</h2>
              <p className="mt-2 text-sm leading-6 text-soft">{result.excerpt}</p>
              <div className="mt-4"><TagList tags={result.tags} /></div>
            </Link>
          ))}
        </div>
      </Container>
    </>
  );
}
