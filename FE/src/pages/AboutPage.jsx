import { Link } from "react-router-dom"; // [FIX] tambah import — wajib untuk navigasi SPA

// ── Data ───────────────────────────────────────────────────────────────────
const VALUES = [
  {
    icon: "✦",
    title: "Science-Led Care",
    desc: "Every treatment and product recommendation is backed by clinical research and dermatological evidence.",
  },
  {
    icon: "◈",
    title: "Personalised Approach",
    desc: "No two skin types are alike. We craft individual treatment plans tailored to your unique skin story.",
  },
  {
    icon: "◇",
    title: "Certified Excellence",
    desc: "All procedures are performed by board-certified aesthetic doctors in a licensed medical facility.",
  },
  {
    icon: "◉",
    title: "Long-Term Results",
    desc: "We focus on sustainable skin health, not quick fixes — building routines and treatments that last.",
  },
];

const TEAM = [
  {
    initials: "AP",
    name: "dr. Anisa Putri, SpKK",
    role: "Founder & Lead Dermatologist",
    bio: "8 years in aesthetic dermatology. Trained at RSUPN Cipto Mangunkusumo. Specialises in laser therapy and skin rejuvenation.",
  },
  {
    initials: "RS",
    name: "dr. Ratna Sari",
    role: "Laser & Skin Expert",
    bio: "6 years of experience with advanced laser modalities. Certified in IPL, fractional CO₂, and Pico laser treatments.",
  },
  {
    initials: "MD",
    name: "dr. Maya Dewi",
    role: "Cosmetic Physician",
    bio: "10 years as a cosmetic physician. Expert in injectable aesthetics — Botox, filler, and bio-remodelling treatments.",
  },
  {
    initials: "FH",
    name: "dr. Farah Hana",
    role: "Skin Nutrition Specialist",
    bio: "Integrates nutritional science with dermatology for holistic skin health. Certified in nutricosmetics.",
  },
];

const MILESTONES = [
  { year: "2016", event: "Aura Clinic founded in South Jakarta with a team of 3 doctors." },
  { year: "2018", event: "Expanded to our current flagship clinic. Added laser therapy wing." },
  { year: "2020", event: "Launched Aura Skincare product line — formulated in-house." },
  { year: "2022", event: "Reached 3,000+ patients treated. Opened second consultation room." },
  { year: "2024", event: "Introduced digital booking system and teleconsultation services." },
  { year: "2025", event: "5,000+ happy patients. Recognised as Top Aesthetic Clinic in Jakarta." },
];

const STATS = [
  { value: "5,000+", label: "Patients Treated" },
  { value: "12+", label: "Expert Doctors" },
  { value: "98%", label: "Satisfaction Rate" },
  { value: "8 yrs", label: "Est. 2016" },
];

