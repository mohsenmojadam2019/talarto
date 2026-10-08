# Talarto wedding design — implemented October 2026

This release recreates the structure of the user's two visual references with real Blade/CSS instead of static screenshots.

- Front page: full-width photographic hero, floating navigation, six-feature strip, about/story, horizontal image gallery, trust strip, testimonials, visit CTA and cream/gold footer with Zafaraniyeh map and redcoweb.ir credit.
- Admin: right-side branded sidebar, actual booking/deposit statistics, dynamic Jalali calendar, 9-month data chart, upcoming ceremonies, messages and reservations. Existing editable admin CRUD forms and role-protected customer portal remain accessible.
- Customer portal remains at /account and users sign in at /login.
- Homepage packages and pricing cards remain deliberately absent (the booking engine and package pricing are preserved internally).
- Photographic imagery: locally hosted AI-generated demonstration assets adapted from github.com/aliirsyaadn/wedding-website (MIT-labelled template). They are *concept art*, not verified photos of the real venue. Replace through admin gallery before public launch.
- Original AI images created earlier in ChatGPT are not uploaded automatically to this host. These varied local photos replace the repeated abstract placeholder artwork. Branding icon in PNG remains a locally rendered placeholder until the final generated emblem is transferred.
- Legal address shown only as Tehran, Zafaraniyeh; no fabricated street address or exact map pin.
- Dashboard never hardcodes fictitious reservations or financial metrics.
- Public CDN blocked on host: all image cards are locally served under public/assets/venue/.
- Sanity checks: Docker healthcheck, authenticated admin Chrome visit, page and asset HTTP 200, Laravel tests.
