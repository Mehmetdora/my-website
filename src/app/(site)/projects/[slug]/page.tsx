import { notFound } from "next/navigation";
import Link from "next/link";
import { Github, PlayCircle } from "lucide-react";
import { Container } from "@/components/layout/Container";
import { Badge } from "@/components/shared/Badge";
import { ImageWithFallback } from "@/components/shared/ImageWithFallback";
import { RichTextRenderer } from "@/components/shared/RichTextRenderer";
import { ProjectCard, ProjectStatusBadge } from "@/components/projects/ProjectCard";
import { ExternalLink } from "@/components/shared/ExternalLink";
import { getProjectBySlug, getRelatedProjects } from "@/lib/cms/queries";
import { createMetadata } from "@/lib/seo/metadata";

export async function generateMetadata({ params }: { params: Promise<{ slug: string }> }) {
  const { slug } = await params;
  const project = await getProjectBySlug(slug);
  if (!project) return createMetadata({ title: "Proje bulunamadı" });
  return createMetadata({ title: project.title, description: project.shortDescription, path: `/projects/${project.slug}`, image: project.coverImage?.url });
}

export default async function ProjectDetailPage({ params }: { params: Promise<{ slug: string }> }) {
  const { slug } = await params;
  const project = await getProjectBySlug(slug);
  if (!project || project.visibility === "private") notFound();
  const related = await getRelatedProjects(project);

  return (
    <Container size="wide" className="py-12">
      <Link href="/projects" className="text-sm font-medium text-accent hover:underline">Projelere dön</Link>
      <header className="mt-8 max-w-4xl">
        <div className="flex flex-wrap gap-2">
          <ProjectStatusBadge status={project.status} />
        </div>
        <h1 className="mt-4 text-4xl font-semibold tracking-normal sm:text-5xl">{project.title}</h1>
        {project.shortDescription ? <p className="mt-4 text-lg leading-8 text-soft">{project.shortDescription}</p> : null}
        <div className="mt-6 flex flex-wrap gap-3">
          {project.githubUrl ? <ExternalLink href={project.githubUrl} label="GitHub"><Github size={17} /> GitHub</ExternalLink> : null}
          {project.demoVideoUrl ? <ExternalLink href={project.demoVideoUrl} label="Demo video"><PlayCircle size={17} /> Demo</ExternalLink> : null}
        </div>
      </header>
      <div className="mt-10"><ImageWithFallback image={project.coverImage} title={project.title} priority /></div>
      <section className="mt-10 grid gap-4 md:grid-cols-3">
        <Info title="Teknolojiler" items={project.technologies} />
        <Info title="Donanım" items={project.hardwareUsed} />
        <Info title="Yazılım" items={project.softwareUsed} />
      </section>
      {project.longDescription ? <article className="mt-12 max-w-4xl"><RichTextRenderer content={project.longDescription} /></article> : null}
      {related.length ? <section className="mt-16"><h2 className="text-2xl font-semibold">Benzer projeler</h2><div className="mt-6 grid gap-5 md:grid-cols-3">{related.map((item) => <ProjectCard key={item.id} project={item} />)}</div></section> : null}
    </Container>
  );
}

function Info({ title, items }: { title: string; items: string[] }) {
  return (
    <div className="rounded-lg border border-border bg-panel p-5">
      <h2 className="font-semibold">{title}</h2>
      <div className="mt-4 flex flex-wrap gap-2">{items.map((item) => <Badge key={item}>{item}</Badge>)}</div>
    </div>
  );
}
