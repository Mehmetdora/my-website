import { Container } from "@/components/layout/Container";

export default function Loading() {
  return (
    <Container className="py-16">
      <div className="h-8 w-48 animate-pulse rounded bg-muted" />
      <div className="mt-8 grid gap-4 md:grid-cols-3">
        {[0, 1, 2].map((item) => <div key={item} className="h-64 animate-pulse rounded-lg bg-muted" />)}
      </div>
    </Container>
  );
}
