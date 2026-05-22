"use client";

export default function Error({ reset }: { reset: () => void }) {
  return (
    <main className="mx-auto max-w-3xl px-6 py-24 text-center">
      <h1 className="text-3xl font-semibold">Bir şeyler ters gitti</h1>
      <p className="mt-3 text-slate-500">Teknik detayları göstermeden güvenli bir hata ekranı sunuyoruz.</p>
      <button type="button" onClick={reset} className="mt-8 rounded-md bg-cyan-700 px-5 py-3 text-sm font-semibold text-white">
        Tekrar dene
      </button>
    </main>
  );
}
