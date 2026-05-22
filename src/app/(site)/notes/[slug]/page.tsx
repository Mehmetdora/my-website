import { notFound } from "next/navigation";
import Link from "next/link";
import { Container } from "@/components/layout/Container";
import { TagList } from "@/components/shared/TagList";
import { formatDate } from "@/lib/utils/date";
import { getNoteBySlug } from "@/lib/cms/queries";
import { createMetadata } from "@/lib/seo/metadata";

export async function generateMetadata({ params }: { params: Promise<{ slug: string }> }) {
  const { slug } = await params;
  const note = await getNoteBySlug(slug);
  return createMetadata({ title: note?.content.slice(0, 40) ?? "Not", description: note?.content, path: `/notes/${slug}` });
}

export default async function NoteDetailPage({ params }: { params: Promise<{ slug: string }> }) {
  const { slug } = await params;
  const note = await getNoteBySlug(slug);
  if (!note) notFound();
  return (
    <Container className="py-12">
      <Link href="/notes" className="text-sm font-medium text-accent hover:underline">Notlara dön</Link>
      <article className="mt-8 rounded-lg border border-border bg-panel p-6">
        <p className="text-sm text-soft">{formatDate(note.createdAt)}</p>
        <h1 className="mt-4 text-2xl font-semibold leading-9">{note.content}</h1>
        <div className="mt-5"><TagList tags={note.tags} /></div>
      </article>
    </Container>
  );
}
