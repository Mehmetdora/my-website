import { Container } from "@/components/layout/Container";
import { PostCard } from "@/components/blog/PostCard";
import { ProjectCard } from "@/components/projects/ProjectCard";
import { NoteCard } from "@/components/notes/NoteCard";
import { EmptyState } from "@/components/shared/EmptyState";
import { PageHeader } from "@/components/shared/PageHeader";
import { getContentByTag } from "@/lib/cms/queries";
import { createMetadata } from "@/lib/seo/metadata";

export async function generateMetadata({ params }: { params: Promise<{ slug: string }> }) {
  const { slug } = await params;
  return createMetadata({ title: `#${slug}`, description: `${slug} etiketi altındaki içerikler.`, path: `/tags/${slug}` });
}

export default async function TagPage({ params }: { params: Promise<{ slug: string }> }) {
  const { slug } = await params;
  const content = await getContentByTag(slug);
  const hasContent = content.posts.length || content.projects.length || content.notes.length;
  return (
    <>
      <PageHeader title={`#${slug}`} description="Bu etikete ait yazılar, projeler ve notlar." />
      <Container size="wide" className="space-y-12 pb-16">
        {!hasContent ? <EmptyState title="Bu etikete ait içerik yok" /> : null}
        {content.posts.length ? <Section title="Yazılar"><div className="grid gap-5 md:grid-cols-3">{content.posts.map((item) => <PostCard key={item.id} post={item} />)}</div></Section> : null}
        {content.projects.length ? <Section title="Projeler"><div className="grid gap-5 md:grid-cols-3">{content.projects.map((item) => <ProjectCard key={item.id} project={item} />)}</div></Section> : null}
        {content.notes.length ? <Section title="Notlar"><div className="grid gap-4">{content.notes.map((item) => <NoteCard key={item.id} note={item} />)}</div></Section> : null}
      </Container>
    </>
  );
}

function Section({ title, children }: { title: string; children: React.ReactNode }) {
  return <section><h2 className="mb-5 text-2xl font-semibold">{title}</h2>{children}</section>;
}
