import { useState } from "react";
import { Link } from "react-router-dom";
import { useCart } from "../contexts/CartContext";
import { useAuth } from "../contexts/AuthContext"; // [FIX] tambah — untuk cek auth di cart indicator

// ── Data ───────────────────────────────────────────────────────────────────
const CATEGORIES = ["All", "Serum", "Moisturiser", "Sunscreen", "Treatment", "Cleanser"];

const PRODUCTS = [
  {
    id: 1,
    name: "Brightening Vitamin C Serum",
    category: "Serum",
    price: 385000,
    originalPrice: 450000,
    rating: 4.9,
    reviews: 128,
    tag: "Best Seller",
    icon: "◈",
    desc: "High-potency 20% Vitamin C with niacinamide for visible brightening in 2 weeks.",
    color: "#f7ede2",
  },
  {
    id: 2,
    name: "Barrier Repair Moisturiser",
    category: "Moisturiser",
    price: 295000,
    originalPrice: null,
    rating: 4.8,
    reviews: 94,
    tag: "Doctor's Pick",
    icon: "◉",
    desc: "Ceramide-rich formula that restores skin barrier and locks in moisture all day.",
    color: "#fdf6ef",
  },
  {
    id: 3,
    name: "Invisible SPF 50+ Sunscreen",
    category: "Sunscreen",
    price: 245000,
    originalPrice: 280000,
    rating: 4.9,
    reviews: 213,
    tag: "Most Popular",
    icon: "✦",
    desc: "Lightweight, non-greasy broad-spectrum UVA/UVB protection. Zero white cast.",
    color: "#f7ede2",
  },
  {
    id: 4,
    name: "Retinol Night Treatment",
    category: "Treatment",
    price: 520000,
    originalPrice: null,
    rating: 4.7,
    reviews: 67,
    tag: "Premium",
    icon: "◇",
    desc: "0.3% encapsulated retinol with bakuchiol for visible anti-aging results.",
    color: "#fdf6ef",
  },
  {
    id: 5,
    name: "Gentle Foam Cleanser",
    category: "Cleanser",
    price: 175000,
    originalPrice: null,
    rating: 4.8,
    reviews: 156,
    tag: "Daily Essential",
    icon: "◈",
    desc: "pH-balanced foaming cleanser that removes impurities without stripping skin.",
    color: "#f7ede2",
  },
  {
    id: 6,
    name: "Hyaluronic Acid Booster",
    category: "Serum",
    price: 310000,
    originalPrice: 360000,
    rating: 4.9,
    reviews: 189,
    tag: "Hydration Hero",
    icon: "◉",
    desc: "Multi-weight HA complex with 3 molecular sizes for deep and surface hydration.",
    color: "#fdf6ef",
  },
  {
    id: 7,
    name: "AHA BHA Exfoliating Toner",
    category: "Treatment",
    price: 265000,
    originalPrice: null,
    rating: 4.6,
    reviews: 82,
    tag: "Clinic Formula",
    icon: "✦",
    desc: "10% AHA + 2% BHA blend for smooth texture, unclogged pores and even tone.",
    color: "#f7ede2",
  },
  {
    id: 8,
    name: "Calming Centella Cream",
    category: "Moisturiser",
    price: 230000,
    originalPrice: 260000,
    rating: 4.8,
    reviews: 110,
    tag: "Sensitive Skin",
    icon: "◇",
    desc: "Cica-powered formula that soothes redness and strengthens compromised skin.",
    color: "#fdf6ef",
  },
];

const fmt = (n) =>
  new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR", maximumFractionDigits: 0 }).format(n);

