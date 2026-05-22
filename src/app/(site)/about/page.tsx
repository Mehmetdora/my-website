import Link from "next/link";
import { BookOpen, Camera, Cpu, Gamepad2, GraduationCap, MapPin, Music, Route } from "lucide-react";
import { Container } from "@/components/layout/Container";
import { Badge } from "@/components/shared/Badge";
import { createMetadata } from "@/lib/seo/metadata";
import { siteConfig } from "@/config/site";

export const metadata = createMetadata({
  title: "About",
  description:
    "Mehmet Dora'nın embedded systems, bilgisayar mühendisliği, projeler, teknik yazılar ve kişisel ilgi alanları üzerine hikayesi.",
  path: "/about",
});

const stats = [
  { value: "CENG", label: "Computer Engineering" },
  { value: "STM32", label: "Main MCU Focus" },
  { value: "ESP32", label: "IoT & Wireless" },
  { value: "C/C++", label: "Core Languages" },
];

const hobbies = [
  {
    title: "Elektronik ve maker projeleri",
    description: "Yeni sensörler, geliştirme kartları ve küçük prototiplerle uğraşmak; öğrendiğim şeyi fiziksel bir çıktıya dönüştürmek.",
    icon: Cpu,
  },
  {
    title: "Teknik okuma ve not tutma",
    description: "Datasheet, uygulama notu, proje dokümantasyonu ve öğrendiğim konuları daha sonra dönüp bakabileceğim şekilde yazmak.",
    icon: BookOpen,
  },
  {
    title: "Yürüyüş ve keşif",
    description: "Kafayı toparlamak, yeni fikirleri sindirmek ve uzun teknik oturumlardan sonra ritmi yeniden bulmak için yürümek.",
    icon: Route,
  },
  {
    title: "Müzik, fotoğraf ve oyun",
    description: "Teknik üretimin dışında müzik dinlemek, anları kaydetmek ve oyunlarla zihni biraz başka bir moda almak.",
    icon: Music,
  },
];

const education = [
  {
    degree: "Computer Engineering",
    period: "Devam ediyor",
    org: "Bilgisayar mühendisliği öğrencisi",
  },
  {
    degree: "Embedded Systems Self-Lab",
    period: "Sürekli",
    org: "STM32, ESP32, FreeRTOS, haberleşme protokolleri ve proje tabanlı öğrenme",
  },
];

