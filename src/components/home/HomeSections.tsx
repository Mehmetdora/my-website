import Link from "next/link";
import { Braces, Cpu, RadioTower, Timer } from "lucide-react";
import type { Post } from "@/types/post";
import type { Project } from "@/types/project";
import { Container } from "@/components/layout/Container";
import { Badge } from "@/components/shared/Badge";
import { ProjectCard } from "@/components/projects/ProjectCard";
import { TagList } from "@/components/shared/TagList";
import { formatDate } from "@/lib/utils/date";

const expertise = [
  {
    title: "Embedded Systems",
    description: "STM32 ve ESP32 tabanlı mikrodenetleyici projeleri, sensör okuma, çevresel birimler ve düşük seviye firmware geliştirme.",
    icon: Cpu,
  },
  {
    title: "Real-Time Software",
    description: "Interrupt-driven mimari, FreeRTOS task yapısı, UART DMA, timer tabanlı işler ve zaman kritik kontrol akışları.",
    icon: Timer,
  },
  {
    title: "Communication Protocols",
    description: "UART, SPI, I2C, CAN-BUS ve BLE gibi haberleşme katmanlarında paket yapısı, debug ve güvenilir veri akışı.",
    icon: RadioTower,
  },
  {
    title: "Code & Project Documentation",
    description: "Teknik yazılar, proje günlükleri ve öğrendiklerimi anlaşılır hale getiren içerik üretimi.",
    icon: Braces,
  },
];

export function ExpertiseSection() {
  return (
    <section id="expertise" className="bg-[#0a0f1e] py-16">
      <Container size="wide">
        <span className="section-label">What I do</span>
        <h2 className="mt-3 text-4xl font-extrabold tracking-normal text-white sm:text-5xl">Uzmanlık Alanlarım</h2>
        <div className="mt-10 grid gap-6 md:grid-cols-2 xl:grid-cols-4">
          {expertise.map((item) => {
            const Icon = item.icon;
            return (
              <article key={item.title} className="group relative rounded-lg border border-white/10 bg-[#101827] p-7 transition hover:-translate-y-1 hover:border-accent/60">
                <div className="inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-cyan-300/10 text-accent">
                  <Icon size={26} strokeWidth={1.8} />
                </div>
                <h3 className="mt-6 text-xl font-bold text-white">{item.title}</h3>
                <p className="mt-3 min-h-28 text-sm leading-7 text-slate-400">{item.description}</p>
              </article>
            );
          })}
        </div>
      </Container>
    </section>
  );
}

export function FeaturedProjects({ projects }: { projects: Project[] }) {
  return (
    <section className="bg-[#0a0f1e] py-14">
      <Container size="wide">
        <SectionTitle label="Portfolio" title="Öne çıkan projeler" href="/projects" linkText="Tüm projeler →" />
        <div className="mt-8 grid gap-5 md:grid-cols-3">
          {projects.map((project) => <ProjectCard key={project.id} project={project} />)}
        </div>
      </Container>
    </section>
  );
}

export function LatestPosts({ posts }: { posts: Post[] }) {
  return (
    <section className="bg-[#0a0f1e] py-16">
      <Container size="wide">
        <SectionTitle label="From the blog" title="Recent Posts" href="/blog" linkText="View all →" />
        <div className="mt-8 grid gap-6 lg:grid-cols-3">
          {posts.map((post) => (
            <Link key={post.id} href={`/blog/${post.slug}`} className="group flex min-h-[330px] flex-col rounded-lg border border-white/10 bg-[#101827] p-7 transition hover:-translate-y-1 hover:border-accent/60">
              <div className="text-sm text-slate-500">
                <time dateTime={post.publishedAt}>{formatDate(post.publishedAt)}</time>
                <div className="mt-4"><TagList tags={post.tags.slice(0, 4)} /></div>
              </div>
              <h3 className="mt-7 text-2xl font-bold leading-8 text-white group-hover:text-accent">{post.title}</h3>
              {post.summary ? <p className="mt-4 line-clamp-4 text-sm leading-7 text-slate-400">{post.summary}</p> : null}
              <span className="mt-auto pt-7 text-sm font-bold text-accent">Read more →</span>
            </Link>
          ))}
        </div>
      </Container>
    </section>
  );
}

function SectionTitle({ label, title, href, linkText }: { label: string; title: string; href: string; linkText: string }) {
  return (
    <div className="flex items-end justify-between gap-4">
      <div>
        <span className="section-label">{label}</span>
        <h2 className="mt-3 text-4xl font-extrabold tracking-normal text-white sm:text-5xl">{title}</h2>
      </div>
      <Link href={href} className="hidden text-sm font-bold text-accent hover:underline sm:inline">{linkText}</Link>
    </div>
  );
}

export function TechStackSection({ stack }: { stack: string[] }) {
  return (
    <section className="bg-[#0a0f1e] py-12">
      <Container>
        <span className="section-label">Stack</span>
        <h2 className="mt-3 text-3xl font-extrabold text-white">Kullandığım ve öğrendiğim teknolojiler</h2>
        <div className="mt-6 flex flex-wrap gap-2">
          {stack.map((item) => <Badge key={item} className="border-cyan-300/20 bg-cyan-300/7 text-slate-200">{item}</Badge>)}
        </div>
      </Container>
    </section>
  );
}
