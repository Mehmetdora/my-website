import Link from "next/link";
import { siteConfig } from "@/config/site";
import { Container } from "./Container";
import { MobileNav } from "./MobileNav";

export function SiteHeader() {
  return (
    <header className="sticky top-0 z-40 border-b border-white/10 bg-[#0a0f1e]/95 backdrop-blur">
      <Container size="wide" className="flex min-h-20 items-center justify-between gap-4">
        <Link href="/" className="focus-ring flex items-center gap-3 rounded-md">
          <span className="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-accent text-sm font-black text-[#07101f]">
            {siteConfig.initials}
          </span>
          <span className="hidden text-base font-semibold text-white sm:inline">{siteConfig.name}</span>
        </Link>
        <nav className="hidden items-center gap-1 lg:flex">
          {siteConfig.nav.map((item) => (
            item.special ? (
              <div key={item.href} className="ml-3 flex items-center gap-4">
                <span className="h-8 w-px bg-white/15" aria-hidden="true" />
                <SpecialNavLink item={item} />
              </div>
            ) : (
            <Link
              key={item.href}
              href={item.href}
              className="focus-ring rounded-md px-3 py-2 text-sm font-semibold text-slate-300 transition hover:bg-white/5 hover:text-accent"
            >
              {item.title}
            </Link>
            )
          ))}
        </nav>
        <MobileNav />
      </Container>
    </header>
  );
}

function SpecialNavLink({ item }: { item: { title: string; href: string } }) {
  return (
    <Link
      href={item.href}
      className="focus-ring relative h-[34px] min-w-[95px] overflow-hidden rounded-full border border-[#6FD1D7]/65 bg-[#3B7597] px-3.5 py-2 text-[11px] font-black text-white shadow-[inset_0_3px_5px_rgba(255,255,255,0.42),inset_0_-6px_10px_rgba(9,60,93,0.38),0_14px_28px_-23px_rgb(9_60_93)] transition hover:translate-y-[-1px] hover:border-[#5DF8D8] hover:shadow-[inset_0_3px_5px_rgba(255,255,255,0.48),inset_0_-6px_10px_rgba(9,60,93,0.34),0_16px_32px_-24px_rgb(93_248_216)]"
    >
      <BeachScene />
      <span className="relative z-20 drop-shadow-[0_1px_2px_rgba(9,60,93,0.8)]">{item.title}</span>
    </Link>
  );
}

function BeachScene() {
  return (
    <>
      <span className="absolute inset-0 bg-[linear-gradient(180deg,#6FD1D7_0%,#5DF8D8_38%,#3B7597_39%,#093C5D_100%)]" />
      <span className="absolute left-2 right-6 top-1.5 z-20 h-1.5 rounded-full bg-gradient-to-b from-white/70 to-white/0" />
      <span className="absolute -right-2 -top-2 z-10 h-8 w-8 rounded-full bg-[radial-gradient(circle_at_35%_35%,#fff8b8_0%,#ffd84d_42%,#f2a900_100%)] shadow-[0_0_14px_rgba(255,216,77,0.95),0_0_32px_rgba(255,216,77,0.48)]" />
      <span className="absolute -right-7 -top-7 z-[9] h-[67px] w-[67px] rounded-full bg-[conic-gradient(from_200deg,rgba(255,232,115,0.56),transparent_10deg,rgba(255,232,115,0.38)_20deg,transparent_34deg,rgba(255,232,115,0.42)_48deg,transparent_64deg,rgba(255,232,115,0.32)_78deg,transparent_96deg)]" />
      <span className="absolute right-2 top-1 z-[11] h-[3px] w-3.5 rotate-45 rounded-full bg-yellow-100/75" />
      <span className="absolute right-6 top-4 z-[11] h-[3px] w-3.5 -rotate-12 rounded-full bg-yellow-100/65" />
      <span className="absolute right-1.5 top-6 z-[11] h-[3px] w-3 -rotate-45 rounded-full bg-yellow-100/60" />
      <span className="absolute left-1.5 top-3 z-[5] h-2 w-8 rounded-full bg-white/55 before:absolute before:left-1.5 before:top-[-5px] before:h-3 before:w-3 before:rounded-full before:bg-white/55 after:absolute after:right-1.5 after:top-[-4px] after:h-2.5 after:w-2.5 after:rounded-full after:bg-white/55" />
      <span className="absolute bottom-4 left-0 z-[3] h-3.5 w-14 bg-[linear-gradient(90deg,#093C5D,#3B7597,#6FD1D7)] opacity-70 [clip-path:polygon(0_82%,15%_58%,31%_73%,48%_48%,65%_70%,82%_56%,100%_78%,100%_100%,0_100%)]" />
      <span className="absolute bottom-0 left-0 z-[4] h-[18px] w-full bg-[repeating-linear-gradient(170deg,rgba(255,255,255,0.22)_0_2px,transparent_2px_10px),linear-gradient(100deg,#093C5D_0%,#3B7597_38%,#6FD1D7_76%,#5DF8D8_100%)]" />
      <span className="absolute bottom-[11px] left-0 z-[5] h-px w-full bg-white/22" />
      <span className="absolute -bottom-2 right-[-10px] z-[6] h-6 w-16 rounded-tl-[78%] bg-[radial-gradient(circle_at_72%_72%,rgba(163,112,45,0.24)_0_1px,transparent_2px),linear-gradient(135deg,#fff1bd_0%,#f3d28b_52%,#d8a85c_100%)]" />
      <span className="absolute bottom-[13px] right-6 z-[7] h-[3px] w-[50px] -rotate-[8deg] rounded-full bg-white/90 before:absolute before:-left-5 before:top-[2px] before:h-[3px] before:w-8 before:rotate-[7deg] before:rounded-full before:bg-white/90" />
      <span className="absolute bottom-5 right-1.5 z-[7] h-[2px] w-8 -rotate-[6deg] rounded-full bg-white/80" />
      <span className="absolute bottom-1.5 left-3.5 z-[6] h-[2px] w-7 rounded-full bg-white/30" />
    </>
  );
}
