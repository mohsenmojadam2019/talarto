"use client";

import { FormEvent, useState } from "react";
import { useRouter } from "next/navigation";
import { Eye, EyeOff, LockKeyhole, LogIn, UserRound } from "lucide-react";
import BrandIcon from "./BrandIcon";

export default function AdminLoginForm({ nextPath = "/admin" }: { nextPath?: string }) {
  const router = useRouter();
  const [username, setUsername] = useState("");
  const [password, setPassword] = useState("");
  const [show, setShow] = useState(false);
  const [error, setError] = useState("");
  const [loading, setLoading] = useState(false);

  const submit = async (event: FormEvent) => {
    event.preventDefault();
    setError("");
    setLoading(true);
    try {
      const response = await fetch("/api/admin/login", {
        method: "POST",
        headers: { "content-type": "application/json" },
        body: JSON.stringify({ username, password }),
      });
      const data = await response.json();
      if (!response.ok) {
        setError(data.message || "ورود انجام نشد.");
        return;
      }
      router.replace(nextPath.startsWith("/admin") ? nextPath : "/admin");
      router.refresh();
    } catch {
      setError("ارتباط با سرور برقرار نشد.");
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="grid min-h-screen place-items-center bg-[#fbf8f2] px-4 py-10">
      <div className="w-full max-w-md overflow-hidden rounded-[30px] border border-[#eadfce] bg-white shadow-[0_30px_90px_rgba(23,34,56,.1)]">
        <div className="border-b border-[#eee4d5] bg-[#fffdfa] px-7 py-7 text-center">
          <BrandIcon className="mx-auto h-16 w-16 text-[#b88334]"/>
          <div className="mt-3 text-2xl font-black text-[#172238]">talarto</div>
          <div className="mt-1 text-xs text-slate-500">ورود به پنل مدیریت تالار</div>
        </div>

        <form onSubmit={submit} className="space-y-5 p-7">
          <label className="block text-xs font-bold text-slate-600">
            نام کاربری
            <div className="relative mt-2">
              <UserRound size={18} className="absolute right-3 top-3.5 text-slate-400"/>
              <input
                autoComplete="username"
                value={username}
                onChange={(e)=>setUsername(e.target.value)}
                className="w-full rounded-xl border border-slate-200 py-3 pl-3 pr-10 outline-none transition focus:border-[#b88334]"
                placeholder="نام کاربری"
                dir="ltr"
              />
            </div>
          </label>

          <label className="block text-xs font-bold text-slate-600">
            رمز عبور
            <div className="relative mt-2">
              <LockKeyhole size={18} className="absolute right-3 top-3.5 text-slate-400"/>
              <input
                autoComplete="current-password"
                type={show ? "text" : "password"}
                value={password}
                onChange={(e)=>setPassword(e.target.value)}
                className="w-full rounded-xl border border-slate-200 py-3 pl-11 pr-10 outline-none transition focus:border-[#b88334]"
                placeholder="رمز عبور"
                dir="ltr"
              />
              <button type="button" onClick={()=>setShow(x=>!x)} className="absolute left-3 top-3 rounded-lg p-1 text-slate-400 hover:text-[#172238]" aria-label={show ? "پنهان کردن رمز" : "نمایش رمز"}>
                {show ? <EyeOff size={17}/> : <Eye size={17}/>}
              </button>
            </div>
          </label>

          {error && <div className="rounded-xl border border-red-200 bg-red-50 px-3 py-2.5 text-xs font-bold text-red-700">{error}</div>}

          <button disabled={loading || !username || !password} className="btn-gold w-full disabled:cursor-not-allowed disabled:opacity-50">
            <LogIn size={18}/>{loading ? "در حال ورود..." : "ورود به پنل مدیریت"}
          </button>

          <p className="text-center text-[10px] leading-6 text-slate-400">نشست مدیریت پس از ۸ ساعت منقضی می‌شود.</p>
        </form>
      </div>
    </div>
  );
}
