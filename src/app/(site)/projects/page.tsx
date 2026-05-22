import { Container } from "@/components/layout/Container";
import { ProjectCard } from "@/components/projects/ProjectCard";
import { EmptyState } from "@/components/shared/EmptyState";
import { PageHeader } from "@/components/shared/PageHeader";
import { Pagination } from "@/components/shared/Pagination";
import { getProjects } from "@/lib/cms/queries";
import { createMetadata } from "@/lib/seo/metadata";

export const metadata = createMetadata({ title: "Projeler", description: "Embedded ve IoT projeleri portfolyosu.", path: "/projects" });

export default async function ProjectsPage({ searchParams }: { searchParams: Promise<{ page?: string; status?: string; technology?: string; difficulty?: string }> }) {
  const params = await searchParams;
  const result = await getProjects({ page: Number(params.page ?? 1), status: params.status, technology: params.technology, difficulty: params.difficulty });
  return (
    <div className="bg-[#0a0f1e]">
      <PageHeader title="Projeler" description="Mikrodenetleyici, haberleşme protokolleri, IoT ve düşük seviye yazılım denemeleri." />
      <Container size="wide" className="pb-16">
        {result.docs.length ? <div className="grid gap-5 md:grid-cols-2 lg:grid-cols-3">{result.docs.map((project) => <ProjectCard key={project.id} project={project} />)}</div> : <EmptyState title="Proje bulunamadı" />}
        <Pagination currentPage={result.page} totalPages={result.totalPages} basePath="/projects" />
      </Container>
    </div>
  );
}
