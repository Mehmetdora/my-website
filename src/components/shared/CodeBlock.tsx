"use client";

import { Check, Copy } from "lucide-react";
import { useState } from "react";

export function CodeBlock({ code, language, filename }: { code: string; language: string; filename?: string }) {
  const [copied, setCopied] = useState(false);

  async function copy() {
    await navigator.clipboard.writeText(code);
    setCopied(true);
    window.setTimeout(() => setCopied(false), 1400);
  }

  return (
    <div className="overflow-hidden rounded-lg border border-border bg-[#0b1220] text-slate-100">
      <div className="flex min-h-11 items-center justify-between border-b border-white/10 px-3">
        <div className="flex items-center gap-2 text-xs text-slate-300">
          <span className="rounded bg-white/10 px-2 py-1 font-mono">{language}</span>
          {filename ? <span className="font-mono">{filename}</span> : null}
        </div>
        <button type="button" onClick={copy} className="focus-ring inline-flex h-8 w-8 items-center justify-center rounded-md text-slate-300 hover:bg-white/10" aria-label="Kodu kopyala">
          {copied ? <Check size={16} /> : <Copy size={16} />}
        </button>
      </div>
      <pre className="overflow-x-auto p-4 text-sm leading-6">
        <code>{code}</code>
      </pre>
    </div>
  );
}
