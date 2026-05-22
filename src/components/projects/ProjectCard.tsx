import Link from "next/link";
import type { Project } from "@/types/project";
import { Badge } from "@/components/shared/Badge";
import { ImageWithFallback } from "@/components/shared/ImageWithFallback";

const statusLabels = {
  planned: "Planlandı",
  "in-progress": "Devam Ediyor",
  completed: "Tamamlandı",
  archived: "Arşivlendi",
};

export function ProjectStatusBadge({ status }: { status: Project["status"] }) {
  return <Badge className="border-signal/40 bg-signal/10 text-signal">{statusLabels[status]}</Badge>;
}

export function ProjectCard({ project }: { project: Project }) {
  return (
    <Link href={`/projects/${project.slug}`} className="group block h-full rounded-lg border border-border bg-panel p-3 shadow-soft transition hover:-translate-y-0.5 hover:border-accent">
      <ImageWithFallback image={project.coverImage} title={project.title} />
      <div className="p-2">
        <div className="mb-3 mt-2 flex flex-wrap gap-2">
          <ProjectStatusBadge status={project.status} />
        </div>
        <h2 className="text-xl font-semibold tracking-normal group-hover:text-accent">{project.title}</h2>
        {project.shortDescription ? <p className="mt-2 line-clamp-3 text-sm leading-6 text-soft">{project.shortDescription}</p> : null}
        <div className="mt-4 flex flex-wrap gap-2">
          {project.technologies.slice(0, 4).map((tech) => <Badge key={tech}>{tech}</Badge>)}
        </div>
      </div>
    </Link>
  );
}
