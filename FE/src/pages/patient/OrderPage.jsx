import { useState } from "react";
import { useCart } from "../../contexts/CartContext";

// ── Static data (replace with useProduct hook) ───────────────────────────────
const CATEGORIES = ["All", "Serum", "Moisturiser", "Sunscreen", "Treatment", "Cleanser"];

const PRODUCTS = [
  { id: 1, name: "Brightening Vitamin C Serum",   category: "Serum",       price: 285000,  unit: "30ml",   icon: "◈", tag: "Best Seller" },
  { id: 2, name: "Deep Repair Moisture Cream",     category: "Moisturiser", price: 245000,  unit: "50ml",   icon: "◉", tag: null          },
  { id: 3, name: "Ceramide Barrier Repair Serum",  category: "Serum",       price: 320000,  unit: "30ml",   icon: "◈", tag: "New"         },
  { id: 4, name: "SPF 50+ Tinted Sunscreen",       category: "Sunscreen",   price: 195000,  unit: "50ml",   icon: "✦", tag: "Recommended" },
  { id: 5, name: "Niacinamide 10% Toner",          category: "Treatment",   price: 165000,  unit: "150ml",  icon: "◇", tag: null          },
  { id: 6, name: "Gentle Foam Cleanser",           category: "Cleanser",    price: 135000,  unit: "120ml",  icon: "◉", tag: null          },
  { id: 7, name: "Retinol Night Renewal Cream",    category: "Treatment",   price: 410000,  unit: "30ml",   icon: "✦", tag: "Premium"     },
  { id: 8, name: "Hyaluronic Acid Hydra Mist",     category: "Serum",       price: 225000,  unit: "100ml",  icon: "◈", tag: null          },
];

function fmt(num) {
  return "Rp " + num.toLocaleString("id-ID");
}

