/* ── Categorías ─────────────────────────────────────────────────────── */
var CATEGORIES = [
  {
    dbId: 1,
    id: "rapidas",
    label: "Comidas Rapidas",
    icon: "🍔",
    bg: "#fff8e1",
    accent: "#f59e0b",
    coverImg:
      "https://images.unsplash.com/photo-1550547660-d9450f859349?w=800&h=400&fit=crop&auto=format",
    description:
      "Burgers, tacos y wraps plant-based para cuando el tiempo apremia.",
  },
  {
    dbId: 2,
    id: "cenas",
    label: "Cenas",
    icon: "🍝",
    bg: "#ede9fe",
    accent: "#7c3aed",
    coverImg:
      "https://images.unsplash.com/photo-1473093226795-af9932fe5856?w=800&h=400&fit=crop&auto=format",
    description:
      "Platos elaborados para una cena especial o en familia.",
  },
  {
    dbId: 3,
    id: "bowls",
    label: "Bowls & Ensaladas",
    icon: "🥗",
    bg: "#dcfce7",
    accent: "#16a34a",
    coverImg:
      "https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=800&h=400&fit=crop&auto=format",
    description:
      "Bowls frescos, coloridos y cargados de nutrientes.",
  },
  {
    dbId: 4,
    id: "postres",
    label: "Postres",
    icon: "🍰",
    bg: "#fce7f3",
    accent: "#db2777",
    coverImg:
      "https://images.unsplash.com/photo-1606313564200-e75d5e30476c?w=800&h=400&fit=crop&auto=format",
    description:
      "Dulces sin culpa: 100% veganos, sin lácteos ni huevos.",
  },
  {
    dbId: 5,
    id: "bebidas",
    label: "Bebidas",
    icon: "🥤",
    bg: "#dbeafe",
    accent: "#2563eb",
    coverImg:
      "https://images.unsplash.com/photo-1622597467836-f3285f2131b8?w=800&h=400&fit=crop&auto=format",
    description:
      "Jugos naturales, smoothies y bebidas frías para hidratarte.",
  },
  {
    dbId: 6,
    id: "desayunos",
    label: "Desayunos",
    icon: "🌅",
    bg: "#fef9c3",
    accent: "#ca8a04",
    coverImg: "assets/image-6.png",
    description:
      "Empieza el día con energía: tostadas, granola y mucho más.",
  },
  {
    dbId: 7,
    id: "snacks",
    label: "Snacks",
    icon: "🍿",
    bg: "#f0fdf4",
    accent: "#15803d",
    coverImg:
      "https://images.unsplash.com/photo-1552332386-f8dd00dc2f85?w=800&h=400&fit=crop&auto=format",
    description:
      "Para el antojo del momento: chips, dips y snacks saludables.",
  },
];

/* ── Categorías de filtro promos ────────────────────────────────────── */
var PROMO_CATS = [
  "Todos",
  "Comidas Rapidas",
  "Cenas",
  "Bowls & Ensaladas",
  "Postres",
  "Bebidas",
  "Desayunos",
  "Snacks",
];

/* ── Promociones ────────────────────────────────────────────────────── */
var ALL_PROMOS = [];
