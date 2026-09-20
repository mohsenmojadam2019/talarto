import AdminSection from "@/components/AdminSection";

export default async function AdminSectionPage({params}:{params:Promise<{section:string}>}) {
  const {section}=await params;
  return <AdminSection section={section}/>;
}
