import { NextResponse } from "next/server";
import { cookies } from "next/headers";
import { timingSafeEqual } from "node:crypto";

function safeEqual(a: string, b: string) {
  const left = Buffer.from(a);
  const right = Buffer.from(b);
  return left.length === right.length && timingSafeEqual(left, right);
}

export async function POST(request: Request) {
  const { username = "", password = "" } = await request.json().catch(() => ({}));
  const expectedUser = process.env.ADMIN_USERNAME;
  const expectedPassword = process.env.ADMIN_PASSWORD;
  const sessionToken = process.env.ADMIN_SESSION_TOKEN;

  if (!expectedUser || !expectedPassword || !sessionToken) {
    return NextResponse.json(
      { message: "تنظیمات ورود ادمین کامل نیست." },
      { status: 500 }
    );
  }

  if (
    !safeEqual(String(username), expectedUser) ||
    !safeEqual(String(password), expectedPassword)
  ) {
    return NextResponse.json(
      { message: "نام کاربری یا رمز عبور اشتباه است." },
      { status: 401 }
    );
  }

  const store = await cookies();
  store.set("talarto_admin_session", sessionToken, {
    httpOnly: true,
    sameSite: "lax",
    secure: process.env.ADMIN_COOKIE_SECURE === "true",
    path: "/",
    maxAge: 60 * 60 * 8,
  });

  return NextResponse.json({ ok: true });
}
