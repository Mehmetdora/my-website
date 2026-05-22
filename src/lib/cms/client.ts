const baseUrl = process.env.PAYLOAD_PUBLIC_API_URL;

export async function cmsFetch<T>(path: string, init?: RequestInit): Promise<T> {
  if (!baseUrl) {
    throw new Error("PAYLOAD_PUBLIC_API_URL is not configured. Mock queries are active.");
  }

  const response = await fetch(`${baseUrl}${path}`, {
    ...init,
    headers: {
      "Content-Type": "application/json",
      ...init?.headers,
    },
    next: { revalidate: 300 },
  });

  if (!response.ok) {
    throw new Error("CMS request failed");
  }

  return response.json() as Promise<T>;
}
