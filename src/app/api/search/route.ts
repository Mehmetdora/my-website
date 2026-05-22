import { NextResponse } from "next/server";
import { searchContent } from "@/lib/cms/queries";

export async function GET(request: Request) {
  const url = new URL(request.url);
  const q = url.searchParams.get("q") ?? "";
  const type = url.searchParams.get("type") ?? undefined;
  const results = await searchContent(q, type);
  return NextResponse.json({ results });
}
