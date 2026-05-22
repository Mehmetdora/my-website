import type { ReactNode } from "react";
import { cn } from "@/lib/utils/cn";

const sizes = {
  sm: "max-w-3xl",
  default: "max-w-5xl",
  wide: "max-w-7xl",
};

export function Container({
  children,
  size = "default",
  className,
}: {
  children: ReactNode;
  size?: keyof typeof sizes;
  className?: string;
}) {
  return <div className={cn("mx-auto w-full px-4 sm:px-6 lg:px-8", sizes[size], className)}>{children}</div>;
}
