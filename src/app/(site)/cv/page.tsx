import { Download, FileText } from "lucide-react";
import { Container } from "@/components/layout/Container";
import { createMetadata } from "@/lib/seo/metadata";
import { siteConfig } from "@/config/site";

export const metadata = createMetadata({
  title: "Resume",
  description: `${siteConfig.name} CV ve kısa yetkinlik özeti.`,
  path: "/cv",
});

export default function CvPage() {
  return (
    <div className="bg-[#0a0f1e]">
      <Container className="py-16">
        <span className="section-label">Resume</span>
        <h1 className="mt-4 text-4xl font-extrabold text-white sm:text-5xl">CV</h1>
        <p className="mt-5 max-w-2xl text-lg leading-8 text-slate-400">
          Buraya PDF CV dosyanı eklediğinde buton doğrudan indirme/görüntüleme bağlantısı olarak çalışacak. Şimdilik kısa yetkinlik özeti hazır duruyor.
        </p>
        <div className="mt-8 flex flex-wrap gap-3">
          <a href="/cv.pdf" className="focus-ring inline-flex min-h-12 items-center gap-2 rounded-md bg-accent px-6 text-sm font-bold text-[#07101f]">
            <Download size={17} /> PDF CV
          </a>
          <a href="/contact" className="focus-ring inline-flex min-h-12 items-center gap-2 rounded-md border border-cyan-300/25 px-6 text-sm font-bold text-slate-100 hover:border-accent hover:text-accent">
            <FileText size={17} /> İletişime geç
          </a>
        </div>
      </Container>
    </div>
  );
}
