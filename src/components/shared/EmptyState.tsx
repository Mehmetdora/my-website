export function EmptyState({ title = "Henüz içerik yok", description = "Bu bölüm için yakında içerik eklenecek." }: { title?: string; description?: string }) {
  return (
    <div className="rounded-lg border border-dashed border-border bg-panel p-8 text-center">
      <p className="text-lg font-semibold">{title}</p>
      <p className="mt-2 text-sm text-soft">{description}</p>
    </div>
  );
}
