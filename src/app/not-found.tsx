import Link from "next/link";
import { Container } from "@/components/layout/Container";

export default function NotFound() {
  return (
    <Container className="py-24 text-center">
      <p className="font-mono text-sm text-accent">404</p>
      <h1 className="mt-3 text-4xl font-semibold">Aradığın içerik bulunamadı</h1>
      <p className="mx-auto mt-4 max-w-xl text-soft">Bu içerik kaldırılmış, henüz yayınlanmamış veya private olabilir.</p>
      <Link href="/" className="focus-ring mt-8 inline-flex min-h-11 items-center rounded-md bg-accent px-5 text-sm font-semibold text-white">
        Ana sayfaya dön
      </Link>
    </Container>
  );
}
