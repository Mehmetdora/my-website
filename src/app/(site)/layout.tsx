import { SiteFooter } from "@/components/layout/SiteFooter";
import { SiteHeader } from "@/components/layout/SiteHeader";
import ChunkErrorHandler from "@/components/layout/ChunkErrorHandler";

export default function SiteLayout({ children }: { children: React.ReactNode }) {
  return (
    <div className="min-h-screen flex flex-col">
      <SiteHeader />
      <main className="flex-1">
        <ChunkErrorHandler />
        {children}
      </main>
      <SiteFooter />
    </div>
  );
}
