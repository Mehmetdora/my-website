import Link from "next/link";

export function Pagination({ currentPage, totalPages, basePath }: { currentPage: number; totalPages: number; basePath: string }) {
  if (totalPages <= 1) return null;
  return (
    <nav className="mt-10 flex items-center justify-center gap-2" aria-label="Sayfalama">
      {Array.from({ length: totalPages }, (_, index) => index + 1).map((page) => (
        <Link
          key={page}
          href={page === 1 ? basePath : `${basePath}?page=${page}`}
          className={`focus-ring inline-flex h-10 w-10 items-center justify-center rounded-md border text-sm font-medium ${page === currentPage ? "border-accent bg-accent text-white" : "border-border bg-panel hover:border-accent"}`}
        >
          {page}
        </Link>
      ))}
    </nav>
  );
}