// ── Component ──────────────────────────────────────────────────────────────
export default function ProductsPage() {
  const [activeCategory, setActiveCategory] = useState("All");
  const [added, setAdded] = useState(null);

  const { cartItems, addToCart, totalItems } = useCart();
  const { isAuthenticated } = useAuth(); // [FIX] untuk cart indicator auth-aware

  const filtered =
    activeCategory === "All"
      ? PRODUCTS
      : PRODUCTS.filter((p) => p.category === activeCategory);

  // [FIX] BUG 1 — Hapus guard `if (!alreadyInCart)`.
  //       Guard itu yang menyebabkan user hanya bisa add 1x per produk.
  //       CartContext.addToCart() sudah handle dua kasus secara internal:
  //         - Produk belum ada → push item baru dengan qty: 1
  //         - Produk sudah ada → increment qty: item.qty + 1
  //       Jadi cukup panggil addToCart() langsung, tidak perlu cek manual.
  const handleAdd = (product) => {
    addToCart({ id: product.id, name: product.name, price: product.price }, 1);
    setAdded(product.id);
    setTimeout(() => setAdded(null), 1200);
  };

  // [FIX] BUG 3 — Helper ambil qty produk di cart (bukan boolean isInCart).
  //       Dipakai untuk tampilkan "x in cart" di button agar user tahu
  //       berapa qty yang sudah ditambah, dan tombol tetap bisa diklik lagi.
  const getQtyInCart = (productId) =>
    cartItems.find((item) => item.id === productId)?.qty ?? 0;

  return (
    <div className="min-h-screen font-sans" style={{ background: "#faf8f5", color: "#2c1f1a" }}>

      {/* ── HEADER ── */}
      <section
        className="pt-24 pb-16 px-6 text-center"
        style={{ background: "linear-gradient(135deg, #fdf6ef 0%, #f7ede2 100%)" }}
      >
        <div className="max-w-2xl mx-auto">
          <p className="text-xs tracking-widest uppercase mb-4" style={{ color: "#b87c5a" }}>
            ✦ Clinic-Grade Skincare
          </p>
          <h1
            className="text-5xl lg:text-6xl font-normal mb-5"
            style={{ fontFamily: "'Playfair Display', Georgia, serif", color: "#2c1f1a" }}
          >
            Our Products
          </h1>
          <p className="text-sm leading-relaxed max-w-md mx-auto" style={{ color: "#6b4c40" }}>
            Formulated with dermatologist expertise. Every product is tested and recommended by our clinic doctors for real, visible results.
          </p>
          <div className="mt-8">
            <Link
              to="/"
              className="inline-flex items-center gap-2 px-6 py-2.5 rounded-full text-xs tracking-wide transition-all duration-200 hover:opacity-80"
              style={{ color: "#5a3e35", border: "1px solid rgba(90,62,53,0.25)" }}
            >
              ← Back to Home
            </Link>
          </div>
        </div>
      </section>

      {/* ── CATEGORY FILTER ── */}
      <div
        className="sticky top-0 z-30 py-4 px-6"
        style={{
          background: "rgba(250,248,245,0.95)",
          backdropFilter: "blur(12px)",
          borderBottom: "1px solid rgba(184,124,90,0.1)",
        }}
      >
        <div
          className="max-w-6xl mx-auto flex gap-2 overflow-x-auto pb-1"
          style={{ scrollbarWidth: "none" }}
        >
          {CATEGORIES.map((cat) => (
            <button
              key={cat}
              onClick={() => setActiveCategory(cat)}
              className="flex-shrink-0 px-5 py-2 rounded-full text-xs tracking-wide transition-all duration-200"
              style={{
                background: activeCategory === cat ? "#b87c5a" : "transparent",
                color: activeCategory === cat ? "#fff" : "#5a3e35",
                border: `1px solid ${activeCategory === cat ? "#b87c5a" : "rgba(184,124,90,0.25)"}`,
              }}
            >
              {cat}
            </button>
          ))}

          {/* [FIX] BUG 2 — Cart indicator auth-aware.
               SEBELUM: selalu tampil {totalItems} untuk semua user.
               SESUDAH:
                 - isAuthenticated + totalItems > 0 → tampil jumlah item
                 - !isAuthenticated + totalItems > 0 → tampil "Login to view cart"
                   dengan Link ke /login, karena guest tidak bisa checkout
                 - totalItems === 0 → tidak tampil apapun               */}
          {totalItems > 0 && (
            isAuthenticated ? (
              <div
                className="flex-shrink-0 ml-auto flex items-center gap-2 px-4 py-2 rounded-full text-xs"
                style={{
                  background: "rgba(184,124,90,0.1)",
                  color: "#8b4c34",
                  border: "1px solid rgba(184,124,90,0.2)",
                }}
              >
                <span>◉</span>
                {totalItems} item{totalItems > 1 ? "s" : ""} in cart
              </div>
            ) : (
              <Link
                to="/login"
                className="flex-shrink-0 ml-auto flex items-center gap-2 px-4 py-2 rounded-full text-xs transition-all duration-200 hover:opacity-80"
                style={{
                  background: "rgba(184,124,90,0.1)",
                  color: "#8b4c34",
                  border: "1px solid rgba(184,124,90,0.2)",
                }}
              >
                <span>◉</span>
                {totalItems} item{totalItems > 1 ? "s" : ""} — Login to view cart
              </Link>
            )
          )}
        </div>
      </div>

      {/* ── PRODUCT GRID ── */}
      <section className="max-w-6xl mx-auto px-6 py-12">
        <p className="text-xs mb-8" style={{ color: "#9a6e62" }}>
          Showing {filtered.length} product{filtered.length !== 1 ? "s" : ""}
          {activeCategory !== "All" ? ` in ${activeCategory}` : ""}
        </p>

        <div className="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
          {filtered.map((p) => {
            // [FIX] BUG 3 — ambil qty per produk, bukan boolean isInCart
            const qtyInCart = getQtyInCart(p.id);
            const isAdding  = added === p.id;

            return (
              <div
                key={p.id}
                className="group relative rounded-2xl overflow-hidden transition-all duration-300 hover:-translate-y-1"
                style={{
                  background: "#fff",
                  border: "1px solid rgba(184,124,90,0.12)",
                  boxShadow: "0 2px 12px rgba(150,80,40,0.04)",
                }}
              >
                {/* Product visual */}
                <div
                  className="relative h-44 flex items-center justify-center"
                  style={{ background: p.color }}
                >
                  <span className="text-6xl opacity-30 select-none" style={{ color: "#b87c5a" }}>
                    {p.icon}
                  </span>
                  <div
                    className="absolute top-3 left-3 px-2.5 py-0.5 rounded-full text-xs"
                    style={{
                      background: "rgba(255,255,255,0.8)",
                      color: "#8b4c34",
                      backdropFilter: "blur(4px)",
                    }}
                  >
                    {p.tag}
                  </div>
                  {p.originalPrice && (
                    <div
                      className="absolute top-3 right-3 px-2 py-0.5 rounded-full text-xs font-medium"
                      style={{ background: "#b87c5a", color: "#fff" }}
                    >
                      Sale
                    </div>
                  )}
                  {/* [FIX] BUG 3 — badge qty di pojok kanan atas visual
                       Muncul saat qtyInCart > 0, menggantikan "In Cart" teks
                       agar user tahu qty tanpa tombol jadi disabled          */}
                  {qtyInCart > 0 && (
                    <div
                      className="absolute bottom-3 right-3 w-6 h-6 rounded-full flex items-center justify-center text-xs font-semibold"
                      style={{ background: "#b87c5a", color: "#fff" }}
                    >
                      {qtyInCart}
                    </div>
                  )}
                </div>

                {/* Product info */}
                <div className="p-5">
                  <p className="text-xs mb-1" style={{ color: "#b87c5a" }}>{p.category}</p>
                  <h3 className="text-sm font-medium leading-snug mb-2" style={{ color: "#2c1f1a" }}>
                    {p.name}
                  </h3>
                  <p className="text-xs leading-relaxed mb-4" style={{ color: "#7a5a52" }}>{p.desc}</p>

                  {/* Rating */}
                  <div className="flex items-center gap-1.5 mb-4">
                    <span style={{ color: "#b87c5a", fontSize: 11 }}>{"★".repeat(5)}</span>
                    <span className="text-xs" style={{ color: "#9a6e62" }}>
                      {p.rating} ({p.reviews})
                    </span>
                  </div>

                  {/* Price + CTA */}
                  <div className="flex items-center justify-between">
                    <div>
                      <span className="text-base font-semibold" style={{ color: "#2c1f1a" }}>
                        {fmt(p.price)}
                      </span>
                      {p.originalPrice && (
                        <span className="text-xs ml-1.5 line-through" style={{ color: "#b0907e" }}>
                          {fmt(p.originalPrice)}
                        </span>
                      )}
                    </div>

                    {/* [FIX] BUG 1 & 3 — button selalu aktif (tidak ada disabled).
                         Label:
                           - isAdding  → "Added ✓"  (feedback 1.2 detik)
                           - qtyInCart → "+ 1 more"  (produk sudah di cart, bisa tambah lagi)
                           - default   → "+ Cart"    (belum di cart)
                         Tidak ada state "In Cart" yang memblokir klik berikutnya. */}
                    <button
                      onClick={() => handleAdd(p)}
                      className="px-3 py-1.5 rounded-full text-xs transition-all duration-200 active:scale-95"
                      style={{
                        background: qtyInCart > 0 ? "#b87c5a" : "rgba(184,124,90,0.1)",
                        color: qtyInCart > 0 ? "#fff" : "#8b4c34",
                        border: "1px solid rgba(184,124,90,0.2)",
                      }}
                    >
                      {isAdding ? "Added ✓" : qtyInCart > 0 ? "+ 1 more" : "+ Cart"}
                    </button>
                  </div>
                </div>
              </div>
            );
          })}
        </div>
      </section>

      {/* ── CTA STRIP ── */}
      <section
        className="py-16 px-6 text-center"
        style={{ background: "linear-gradient(135deg, #f0ddd0, #e8c9b0)" }}
      >
        <p className="text-xs tracking-widest uppercase mb-3" style={{ color: "#8b4c34" }}>
          Not sure what to use?
        </p>
        <h2
          className="text-3xl font-normal mb-5"
          style={{ fontFamily: "'Playfair Display', Georgia, serif", color: "#2c1208" }}
        >
          Get a personalised <span className="italic">product plan</span>
        </h2>
        <Link
          to="/login"
          className="inline-block px-8 py-3 rounded-full text-white text-sm tracking-wide transition-all duration-300 hover:opacity-90"
          style={{ background: "linear-gradient(135deg, #c4865f, #8b4c34)" }}
        >
          Book Consultation
        </Link>
      </section>

      <style>{`
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;1,400&display=swap');
      `}</style>
    </div>
  );
}