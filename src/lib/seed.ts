export type GallerySeedItem = {
  id: string;
  title: string;
  src: string;
  category: "hall" | "ceremony" | "detail" | "garden" | "food";
  alt: string;
};

export const gallerySeed: GallerySeedItem[] = [
  {
    id: "hall-main",
    title: "سالن اصلی",
    src: "/images/seed/gallery-hall.webp",
    category: "hall",
    alt: "سالن اصلی تالارتو با چیدمان عروسی",
  },
  {
    id: "ceremony",
    title: "جایگاه مراسم",
    src: "/images/seed/gallery-ceremony.webp",
    category: "ceremony",
    alt: "جایگاه عقد و مراسم تالارتو",
  },
  {
    id: "rings",
    title: "جزئیات عاشقانه",
    src: "/images/seed/gallery-rings.webp",
    category: "detail",
    alt: "حلقه‌های ازدواج در دکور مراسم",
  },
  {
    id: "garden",
    title: "باغ و فضای باز",
    src: "/images/seed/gallery-garden.webp",
    category: "garden",
    alt: "فضای باغ تالارتو در شب",
  },
  {
    id: "floral",
    title: "گل‌آرایی",
    src: "/images/seed/gallery-floral.webp",
    category: "detail",
    alt: "گل‌آرایی سفید و طلایی مراسم",
  },
  {
    id: "food",
    title: "پذیرایی",
    src: "/images/seed/gallery-food.webp",
    category: "food",
    alt: "پذیرایی و غذای مراسم",
  },
];

export const serviceImageSeed: Record<string, string> = {
  food: "/images/seed/gallery-food.webp",
  drinks: "/images/seed/service-drinks.webp",
  music: "/images/seed/service-dj.webp",
  floral: "/images/seed/gallery-floral.webp",
  photo: "/images/seed/gallery-rings.webp",
  effects: "/images/seed/gallery-ceremony.webp",
  parking: "/images/seed/service-parking.webp",
  catering: "/images/seed/service-dessert.webp",
  management: "/images/seed/service-vip.webp",
};

export const heroSeed = {
  src: "/images/seed/hero.webp",
  alt: "عروس و داماد در فضای لوکس تالارتو",
};