// ── Component ──────────────────────────────────────────────────────────────
export default function AboutPage() {
  return (
    <div className="min-h-screen font-sans" style={{ background: "#faf8f5", color: "#2c1f1a" }}>

      {/* ── HERO ── */}
      <section
        className="pt-24 pb-20 px-6 relative overflow-hidden"
        style={{ background: "linear-gradient(135deg, #fdf6ef 0%, #f7ede2 60%, #f0ddd0 100%)" }}
      >
        {/* Decorative */}
        <div className="absolute top-12 right-0 w-80 h-80 rounded-full opacity-20" style={{ background: "#e8c9b0", transform: "translate(30%, 0)" }} />
        <div className="absolute bottom-0 left-8 text-8xl opacity-5 select-none" style={{ color: "#b87c5a", fontFamily: "'Playfair Display', Georgia, serif" }}>✦</div>

        <div className="max-w-6xl mx-auto grid lg:grid-cols-2 gap-14 items-center relative">
          <div>
            <p className="text-xs tracking-widest uppercase mb-5" style={{ color: "#b87c5a" }}>✦ Our Story</p>
            <h1
              className="text-5xl lg:text-6xl font-normal leading-tight mb-7"
              style={{ fontFamily: "'Playfair Display', Georgia, serif", color: "#2c1f1a" }}
            >
              Skin Health is
              <br />
              <span className="italic" style={{ color: "#b87c5a" }}>a Journey</span>
            </h1>
            <p className="text-sm leading-relaxed max-w-md mb-5" style={{ color: "#6b4c40" }}>
              Founded in 2016 by dr. Anisa Putri, Aura Clinic was born from a simple belief: everyone deserves access to expert-led, personalised skin care — not just those who can afford luxury.
            </p>
            <p className="text-sm leading-relaxed max-w-md" style={{ color: "#6b4c40" }}>
              We combine the rigour of clinical dermatology with a warm, welcoming environment. From your first consultation to your final treatment, every step is guided by science and care.
            </p>
          </div>

          {/* Stats card */}
          <div className="grid grid-cols-2 gap-4">
            {STATS.map((s) => (
              <div
                key={s.label}
                className="p-7 rounded-2xl"
                style={{ background: "#fff", border: "1px solid rgba(184,124,90,0.12)" }}
              >
                <div
                  className="text-3xl font-semibold mb-1"
                  style={{ fontFamily: "'Playfair Display', Georgia, serif", color: "#2c1f1a" }}
                >
                  {s.value}
                </div>
                <div className="text-xs" style={{ color: "#9a6e62" }}>{s.label}</div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* ── VALUES ── */}
      <section className="py-24 px-6 max-w-6xl mx-auto">
        <div className="text-center mb-16">
          <p className="text-xs tracking-widest uppercase mb-3" style={{ color: "#b87c5a" }}>What Drives Us</p>
          <h2
            className="text-4xl lg:text-5xl font-normal"
            style={{ fontFamily: "'Playfair Display', Georgia, serif", color: "#2c1f1a" }}
          >
            Our Values
          </h2>
        </div>

        <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-5">
          {VALUES.map((v, i) => (
            <div
              key={i}
              className="p-7 rounded-2xl transition-all duration-300 hover:-translate-y-1"
              style={{ background: i % 2 === 0 ? "#fff" : "#fdf6ef", border: "1px solid rgba(184,124,90,0.12)" }}
            >
              <div className="text-3xl mb-5" style={{ color: "#b87c5a" }}>{v.icon}</div>
              <h3 className="text-sm font-medium mb-2" style={{ color: "#2c1f1a" }}>{v.title}</h3>
              <p className="text-xs leading-relaxed" style={{ color: "#7a5a52" }}>{v.desc}</p>
            </div>
          ))}
        </div>
      </section>

      {/* ── TEAM ── */}
      <section className="py-24 px-6" style={{ background: "#fdf6ef" }}>
        <div className="max-w-6xl mx-auto">
          <div className="text-center mb-16">
            <p className="text-xs tracking-widest uppercase mb-3" style={{ color: "#b87c5a" }}>The Experts</p>
            <h2
              className="text-4xl lg:text-5xl font-normal"
              style={{ fontFamily: "'Playfair Display', Georgia, serif", color: "#2c1f1a" }}
            >
              Meet Our Doctors
            </h2>
          </div>

          <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-5">
            {TEAM.map((t, i) => (
              <div
                key={i}
                className="p-7 rounded-2xl transition-all duration-300 hover:-translate-y-1"
                style={{ background: "#fff", border: "1px solid rgba(184,124,90,0.12)" }}
              >
                <div
                  className="w-16 h-16 rounded-full flex items-center justify-center text-lg font-semibold mb-5"
                  style={{
                    background: "linear-gradient(135deg, #e8c9b0, #d4a882)",
                    color: "#5a2e12",
                    fontFamily: "'Playfair Display', Georgia, serif",
                  }}
                >
                  {t.initials}
                </div>
                <h3 className="text-sm font-medium mb-0.5" style={{ color: "#2c1f1a" }}>{t.name}</h3>
                <p className="text-xs mb-3" style={{ color: "#b87c5a" }}>{t.role}</p>
                <p className="text-xs leading-relaxed" style={{ color: "#7a5a52" }}>{t.bio}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* ── TIMELINE ── */}
      <section className="py-24 px-6 max-w-4xl mx-auto">
        <div className="text-center mb-16">
          <p className="text-xs tracking-widest uppercase mb-3" style={{ color: "#b87c5a" }}>Our Journey</p>
          <h2
            className="text-4xl lg:text-5xl font-normal"
            style={{ fontFamily: "'Playfair Display', Georgia, serif", color: "#2c1f1a" }}
          >
            A Decade of Care
          </h2>
        </div>

        <div className="relative">
          {/* Vertical line */}
          <div
            className="absolute left-1/2 top-0 bottom-0 w-px -translate-x-1/2"
            style={{ background: "rgba(184,124,90,0.2)" }}
          />

          <div className="flex flex-col gap-10">
            {MILESTONES.map((m, i) => (
              <div
                key={i}
                className={`relative flex items-center gap-8 ${i % 2 === 0 ? "flex-row" : "flex-row-reverse"}`}
              >
                {/* Content */}
                <div className={`flex-1 ${i % 2 === 0 ? "text-right" : "text-left"}`}>
                  <div
                    className="inline-block p-5 rounded-2xl"
                    style={{ background: "#fff", border: "1px solid rgba(184,124,90,0.12)" }}
                  >
                    <p className="text-xs font-medium mb-1" style={{ color: "#b87c5a" }}>{m.year}</p>
                    <p className="text-sm leading-relaxed" style={{ color: "#5a3e35" }}>{m.event}</p>
                  </div>
                </div>

                {/* Center dot */}
                <div
                  className="flex-shrink-0 w-3 h-3 rounded-full z-10"
                  style={{ background: "#b87c5a", boxShadow: "0 0 0 4px rgba(184,124,90,0.15)" }}
                />

                {/* Empty side */}
                <div className="flex-1" />
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* ── CTA ── */}
      <section className="py-24 px-6">
        <div
          className="max-w-4xl mx-auto rounded-3xl p-12 lg:p-16 text-center relative overflow-hidden"
          style={{ background: "linear-gradient(135deg, #f0ddd0, #e8c9b0)" }}
        >
          <div className="absolute top-4 right-8 text-6xl opacity-10 select-none" style={{ color: "#7a3e22", fontFamily: "'Playfair Display', Georgia, serif" }}>✦</div>
          <p className="text-xs tracking-widest uppercase mb-4" style={{ color: "#8b4c34" }}>Join Our Community</p>
          <h2
            className="text-4xl lg:text-5xl font-normal mb-6"
            style={{ fontFamily: "'Playfair Display', Georgia, serif", color: "#2c1208" }}
          >
            Start Your Skin
            <br />
            <span className="italic">Journey Today</span>
          </h2>
          <p className="text-sm leading-relaxed mb-10 max-w-md mx-auto" style={{ color: "#6b4030" }}>
            Book your first consultation for free. Our doctors will listen, analyse, and guide you toward the skin you deserve.
          </p>
          <div className="flex flex-col sm:flex-row gap-4 justify-center">
            {/* [FIX] <a href> → <Link to> agar tidak full page reload */}
            <Link
              to="/register"
              className="px-8 py-4 rounded-full text-white text-sm tracking-wide transition-all duration-300 hover:opacity-90"
              style={{ background: "linear-gradient(135deg, #c4865f, #8b4c34)" }}
            >
              Book Free Consultation
            </Link>
            {/* Anchor internal (#contact) — boleh tetap <a> */}
            <a
              href="#contact"
              className="px-8 py-4 rounded-full text-sm tracking-wide transition-all duration-300"
              style={{ color: "#5a2e12", border: "1px solid rgba(90,46,18,0.3)" }}
            >
              Get in Touch
            </a>
          </div>
        </div>
      </section>

      <style>{`
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;1,400&display=swap');
      `}</style>
    </div>
  );
}