// ── Main Page ─────────────────────────────────────────────────────────────────
export default function OrderPage() {
  const [activeCategory, setActiveCategory] = useState("All");
  const [cartOpen,       setCartOpen]       = useState(false);
  const [cart,           setCart]           = useState({}); // { productId: qty }
  const [checkoutDone,   setCheckoutDone]   = useState(false);

  const filteredProducts =
    activeCategory === "All"
      ? PRODUCTS
      : PRODUCTS.filter((p) => p.category === activeCategory);

  function addToCart(id) {
    setCart((prev) => ({ ...prev, [id]: (prev[id] ?? 0) + 1 }));
  }
  function removeOne(id) {
    setCart((prev) => {
      const qty = (prev[id] ?? 0) - 1;
      if (qty <= 0) {
        const { [id]: _, ...rest } = prev;
        return rest;
      }
      return { ...prev, [id]: qty };
    });
  }
  function clearCart() {
    setCart({});
  }

  const cartItems  = Object.entries(cart).map(([id, qty]) => ({ ...PRODUCTS.find((p) => p.id === +id), qty }));
  const totalItems = cartItems.reduce((s, i) => s + i.qty, 0);
  const totalPrice = cartItems.reduce((s, i) => s + i.price * i.qty, 0);

  function handleCheckout() {
    // TODO: call CartContext checkout()
    setCheckoutDone(true);
    setCartOpen(false);
    clearCart();
  }

  if (checkoutDone) return <CheckoutSuccess onBack={() => setCheckoutDone(false)} />;

  return (
    <div className="min-h-screen" style={{ background: "#faf8f5", color: "#2c1f1a" }}>
      <div className="max-w-5xl mx-auto px-6 py-10">

        {/* Header */}
        <div className="flex items-end justify-between mb-10">
          <div>
            <p className="text-xs tracking-widest uppercase mb-1" style={{ color: "#b87c5a" }}>Clinic Store</p>
            <h1
              className="text-4xl font-normal"
              style={{ fontFamily: "'Playfair Display', Georgia, serif", color: "#2c1f1a" }}
            >
              Skincare <span className="italic" style={{ color: "#b87c5a" }}>Products</span>
            </h1>
          </div>

          {/* Cart button */}
          <button
            onClick={() => setCartOpen(true)}
            className="relative flex items-center gap-2 px-5 py-2.5 rounded-full transition-all hover:opacity-90"
            style={{ background: "linear-gradient(135deg, #c4865f, #a0613e)", color: "#fff" }}
          >
            <span>◇</span>
            <span className="text-sm">Cart</span>
            {totalItems > 0 && (
              <span
                className="absolute -top-1.5 -right-1.5 w-5 h-5 rounded-full flex items-center justify-center text-xs font-semibold"
                style={{ background: "#2c1f1a", color: "#fff" }}
              >
                {totalItems}
              </span>
            )}
          </button>
        </div>

        {/* Category filter */}
        <div className="flex gap-2 overflow-x-auto pb-2 mb-8">
          {CATEGORIES.map((c) => (
            <button
              key={c}
              onClick={() => setActiveCategory(c)}
              className="shrink-0 px-5 py-2 rounded-full text-sm transition-all duration-200"
              style={{
                background: activeCategory === c ? "linear-gradient(135deg, #c4865f, #a0613e)" : "#fff",
                color:      activeCategory === c ? "#fff" : "#5a3e35",
                border:     activeCategory === c ? "none" : "1px solid rgba(184,124,90,0.2)",
              }}
            >
              {c}
            </button>
          ))}
        </div>

        {/* Product Grid */}
        <div className="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
          {filteredProducts.map((p) => {
            const inCart = cart[p.id] ?? 0;
            return (
              <div
                key={p.id}
                className="rounded-2xl p-5 flex flex-col transition-all duration-300 hover:-translate-y-0.5"
                style={{ background: "#fff", border: "1px solid rgba(184,124,90,0.12)" }}
              >
                {/* Product visual */}
                <div
                  className="w-full h-28 rounded-xl flex items-center justify-center text-4xl mb-4"
                  style={{ background: "linear-gradient(135deg, #fdf6ef, #f0ddd0)" }}
                >
                  <span style={{ color: "#b87c5a" }}>{p.icon}</span>
                </div>

                {/* Tag */}
                {p.tag && (
                  <span
                    className="inline-block text-xs px-2.5 py-0.5 rounded-full mb-2 self-start"
                    style={{ background: "rgba(184,124,90,0.1)", color: "#8b4c34" }}
                  >
                    {p.tag}
                  </span>
                )}

                <p className="text-sm font-medium leading-snug flex-1" style={{ color: "#2c1f1a" }}>{p.name}</p>
                <p className="text-xs mt-1 mb-3" style={{ color: "#9a6e62" }}>{p.unit}</p>

                <div className="flex items-center justify-between">
                  <p className="text-sm font-semibold" style={{ color: "#b87c5a" }}>{fmt(p.price)}</p>

                  {inCart === 0 ? (
                    <button
                      onClick={() => addToCart(p.id)}
                      className="w-8 h-8 rounded-full flex items-center justify-center text-sm transition-all hover:opacity-80"
                      style={{ background: "linear-gradient(135deg, #c4865f, #a0613e)", color: "#fff" }}
                    >
                      +
                    </button>
                  ) : (
                    <div className="flex items-center gap-2">
                      <button
                        onClick={() => removeOne(p.id)}
                        className="w-7 h-7 rounded-full flex items-center justify-center text-sm border transition-all"
                        style={{ borderColor: "rgba(184,124,90,0.3)", color: "#b87c5a" }}
                      >
                        −
                      </button>
                      <span className="text-sm font-medium w-4 text-center" style={{ color: "#2c1f1a" }}>
                        {inCart}
                      </span>
                      <button
                        onClick={() => addToCart(p.id)}
                        className="w-7 h-7 rounded-full flex items-center justify-center text-sm transition-all hover:opacity-80"
                        style={{ background: "linear-gradient(135deg, #c4865f, #a0613e)", color: "#fff" }}
                      >
                        +
                      </button>
                    </div>
                  )}
                </div>
              </div>
            );
          })}
        </div>
      </div>

      {/* ── Cart Drawer ── */}
      {cartOpen && (
        <>
          {/* Overlay */}
          <div
            className="fixed inset-0 z-40"
            style={{ background: "rgba(44,31,26,0.4)", backdropFilter: "blur(4px)" }}
            onClick={() => setCartOpen(false)}
          />

          {/* Drawer */}
          <div
            className="fixed top-0 right-0 h-full z-50 flex flex-col"
            style={{
              width: "min(420px, 100vw)",
              background: "#faf8f5",
              boxShadow: "-8px 0 40px rgba(44,31,26,0.12)",
            }}
          >
            {/* Drawer header */}
            <div
              className="flex items-center justify-between px-7 py-5"
              style={{ borderBottom: "1px solid rgba(184,124,90,0.12)" }}
            >
              <div>
                <p className="text-xs tracking-widest uppercase mb-0.5" style={{ color: "#b87c5a" }}>Your</p>
                <h2
                  className="text-2xl font-normal"
                  style={{ fontFamily: "'Playfair Display', Georgia, serif", color: "#2c1f1a" }}
                >
                  Cart
                </h2>
              </div>
              <button
                onClick={() => setCartOpen(false)}
                className="w-8 h-8 rounded-full flex items-center justify-center transition-all hover:bg-stone-100"
                style={{ color: "#5a3e35" }}
              >
                ✕
              </button>
            </div>

            {/* Cart items */}
            <div className="flex-1 overflow-y-auto px-7 py-5">
              {cartItems.length === 0 ? (
                <div className="flex flex-col items-center justify-center h-full gap-4">
                  <span className="text-4xl opacity-20" style={{ color: "#b87c5a" }}>◇</span>
                  <p className="text-sm" style={{ color: "#c0a090" }}>Your cart is empty</p>
                  <button
                    onClick={() => setCartOpen(false)}
                    className="text-sm px-5 py-2 rounded-full transition hover:opacity-80"
                    style={{ background: "rgba(184,124,90,0.1)", color: "#8b4c34" }}
                  >
                    Continue Shopping
                  </button>
                </div>
              ) : (
                <div className="flex flex-col gap-4">
                  {cartItems.map((item) => (
                    <div
                      key={item.id}
                      className="flex items-center gap-4 p-4 rounded-2xl"
                      style={{ background: "#fff", border: "1px solid rgba(184,124,90,0.1)" }}
                    >
                      <div
                        className="w-12 h-12 rounded-xl flex items-center justify-center text-xl shrink-0"
                        style={{ background: "linear-gradient(135deg, #fdf6ef, #f0ddd0)" }}
                      >
                        <span style={{ color: "#b87c5a" }}>{item.icon}</span>
                      </div>
                      <div className="flex-1 min-w-0">
                        <p className="text-sm font-medium leading-snug truncate" style={{ color: "#2c1f1a" }}>{item.name}</p>
                        <p className="text-xs mt-0.5" style={{ color: "#b87c5a" }}>{fmt(item.price)}</p>
                      </div>
                      <div className="flex items-center gap-2 shrink-0">
                        <button
                          onClick={() => removeOne(item.id)}
                          className="w-6 h-6 rounded-full flex items-center justify-center text-xs border"
                          style={{ borderColor: "rgba(184,124,90,0.3)", color: "#b87c5a" }}
                        >
                          −
                        </button>
                        <span className="text-sm w-4 text-center" style={{ color: "#2c1f1a" }}>{item.qty}</span>
                        <button
                          onClick={() => addToCart(item.id)}
                          className="w-6 h-6 rounded-full flex items-center justify-center text-xs hover:opacity-80"
                          style={{ background: "linear-gradient(135deg, #c4865f, #a0613e)", color: "#fff" }}
                        >
                          +
                        </button>
                      </div>
                    </div>
                  ))}
                </div>
              )}
            </div>

            {/* Drawer footer */}
            {cartItems.length > 0 && (
              <div
                className="px-7 py-6"
                style={{ borderTop: "1px solid rgba(184,124,90,0.12)" }}
              >
                <div className="flex justify-between mb-1">
                  <span className="text-sm" style={{ color: "#9a6e62" }}>Subtotal</span>
                  <span className="text-sm font-semibold" style={{ color: "#2c1f1a" }}>{fmt(totalPrice)}</span>
                </div>
                <div className="flex justify-between mb-5">
                  <span className="text-xs" style={{ color: "#b0907e" }}>Shipping calculated at checkout</span>
                </div>
                <button
                  onClick={handleCheckout}
                  className="w-full py-3.5 rounded-full text-white text-sm tracking-wide transition hover:opacity-90"
                  style={{ background: "linear-gradient(135deg, #c4865f, #a0613e)" }}
                >
                  Checkout — {fmt(totalPrice)}
                </button>
                <button
                  onClick={clearCart}
                  className="w-full mt-2 py-2 text-xs transition hover:opacity-70"
                  style={{ color: "#c0a090" }}
                >
                  Clear cart
                </button>
              </div>
            )}
          </div>
        </>
      )}

      <style>{`
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;1,400;1,500&display=swap');
      `}</style>
    </div>
  );
}