export default function AboutPage() {
  return (
    <div className="bg-[#0a0f1e]">
      <Container size="wide" className="py-16 lg:py-20">
        <header className="grid gap-10 lg:grid-cols-[1.15fr_0.85fr] lg:items-end">
          <div>
            <span className="section-label">About Me</span>
            <h1 className="mt-4 text-[clamp(2.7rem,6vw,5.4rem)] font-extrabold leading-[1.02] tracking-normal text-white">
              {siteConfig.name}
            </h1>
            <p className="mt-4 text-lg font-semibold text-slate-300">
              {siteConfig.role} · {siteConfig.location}
            </p>
            <p className="mt-6 max-w-4xl text-base leading-8 text-slate-400 sm:text-lg">
              Embedded sistemler, low-level programming ve proje tabanlı öğrenme etrafında kendini geliştiren bir bilgisayar mühendisliği öğrencisiyim. STM32, ESP32, C/C++, IoT ve elektronik üzerine çalışıyor; öğrendiklerimi yazılar, kod parçaları, proje günlükleri ve kişisel notlar halinde düzenli bir arşive dönüştürmeyi seviyorum.
            </p>
          </div>
          <div className="grid grid-cols-2 gap-4">
            {stats.map((stat) => (
              <div key={stat.label} className="rounded-lg border border-white/10 bg-[#101827] p-5">
                <span className="block text-3xl font-extrabold text-accent">{stat.value}</span>
                <span className="mt-2 block text-sm font-semibold text-slate-400">{stat.label}</span>
              </div>
            ))}
          </div>
        </header>

        <div className="mt-16 space-y-12">
          <NarrativeSection title="Nasıl Başladı?">
            <p>
              Yazılım ve elektroniğin kesiştiği alan beni her zaman daha fazla çekti. Bir kod satırının gerçek dünyada bir pini değiştirmesi, bir sensörden veri okuması veya bir cihazın davranışını belirlemesi benim için embedded sistemleri özel yapan taraf.
            </p>
            <p>
              Bu yüzden öğrenme rotamı mikrodenetleyiciler, haberleşme protokolleri, C/C++ ve donanım-yazılım ilişkisi üzerine kuruyorum. Amacım sadece çalışan demo üretmek değil; neden çalıştığını anlayan, debug edilebilir ve belgelenebilir sistemler geliştirmek.
            </p>
          </NarrativeSection>

          <NarrativeSection title="Derinleştiğim Alanlar">
            <p>
              Şu anda STM32 tarafında UART DMA, timer, interrupt ve FreeRTOS task yapıları; ESP32 tarafında ise BLE, IoT haberleşmesi ve sensör odaklı küçük sistemler üzerinde çalışıyorum.
            </p>
            <p>
              Kendi projelerimde kodu katmanlara ayırmaya, parser ve driver mantığını temiz tutmaya, öğrendiğim parçaları düzenli teknik notlara dönüştürmeye özen gösteriyorum.
            </p>
            <div className="mt-5 flex flex-wrap gap-2">
              {["Embedded C", "C++", "STM32", "ESP32", "FreeRTOS", "UART", "SPI", "I2C", "CAN", "BLE", "Linux", "Git"].map((item) => (
                <Badge key={item} className="border-cyan-300/20 bg-cyan-300/7 text-slate-200">{item}</Badge>
              ))}
            </div>
          </NarrativeSection>

          <NarrativeSection title="Bu Site Neden Var?">
            <p>
              Bu site benim için hem kişisel bir vitrin hem de düzenli bir mühendislik defteri. Blog kısmında teknik yazılar, projelerde geliştirdiğim uygulamalar ve My Life tarafında daha kişisel kayıtlar yer alacak.
            </p>
            <p>
              Hedefim zamanla sadece sonuçları değil, öğrenme sürecini de görünür kılmak: karşılaştığım hatalar, denediğim çözümler, donanım seçimleri ve proje kararları bu arşivin parçası olacak.
            </p>
          </NarrativeSection>
        </div>

        <section className="mt-16">
          <h2 className="text-3xl font-extrabold text-white">Hobilerim ve Kişisel Tarafım</h2>
          <div className="mt-8 grid gap-5 md:grid-cols-2 xl:grid-cols-4">
            {hobbies.map((hobby) => {
              const Icon = hobby.icon;
              return (
                <article key={hobby.title} className="rounded-lg border border-white/10 bg-[#101827] p-6">
                  <div className="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-cyan-300/10 text-accent">
                    <Icon size={23} strokeWidth={1.8} />
                  </div>
                  <h3 className="mt-5 text-lg font-bold text-white">{hobby.title}</h3>
                  <p className="mt-3 text-sm leading-7 text-slate-400">{hobby.description}</p>
                </article>
              );
            })}
          </div>
        </section>

        <section className="mt-16 grid gap-8 lg:grid-cols-[1fr_0.85fr]">
          <div>
            <h2 className="flex items-center gap-3 text-3xl font-extrabold text-white">
              <GraduationCap className="text-accent" size={30} /> Education
            </h2>
            <div className="mt-6 space-y-4">
              {education.map((item) => (
                <div key={item.degree} className="rounded-lg border border-white/10 bg-[#101827] p-5">
                  <div className="flex flex-wrap items-start justify-between gap-3">
                    <span className="font-bold text-white">{item.degree}</span>
                    <span className="rounded-full bg-white/5 px-3 py-1 text-xs font-semibold text-slate-400">{item.period}</span>
                  </div>
                  <p className="mt-2 text-sm leading-6 text-slate-400">{item.org}</p>
                </div>
              ))}
            </div>
          </div>
          <aside className="rounded-lg border border-white/10 bg-[#101827] p-6">
            <h2 className="text-2xl font-extrabold text-white">Kısa Profil</h2>
            <div className="mt-5 space-y-4 text-sm leading-7 text-slate-400">
              <p className="flex gap-3"><MapPin className="mt-1 shrink-0 text-accent" size={18} /> İstanbul merkezli, embedded sistemler ve teknik içerik üretimi odaklı kişisel çalışma alanı.</p>
              <p className="flex gap-3"><Camera className="mt-1 shrink-0 text-accent" size={18} /> Projeleri yalnızca kod olarak değil; görsel, not, deneyim ve sonuçlarıyla birlikte belgelemeyi önemsiyorum.</p>
              <p className="flex gap-3"><Gamepad2 className="mt-1 shrink-0 text-accent" size={18} /> Teknik üretimin dışında zihni dinlendiren yaratıcı ve keyifli uğraşlara da alan açıyorum.</p>
            </div>
          </aside>
        </section>

        <div className="mt-16 flex flex-wrap gap-4">
          <Link href="/projects" className="focus-ring inline-flex min-h-12 items-center rounded-md bg-accent px-7 text-sm font-bold text-[#07101f]">
            Projeleri Gör
          </Link>
          <Link href="/cv" className="focus-ring inline-flex min-h-12 items-center rounded-md border border-cyan-300/25 px-7 text-sm font-bold text-slate-100 hover:border-accent hover:text-accent">
            CV Görüntüle
          </Link>
          <Link href="/contact" className="focus-ring inline-flex min-h-12 items-center rounded-md border border-cyan-300/25 px-7 text-sm font-bold text-slate-100 hover:border-accent hover:text-accent">
            İletişime Geç
          </Link>
        </div>
      </Container>
    </div>
  );
}

function NarrativeSection({ title, children }: { title: string; children: React.ReactNode }) {
  return (
    <section className="grid gap-5 border-t border-white/10 pt-8 lg:grid-cols-[290px_minmax(0,1fr)]">
      <h2 className="text-2xl font-extrabold text-white">{title}</h2>
      <div className="space-y-5 text-base leading-8 text-slate-400">{children}</div>
    </section>
  );
}
