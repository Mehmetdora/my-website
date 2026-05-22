import { ImageWithFallback } from "./ImageWithFallback";
import { CodeBlock } from "@/components/shared/CodeBlock";
import type { RichBlock } from "@/types/common";

export function RichTextRenderer({ content }: { content: RichBlock[] }) {
  return (
    <div className="prose-custom">
      {content.map((block, index) => {
        if (block.type === "heading") {
          const id = block.text.toLocaleLowerCase("tr-TR").replaceAll(" ", "-");
          if (block.level === 2) return <h2 id={id} key={index}>{block.text}</h2>;
          if (block.level === 3) return <h3 id={id} key={index}>{block.text}</h3>;
          return <h4 id={id} key={index}>{block.text}</h4>;
        }
        if (block.type === "paragraph") return <p key={index}>{block.text}</p>;
        if (block.type === "quote") return <blockquote key={index}>{block.text}</blockquote>;
        if (block.type === "list") {
          const List = block.ordered ? "ol" : "ul";
          return (
            <List key={index}>
              {block.items.map((item) => <li key={item}>{item}</li>)}
            </List>
          );
        }
        if (block.type === "code") return <CodeBlock key={index} code={block.code} language={block.language} filename={block.filename} />;
        if (block.type === "callout") {
          return (
            <aside key={index} className="not-prose rounded-lg border border-accent/30 bg-accent/10 p-4">
              {block.title ? <p className="font-semibold">{block.title}</p> : null}
              <p className="mt-1 text-sm leading-6 text-soft">{block.text}</p>
            </aside>
          );
        }
        if (block.type === "image") return <ImageWithFallback key={index} image={block.image} title={block.caption ?? block.image.alt ?? "İçerik görseli"} />;
        return null;
      })}
    </div>
  );
}
