"use client";

import Link from "next/link";
import { Menu, X } from "lucide-react";
import { useState } from "react";
import { siteConfig } from "@/config/site";

export function MobileNav() {
  const [open, setOpen] = useState(false);

  return (
    <div className="lg:hidden">
      <button
        type="button"
        aria-label={open ? "Menüyü kapat" : "Menüyü aç"}
        onClick={() => setOpen((value) => !value)}
        className="focus-ring inline-flex h-11 w-11 items-center justify-center rounded-md border border-white/10 bg-white/5 text-white"
      >
        {open ? <X size={19} /> : <Menu size={19} />}
      </button>
      {open ? (
        <div className="absolute inset-x-4 top-20 z-50 rounded-lg border border-white/10 bg-[#101827] p-4 shadow-soft">
          <nav className="grid gap-2">
            {siteConfig.nav.filter((item) => !item.special).map((item) => (
              <Link
                key={item.href}
                onClick={() => setOpen(false)}
                className="rounded-md px-3 py-2 font-medium text-slate-200 hover:bg-white/5 hover:text-accent"
                href={item.href}
              >
                {item.title}
              </Link>
            ))}
            {siteConfig.nav.filter((item) => item.special).map((item) => (
              <div key={item.href} className="mt-2 border-t border-white/10 pt-3">
                <Link
                  onClick={() => setOpen(false)}
                  className="relative flex min-h-12 items-center overflow-hidden rounded-md border border-[#6FD1D7]/65 bg-[#3B7597] px-3 py-2 font-black text-white shadow-[inset_0_4px_6px_rgba(255,255,255,0.38),inset_0_-8px_14px_rgba(9,60,93,0.36)]"
                  href={item.href}
                >
                  <MobileBeachScene />
                  <span className="relative z-20 drop-shadow-[0_1px_2px_rgba(9,60,93,0.8)]">{item.title}</span>
                </Link>
              </div>
            ))}
            <Link onClick={() => setOpen(false)} className="rounded-md px-3 py-2 font-medium text-slate-200 hover:bg-white/5 hover:text-accent" href="/search">
              Arama
            </Link>
          </nav>
        </div>
      ) : null}
    </div>
  );
}

function MobileBeachScene() {
  return (
    <>
      <span className="absolute inset-0 bg-[linear-gradient(180deg,#6FD1D7_0%,#5DF8D8_38%,#3B7597_39%,#093C5D_100%)]" />
      <span className="absolute left-5 top-2 z-20 h-2 w-24 rounded-full bg-gradient-to-b from-white/60 to-white/0" />
      <span className="absolute right-2 top-1 z-10 h-9 w-9 rounded-full bg-[radial-gradient(circle_at_35%_35%,#fff8b8_0%,#ffd84d_42%,#f2a900_100%)] shadow-[0_0_17px_rgba(255,216,77,0.92),0_0_36px_rgba(255,216,77,0.42)]" />
      <span className="absolute -right-6 -top-7 z-[9] h-20 w-20 rounded-full bg-[conic-gradient(from_200deg,rgba(255,232,115,0.52),transparent_12deg,rgba(255,232,115,0.34)_24deg,transparent_42deg,rgba(255,232,115,0.38)_58deg,transparent_76deg)]" />
      <span className="absolute right-6 top-1 z-[11] h-1 w-5 rotate-45 rounded-full bg-yellow-100/70" />
      <span className="absolute left-4 top-5 z-[5] h-2.5 w-12 rounded-full bg-white/50 before:absolute before:left-2 before:top-[-6px] before:h-4 before:w-4 before:rounded-full before:bg-white/50 after:absolute after:right-2 after:top-[-5px] after:h-3.5 after:w-3.5 after:rounded-full after:bg-white/50" />
      <span className="absolute bottom-0 left-0 z-[4] h-[21px] w-full bg-[repeating-linear-gradient(170deg,rgba(255,255,255,0.22)_0_2px,transparent_2px_13px),linear-gradient(100deg,#093C5D_0%,#3B7597_42%,#6FD1D7_78%,#5DF8D8_100%)]" />
      <span className="absolute -bottom-3 right-[-6px] z-[6] h-8 w-28 rounded-tl-[70%] bg-[linear-gradient(135deg,#fff1bd_0%,#f3d28b_54%,#d8a85c_100%)]" />
      <span className="absolute bottom-[19px] right-12 z-[7] h-[4px] w-24 -rotate-[8deg] rounded-full bg-white/90" />
      <span className="absolute bottom-[29px] right-4 z-[7] h-[3px] w-14 -rotate-[6deg] rounded-full bg-white/80" />
    </>
  );
}
