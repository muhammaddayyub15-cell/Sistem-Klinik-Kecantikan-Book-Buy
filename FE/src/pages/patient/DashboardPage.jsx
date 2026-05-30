import { useState } from "react";
import { useAuth } from "../../contexts/AuthContext";

const UPCOMING_BOOKINGS = [
  { id: 1, service: "Facial Treatment", doctor: "dr. Anisa Putri", date: "Mon, 2 Jun 2025", time: "10:00", status: "confirmed" },
  { id: 2, service: "Skin Consultation", doctor: "dr. Maya Dewi", date: "Thu, 5 Jun 2025", time: "14:30", status: "pending" },
];

const RECENT_ORDERS = [
  { id: "ORD-001", items: "Hydrating Serum × 1", total: "Rp 285.000", date: "28 May 2025", status: "delivered" },
  { id: "ORD-002", items: "Vitamin C Cream × 2", total: "Rp 490.000", date: "20 May 2025", status: "delivered" },
];

const STATUS_STYLES = {
  confirmed: { bg: "rgba(134,180,134,0.12)", color: "#3a7a3a", label: "Confirmed" },
  pending:   { bg: "rgba(184,124,90,0.12)",  color: "#8b4c34", label: "Pending"   },
  cancelled: { bg: "rgba(200,80,80,0.1)",    color: "#9a3030", label: "Cancelled" },
  delivered: { bg: "rgba(134,180,134,0.12)", color: "#3a7a3a", label: "Delivered" },
};

const QUICK_ACTIONS = [
  { icon: "◈", label: "Book Treatment", href: "/patient/booking", desc: "Schedule a new session" },
  { icon: "◇", label: "Shop Products", href: "/patient/order",   desc: "Browse skincare range" },
  { icon: "✦", label: "My Records",    href: "#",                 desc: "View consultation history" },
];

