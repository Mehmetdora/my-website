import Image from "next/image";
import Link from "next/link";
import { FileText, Github, Linkedin, Mail } from "lucide-react";
import { Container } from "@/components/layout/Container";
import { siteConfig } from "@/config/site";

const topSkills = [
  "C / C++",
  "Embedded C",
  "STM32 / ESP32",
  "RTOS / FreeRTOS",
  "UART / SPI / I2C",
  "CAN-BUS",
  "PCB & Debug",
  "Firmware Architecture",
];

export function HeroSection() {
  return (
    <section className="bg-[#0a0f1e]">
      <Container size="wide" className="grid min-h-[calc(100vh-5rem)] items-center gap-12 py-14 lg:grid-cols-[1.18fr_0.82fr] lg:py-20">
        <div>
          <span className="section-label">Welcome</span>
          <h1 className="mt-5 max-w-4xl text-[clamp(1.6rem,3.5vw,2.8rem)] font-extrabold leading-[1.05] tracking-normal text-white">
            Hi, I&apos;m <span className="text-accent">{siteConfig.name}</span><br /> Embedded Systems Developer.
          </h1>
          <p className="mt-6 max-w-3xl text-base leading-8 text-slate-400 sm:text-lg">
            Bilgisayar mühendisliği öğrencisi olarak STM32, ESP32, C/C++, IoT ve elektronik üzerine çalışıyorum. Öğrendiklerimi projeler, teknik yazılar, kısa notlar ve kod parçaları halinde burada topluyorum.
          </p>

          <div className="mt-9 grid gap-8 grid-cols-1">
            <div>
              <span className="text-xs font-bold uppercase tracking-[0.16em] text-slate-500">Find me on</span>
              <div className="mt-3 flex flex-wrap gap-3">
                <SocialLink href={siteConfig.links.github} label="GitHub"><Github size={19} /></SocialLink>
                <SocialLink href={siteConfig.links.linkedin} label="LinkedIn"><Linkedin size={19} /></SocialLink>
                <SocialLink href={siteConfig.links.telegram} label="Telegram">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" className="h-5 w-5" fill="none" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                    <path d="M22 2L11 13" />
                    <path d="M22 2L15 22l-4-9-9-4 18-7z" />
                  </svg>
                </SocialLink>
                <SocialLink href={siteConfig.links.email} label="Email"><Mail size={19} /></SocialLink>
              </div>
            </div>
            <div>
              <span className="text-xs font-bold uppercase tracking-[0.16em] text-slate-500">Top skills</span>
              <div className="mt-3 flex flex-wrap gap-2">
                {topSkills.map((skill) => (
                  <span key={skill} className="rounded-full border border-cyan-300/18 bg-cyan-300/7 px-3 py-1.5 text-sm font-semibold text-slate-200">
                    {skill}
                  </span>
                ))}
              </div>
            </div>
          </div>

          <div className="mt-9 flex flex-wrap gap-4">
            <Link href="/contact" className="focus-ring inline-flex min-h-12 items-center rounded-md bg-accent px-7 text-sm font-bold text-[#07101f] transition hover:translate-y-[-1px] hover:shadow-[0_14px_30px_-18px_#22d3ee]">
              İletişime geç
            </Link>
            <Link href={siteConfig.links.cv} className="focus-ring inline-flex min-h-12 items-center gap-2 rounded-md border border-cyan-300/25 bg-transparent px-7 text-sm font-bold text-slate-100 transition hover:border-accent hover:text-accent">
              <FileText size={17} /> CV görüntüle
            </Link>
          </div>
        </div>

        <div className="flex justify-center lg:justify-end">
          <div className="relative w-full max-w-[400px]">
            <div className="absolute inset-5 rounded-[28px] bg-accent/18 blur-3xl" />
            <div className="relative rounded-[28px] border border-cyan-300/18 bg-[#101827] p-4 shadow-[0_30px_100px_-55px_#22d3ee]">
              <Image
                src={siteConfig.profileImage}
                alt={`${siteConfig.name} profil fotoğrafı`}
                width={400}
                height={494}
                priority
                className="aspect-[400/494] w-full rounded-[20px] object-cover"
              />
              <div className="mt-4 flex justify-center">
                <div className="rounded-full bg-accent px-4 py-2 text-sm font-bold text-[#07101f] shadow-lg">
                  Available for projects
                </div>
              </div>
            </div>
          </div>
        </div>
      </Container>
    </section>
  );
}

function SocialLink({ href, label, children }: { href: string; label: string; children: React.ReactNode }) {
  return (
    <a
      href={href}
      aria-label={label}
      target="_blank"
      rel="noopener noreferrer"
      className="focus-ring inline-flex h-12 w-12 items-center justify-center rounded-md border border-white/10 bg-white/5 text-slate-200 transition hover:border-accent hover:bg-accent hover:text-[#07101f]"
    >
      {children}
    </a>
  );
}
