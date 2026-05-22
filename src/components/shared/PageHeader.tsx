import { Container } from "@/components/layout/Container";

export function PageHeader({ title, description }: { title: string; description?: string }) {
  return (
    <Container className="py-12 sm:py-16">
      <h1 className="max-w-3xl text-4xl font-semibold tracking-normal sm:text-5xl">{title}</h1>
      {description ? <p className="mt-4 max-w-2xl text-lg leading-8 text-soft">{description}</p> : null}
    </Container>
  );
}