// ── Checkout success screen ───────────────────────────────────────────────────
function CheckoutSuccess({ onBack }) {
  return (
    <div className="min-h-screen flex items-center justify-center px-6" style={{ background: "#faf8f5" }}>
      <div className="text-center max-w-md">
        <div
          className="w-20 h-20 rounded-full mx-auto mb-6 flex items-center justify-center text-3xl"
          style={{ background: "linear-gradient(135deg, #e8c9b0, #d4a882)", color: "#5a2e12" }}
        >
          ✦
        </div>
        <h2
          className="text-4xl font-normal mb-3"
          style={{ fontFamily: "'Playfair Display', Georgia, serif", color: "#2c1f1a" }}
        >
          Order <span className="italic" style={{ color: "#b87c5a" }}>Placed!</span>
        </h2>
        <p className="text-sm mb-8" style={{ color: "#9a6e62" }}>
          Your order is confirmed and being prepared. You'll receive a notification once it ships.
        </p>
        <div className="flex flex-col sm:flex-row gap-3 justify-center">
          <a
            href="/patient/dashboard"
            className="px-7 py-3 rounded-full text-white text-sm hover:opacity-90 transition"
            style={{ background: "linear-gradient(135deg, #c4865f, #a0613e)" }}
          >
            Back to Dashboard
          </a>
          <button
            onClick={onBack}
            className="px-7 py-3 rounded-full text-sm transition hover:bg-stone-100"
            style={{ color: "#5a3e35", border: "1px solid rgba(90,62,53,0.2)" }}
          >
            Continue Shopping
          </button>
        </div>
      </div>
    </div>
  );
}