import { Container } from "@/components/layout/Container";
import { NoteCard } from "@/components/notes/NoteCard";
import { PageHeader } from "@/components/shared/PageHeader";
import { Pagination } from "@/components/shared/Pagination";
import { getNotes } from "@/lib/cms/queries";
import { createMetadata } from "@/lib/seo/metadata";

export const metadata = createMetadata({ title: "Notlar", description: "Telegram tarzı kısa teknik notlar.", path: "/notes" });

export default async function NotesPage({ searchParams }: { searchParams: Promise<{ page?: string }> }) {
  const params = await searchParams;
  const result = await getNotes({ page: Number(params.page ?? 1) });
  return (
    <>
      <PageHeader title="Notlar" description="Kısa teknik gözlemler, hatırlatmalar ve çalışma günlüğü parçaları." />
      <Container className="grid gap-4 pb-16">
        {result.docs.map((note) => <NoteCard key={note.id} note={note} />)}
        <Pagination currentPage={result.page} totalPages={result.totalPages} basePath="/notes" />
      </Container>
    </>
  );
}
