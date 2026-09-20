import AdminShell from "@/components/AdminShell";

export const metadata = { title: "پنل مدیریت تالار" };

export default function AdminLayout({children}:{children:React.ReactNode}) {
  return <AdminShell>{children}</AdminShell>;
}