export default function PatientDashboardPage() {
  const { user } = useAuth();
  const firstName = user?.name?.split(" ")[0] ?? "there";

  const hour = new Date().getHours();
  const greeting = hour < 12 ? "Good morning" : hour < 17 ? "Good afternoon" : "Good evening";

  return (
    <div className="min-h-screen" style={{ background: "#faf8f5", color: "#2c1f1a" }}>
      <div className="max-w-5xl mx-auto px-6 py-10">

        {/* ── Greeting ── */}
        <div className="mb-10">
          <p className="text-xs tracking-widest uppercase mb-1" style={{ color: "#b87c5a" }}>
            {greeting}
          </p>
          <h1
            className="text-4xl lg:text-5xl font-normal leading-tight"
            style={{ fontFamily: "'Playfair Display', Georgia, serif", color: "#2c1f1a" }}
          >
            Welcome back,{" "}
            <span className="italic" style={{ color: "#b87c5a" }}>{firstName}</span>
          </h1>
          <p className="text-sm mt-2" style={{ color: "#9a6e62" }}>
            Here's what's happening with your skin journey.
          </p>
        </div>

        {/* ── Skin Score Banner ── */}
        <div
          className="relative rounded-3xl p-8 mb-8 overflow-hidden"
          style={{ background: "linear-gradient(135deg, #f0ddd0, #e8c9b0)" }}
        >
          <div
            className="absolute top-4 right-8 text-7xl opacity-10 select-none"
            style={{ color: "#7a3e22", fontFamily: "'Playfair Display', Georgia, serif" }}
          >
            ✦
          </div>
          <div className="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
              <p className="text-xs tracking-widest uppercase mb-2" style={{ color: "#8b4c34" }}>
                Your Skin Journey
              </p>
              <p className="text-lg font-medium" style={{ fontFamily: "'Playfair Display', Georgia, serif", color: "#2c1208" }}>
                2 sessions completed this month
              </p>
              <p className="text-sm mt-1" style={{ color: "#6b4030" }}>
                You're doing great — consistency is key to glowing skin.
              </p>
            </div>
            <div className="flex gap-6">
              {[
                { label: "Sessions", value: "2" },
                { label: "Orders", value: "4" },
                { label: "Next Visit", value: "3d" },
              ].map((s) => (
                <div key={s.label} className="text-center">
                  <div
                    className="text-2xl font-semibold"
                    style={{ fontFamily: "'Playfair Display', Georgia, serif", color: "#2c1208" }}
                  >
                    {s.value}
                  </div>
                  <div className="text-xs mt-0.5" style={{ color: "#7a5040" }}>{s.label}</div>
                </div>
              ))}
            </div>
          </div>
        </div>

        {/* ── Quick Actions ── */}
        <div className="grid grid-cols-3 gap-4 mb-10">
          {QUICK_ACTIONS.map((a) => (
            <a
              key={a.label}
              href={a.href}
              className="group p-6 rounded-2xl transition-all duration-300 hover:-translate-y-0.5"
              style={{ background: "#fff", border: "1px solid rgba(184,124,90,0.12)" }}
            >
              <div className="text-2xl mb-3" style={{ color: "#b87c5a" }}>{a.icon}</div>
              <div className="text-sm font-medium mb-0.5" style={{ color: "#2c1f1a" }}>{a.label}</div>
              <div className="text-xs" style={{ color: "#9a6e62" }}>{a.desc}</div>
            </a>
          ))}
        </div>

        <div className="grid lg:grid-cols-2 gap-6">

          {/* ── Upcoming Bookings ── */}
          <div
            className="rounded-2xl p-7"
            style={{ background: "#fff", border: "1px solid rgba(184,124,90,0.12)" }}
          >
            <div className="flex items-center justify-between mb-6">
              <div>
                <p className="text-xs tracking-widest uppercase mb-1" style={{ color: "#b87c5a" }}>
                  Coming Up
                </p>
                <h2
                  className="text-xl font-normal"
                  style={{ fontFamily: "'Playfair Display', Georgia, serif", color: "#2c1f1a" }}
                >
                  Bookings
                </h2>
              </div>
              <a
                href="/patient/booking"
                className="text-xs px-4 py-1.5 rounded-full transition-all hover:opacity-80"
                style={{ background: "rgba(184,124,90,0.1)", color: "#8b4c34" }}
              >
                + New
              </a>
            </div>

            {UPCOMING_BOOKINGS.length === 0 ? (
              <EmptyState icon="◈" text="No upcoming bookings" />
            ) : (
              <div className="flex flex-col gap-4">
                {UPCOMING_BOOKINGS.map((b) => {
                  const st = STATUS_STYLES[b.status];
                  return (
                    <div
                      key={b.id}
                      className="flex items-start gap-4 p-4 rounded-xl"
                      style={{ background: "#faf8f5" }}
                    >
                      <div
                        className="w-10 h-10 rounded-xl flex items-center justify-center text-base shrink-0"
                        style={{ background: "rgba(184,124,90,0.12)", color: "#b87c5a" }}
                      >
                        ✦
                      </div>
                      <div className="flex-1 min-w-0">
                        <div className="flex items-center justify-between gap-2">
                          <p className="text-sm font-medium truncate" style={{ color: "#2c1f1a" }}>
                            {b.service}
                          </p>
                          <span
                            className="text-xs px-2 py-0.5 rounded-full shrink-0"
                            style={{ background: st.bg, color: st.color }}
                          >
                            {st.label}
                          </span>
                        </div>
                        <p className="text-xs mt-0.5" style={{ color: "#9a6e62" }}>{b.doctor}</p>
                        <p className="text-xs mt-1 font-medium" style={{ color: "#b87c5a" }}>
                          {b.date} · {b.time}
                        </p>
                      </div>
                    </div>
                  );
                })}
              </div>
            )}
          </div>

          {/* ── Recent Orders ── */}
          <div
            className="rounded-2xl p-7"
            style={{ background: "#fff", border: "1px solid rgba(184,124,90,0.12)" }}
          >
            <div className="flex items-center justify-between mb-6">
              <div>
                <p className="text-xs tracking-widest uppercase mb-1" style={{ color: "#b87c5a" }}>
                  Recent
                </p>
                <h2
                  className="text-xl font-normal"
                  style={{ fontFamily: "'Playfair Display', Georgia, serif", color: "#2c1f1a" }}
                >
                  Orders
                </h2>
              </div>
              <a
                href="/patient/order"
                className="text-xs px-4 py-1.5 rounded-full transition-all hover:opacity-80"
                style={{ background: "rgba(184,124,90,0.1)", color: "#8b4c34" }}
              >
                View all
              </a>
            </div>

            {RECENT_ORDERS.length === 0 ? (
              <EmptyState icon="◇" text="No orders yet" />
            ) : (
              <div className="flex flex-col gap-4">
                {RECENT_ORDERS.map((o) => {
                  const st = STATUS_STYLES[o.status];
                  return (
                    <div
                      key={o.id}
                      className="flex items-center gap-4 p-4 rounded-xl"
                      style={{ background: "#faf8f5" }}
                    >
                      <div
                        className="w-10 h-10 rounded-xl flex items-center justify-center text-base shrink-0"
                        style={{ background: "rgba(184,124,90,0.12)", color: "#b87c5a" }}
                      >
                        ◇
                      </div>
                      <div className="flex-1 min-w-0">
                        <div className="flex items-center justify-between gap-2">
                          <p className="text-xs font-medium" style={{ color: "#9a6e62" }}>{o.id}</p>
                          <span
                            className="text-xs px-2 py-0.5 rounded-full shrink-0"
                            style={{ background: st.bg, color: st.color }}
                          >
                            {st.label}
                          </span>
                        </div>
                        <p className="text-sm mt-0.5 truncate" style={{ color: "#2c1f1a" }}>{o.items}</p>
                        <div className="flex items-center justify-between mt-1">
                          <p className="text-xs" style={{ color: "#9a6e62" }}>{o.date}</p>
                          <p className="text-xs font-semibold" style={{ color: "#b87c5a" }}>{o.total}</p>
                        </div>
                      </div>
                    </div>
                  );
                })}
              </div>
            )}
          </div>
        </div>
      </div>

      <style>{`
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;1,400;1,500&display=swap');
      `}</style>
    </div>
  );
}

function EmptyState({ icon, text }) {
  return (
    <div className="flex flex-col items-center justify-center py-10 gap-3">
      <span className="text-3xl opacity-20" style={{ color: "#b87c5a" }}>{icon}</span>
      <p className="text-sm" style={{ color: "#c0a090" }}>{text}</p>
    </div>
  );
}