import { NextRequest, NextResponse } from "next/server";

export function proxy(request: NextRequest) {
  const expected = process.env.ADMIN_SESSION_TOKEN;
  const session = request.cookies.get("talarto_admin_session")?.value;

  if (!expected || session !== expected) {
    const loginUrl = new URL("/admin-login", request.url);
    loginUrl.searchParams.set("next", request.nextUrl.pathname + request.nextUrl.search);
    return NextResponse.redirect(loginUrl);
  }

  return NextResponse.next();
}

export const config = {
  matcher: ["/admin/:path*"],
};
