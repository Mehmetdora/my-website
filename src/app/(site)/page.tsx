import { HeroSection } from "@/components/home/HeroSection";
import { ExpertiseSection, FeaturedProjects, LatestPosts, TechStackSection } from "@/components/home/HomeSections";
import { getFeaturedProjects, getLatestPosts, getSiteSettings } from "@/lib/cms/queries";

export default async function HomePage() {
  const [settings, posts, projects] = await Promise.all([
    getSiteSettings(),
    getLatestPosts(3),
    getFeaturedProjects(3),
  ]);

  return (
    <>
      <HeroSection />
      <ExpertiseSection />
      <FeaturedProjects projects={projects} />
      <LatestPosts posts={posts} />
      <TechStackSection stack={settings.techStack} />
    </>
  );
}
