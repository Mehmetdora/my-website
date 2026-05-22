import type { ReactNode } from "react";
import { cn } from "@/lib/utils/cn";

export function Badge({ children, className }: { children: ReactNode; className?: string }) {
  return (
    <span className={cn("inline-flex items-center rounded-md border border-border bg-muted px-2 py-1 text-xs font-medium text-soft", className)}>
      {children}
    </span>
  );
}
