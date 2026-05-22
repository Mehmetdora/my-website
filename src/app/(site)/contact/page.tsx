import { Github, Linkedin, Mail, Send } from "lucide-react";
import { Container } from "@/components/layout/Container";
import { ExternalLink } from "@/components/shared/ExternalLink";
import { PageHeader } from "@/components/shared/PageHeader";
import { siteConfig } from "@/config/site";
import { createMetadata } from "@/lib/seo/metadata";

export const metadata = createMetadata({ title: "İletişim", description: "Projeler, teknik yazılar ve iş birlikleri için iletişim.", path: "/contact" });

export default function ContactPage() {
  return (
    <div className="bg-[#0a0f1e]">
      <PageHeader title="İletişim" description="Projeler, teknik yazılar, embedded fikirleri veya iş birlikleri için ulaşabilirsin." />
      <Container className="grid gap-6 pb-16 md:grid-cols-2">
        <div className="rounded-lg border border-white/10 bg-[#101827] p-6">
          <h2 className="text-xl font-semibold text-white">Bağlantılar</h2>
          <div className="mt-5 flex flex-wrap gap-3">
            <ExternalLink href={siteConfig.links.github} label="GitHub"><Github size={17} /> GitHub</ExternalLink>
            <ExternalLink href={siteConfig.links.linkedin} label="LinkedIn"><Linkedin size={17} /> LinkedIn</ExternalLink>
            <ExternalLink href={siteConfig.links.telegram} label="Telegram"><Send size={17} /> Telegram</ExternalLink>
            <ExternalLink href={siteConfig.links.email} label="Email"><Mail size={17} /> Email</ExternalLink>
          </div>
        </div>
        <div className="rounded-lg border border-white/10 bg-[#101827] p-6">
          <h2 className="text-xl font-semibold text-white">Contact form notu</h2>
          <p className="mt-3 leading-7 text-slate-400">
            Form backend tarafında rate-limit, honeypot ve validation ile bağlanmalı. Şimdilik public frontend gizli anahtar kullanmadan güvenli bağlantı linkleri sunuyor.
          </p>
        </div>
      </Container>
    </div>
  );
}
