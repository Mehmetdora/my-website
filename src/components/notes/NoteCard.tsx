import Link from "next/link";
import { formatDate } from "@/lib/utils/date";
import type { Note } from "@/types/content";
import { TagList } from "@/components/shared/TagList";

export function NoteCard({ note }: { note: Note }) {
  return (
    <Link href={`/notes/${note.slug}`} className="block rounded-lg border border-border bg-panel p-5 transition hover:border-accent">
      <p className="text-sm text-soft">{formatDate(note.createdAt)}</p>
      <p className="mt-3 leading-7">{note.content}</p>
      <div className="mt-4">
        <TagList tags={note.tags} />
      </div>
    </Link>
  );
}
