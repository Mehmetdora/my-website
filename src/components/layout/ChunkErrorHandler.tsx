"use client";

import { useEffect } from "react";

export default function ChunkErrorHandler() {
  useEffect(() => {
    function onError(e: ErrorEvent) {
      const message = e?.message ?? "";
      const isChunkError = message.includes("Loading chunk") || (e?.error && (e.error.name === "ChunkLoadError" || String(e.error).includes("Loading chunk")));
      if (isChunkError) {
        // Try a hard reload to recover from stale chunks
        console.warn("Chunk load error detected, reloading page to recover.");
        window.location.reload();
      }
    }

    function onRejection(event: PromiseRejectionEvent) {
      const reason = event?.reason ?? "";
      if (typeof reason === "string" && reason.includes("Loading chunk")) {
        console.warn("Chunk load rejection detected, reloading page to recover.");
        window.location.reload();
      }
    }

    window.addEventListener("error", onError);
    window.addEventListener("unhandledrejection", onRejection);

    return () => {
      window.removeEventListener("error", onError);
      window.removeEventListener("unhandledrejection", onRejection);
    };
  }, []);

  return null;
}
