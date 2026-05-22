export const siteConfig = {
  name: "Mehmet Dora",
  initials: "MD",
  role: "Embedded Systems & Software Developer",
  location: "Istanbul, Turkey",
  title: "Mehmet Dora | Embedded Systems & Personal Notes",
  description:
    "Embedded systems, low-level programming, projeler, teknik yazılar ve kişisel notlar.",
  url: process.env.NEXT_PUBLIC_SITE_URL ?? "http://localhost:3000",
  profileImage: "/profile.svg",
  links: {
    github: "https://github.com/Mehmetdora",
    linkedin: "https://linkedin.com/",
    telegram: "https://t.me/",
    medium: "https://medium.com/",
    email: "mailto:mehmetdora333@gmail.com",
    cv: "/cv",
  },
  nav: [
    { title: "Home", href: "/" },
    { title: "About", href: "/about" },
    { title: "Projects", href: "/projects" },
    { title: "Blog", href: "/blog" },
    { title: "Resume", href: "/cv" },
    { title: "Contact", href: "/contact" },
    { title: "My Life", href: "/life", special: true },
  ],
};
