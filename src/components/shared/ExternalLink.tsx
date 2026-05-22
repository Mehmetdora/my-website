import type { ReactNode } from "react";

export function ExternalLink({ href, children, label }: { href: string; children: ReactNode; label?: string }) {
  return (
    <a
      href={href}
      aria-label={label}
      target="_blank"
      rel="noopener noreferrer"
      className="focus-ring inline-flex min-h-10 items-center justify-center gap-2 rounded-md border border-border bg-panel px-3 text-sm font-medium transition hover:border-accent hover:text-accent"
    >
      {children}
    </a>
  );
}
