import AdminLoginForm from "@/components/AdminLoginForm";

export const metadata = { title: "ورود مدیر" };

export default async function AdminLoginPage({
  searchParams,
}: {
  searchParams: Promise<{ next?: string }>;
}) {
  const params = await searchParams;
  return <AdminLoginForm nextPath={params.next || "/admin"}/>;
}
