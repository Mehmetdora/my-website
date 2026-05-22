import Link from "next/link";
import { Github, Linkedin, Mail, Send } from "lucide-react";
import { siteConfig } from "@/config/site";
import { Container } from "./Container";
import { ExternalLink } from "@/components/shared/ExternalLink";

export function SiteFooter() {
  return (
    <footer className="border-t border-white/10 bg-[#0a0f1e]">
      <Container size="wide" className="grid gap-8 py-10 md:grid-cols-[1.2fr_1.5fr_1fr]">
        <div>
          <p className="text-lg font-semibold text-white">{siteConfig.name}</p>
          <p className="mt-1 text-sm text-slate-400">{siteConfig.role}</p>
          <p className="mt-1 text-sm text-slate-500">{siteConfig.location}</p>
        </div>
        <div>
          <nav className="flex flex-wrap gap-x-4 gap-y-2 text-sm text-slate-400" aria-label="Footer navigation">
            {siteConfig.nav.map((item) => (
              <Link key={item.href} className="hover:text-accent" href={item.href}>
                {item.title}
              </Link>
            ))}
          </nav>
        </div>
        <div className="md:text-right">
          <div className="flex gap-2 md:justify-end">
            <ExternalLink href={siteConfig.links.github} label="GitHub">
              <Github size={17} />
            </ExternalLink>
            <ExternalLink href={siteConfig.links.linkedin} label="LinkedIn">
              <Linkedin size={17} />
            </ExternalLink>
            <ExternalLink href={siteConfig.links.telegram} label="Telegram">
              <Send size={17} />
            </ExternalLink>
            <ExternalLink href={siteConfig.links.email} label="Email">
              <Mail size={17} />
            </ExternalLink>
          </div>
          <p className="mt-4 text-sm text-slate-500">© 2026 {siteConfig.name}</p>
        </div>
      </Container>
    </footer>
  );
}
