import { useState } from "react";
import { useBooking } from "../../contexts/BookingContext";

// ── Static data (replace with API calls via hooks) ──────────────────────────
const SERVICES = [
  { id: 1, name: "Facial Treatment",     duration: "60 min", price: "Rp 350.000", icon: "✦", tag: "Most Popular" },
  { id: 2, name: "Laser Therapy",        duration: "45 min", price: "Rp 750.000", icon: "◈", tag: "Premium"      },
  { id: 3, name: "Aesthetic Injection",  duration: "30 min", price: "Rp 1.200.000", icon: "◇", tag: "Expert Care" },
  { id: 4, name: "Skin Consultation",    duration: "30 min", price: "Free",        icon: "◉", tag: "Free First Visit" },
];

const DOCTORS = [
  { id: 1, name: "dr. Anisa Putri", spec: "Aesthetic Dermatologist", initials: "AP" },
  { id: 2, name: "dr. Ratna Sari",  spec: "Laser & Skin Expert",     initials: "RS" },
  { id: 3, name: "dr. Maya Dewi",   spec: "Cosmetic Physician",       initials: "MD" },
];

const TIME_SLOTS = ["09:00", "10:00", "11:00", "13:00", "14:00", "15:00", "16:00"];

// Generate the next 7 days
function getNextDays(n = 7) {
  const days = [];
  for (let i = 1; i <= n; i++) {
    const d = new Date();
    d.setDate(d.getDate() + i);
    days.push(d);
  }
  return days;
}

const DAY_NAMES = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
const MONTH_NAMES = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];

// ── Steps ───────────────────────────────────────────────────────────────────
const STEPS = ["Service", "Doctor", "Schedule", "Confirm"];

export default function BookingPage() {
  const [step, setStep] = useState(0);
  const [selectedService, setSelectedService] = useState(null);
  const [selectedDoctor,  setSelectedDoctor]  = useState(null);
  const [selectedDate,    setSelectedDate]    = useState(null);
  const [selectedTime,    setSelectedTime]    = useState(null);
  const [notes,           setNotes]           = useState("");
  const [submitted,       setSubmitted]       = useState(false);

  const days = getNextDays(7);

  const canNext =
    (step === 0 && selectedService) ||
    (step === 1 && selectedDoctor) ||
    (step === 2 && selectedDate && selectedTime) ||
    step === 3;

  function handleNext() {
    if (step < 3) setStep(step + 1);
  }
  function handleBack() {
    if (step > 0) setStep(step - 1);
  }

  async function handleSubmit() {
    // TODO: call createBooking from BookingContext
    setSubmitted(true);
  }

  if (submitted) return <SuccessScreen service={selectedService} doctor={selectedDoctor} date={selectedDate} time={selectedTime} />;

  return (
    <div className="min-h-screen" style={{ background: "#faf8f5", color: "#2c1f1a" }}>
      <div className="max-w-3xl mx-auto px-6 py-10">

        {/* Header */}
        <div className="mb-10">
          <p className="text-xs tracking-widest uppercase mb-1" style={{ color: "#b87c5a" }}>Patient Portal</p>
          <h1
            className="text-4xl font-normal"
            style={{ fontFamily: "'Playfair Display', Georgia, serif", color: "#2c1f1a" }}
          >
            Book a <span className="italic" style={{ color: "#b87c5a" }}>Treatment</span>
          </h1>
        </div>

        {/* Progress Bar */}
        <div className="flex items-center gap-0 mb-10">
          {STEPS.map((s, i) => (
            <div key={s} className="flex items-center flex-1 last:flex-none">
              <div className="flex flex-col items-center">
                <div
                  className="w-8 h-8 rounded-full flex items-center justify-center text-xs font-medium transition-all duration-300"
                  style={{
                    background: i <= step ? "linear-gradient(135deg, #c4865f, #a0613e)" : "rgba(184,124,90,0.12)",
                    color:      i <= step ? "#fff" : "#b87c5a",
                  }}
                >
                  {i < step ? "✓" : i + 1}
                </div>
                <p
                  className="text-xs mt-1.5 hidden sm:block"
                  style={{ color: i === step ? "#b87c5a" : "#c0a090" }}
                >
                  {s}
                </p>
              </div>
              {i < STEPS.length - 1 && (
                <div
                  className="flex-1 h-px mx-2 transition-all duration-300"
                  style={{ background: i < step ? "#b87c5a" : "rgba(184,124,90,0.2)" }}
                />
              )}
            </div>
          ))}
        </div>

        {/* ── Step 0: Choose Service ── */}
        {step === 0 && (
          <div className="animate-fadeIn">
            <SectionTitle icon="✦" title="Choose a Service" />
            <div className="grid sm:grid-cols-2 gap-4 mt-5">
              {SERVICES.map((s) => (
                <SelectCard
                  key={s.id}
                  selected={selectedService?.id === s.id}
                  onClick={() => setSelectedService(s)}
                >
                  <div className="flex items-start justify-between mb-3">
                    <span className="text-2xl" style={{ color: "#b87c5a" }}>{s.icon}</span>
                    <span
                      className="text-xs px-2 py-0.5 rounded-full"
                      style={{ background: "rgba(184,124,90,0.1)", color: "#8b4c34" }}
                    >
                      {s.tag}
                    </span>
                  </div>
                  <p className="text-sm font-medium" style={{ color: "#2c1f1a" }}>{s.name}</p>
                  <div className="flex items-center justify-between mt-2">
                    <p className="text-xs" style={{ color: "#9a6e62" }}>{s.duration}</p>
                    <p className="text-sm font-semibold" style={{ color: "#b87c5a" }}>{s.price}</p>
                  </div>
                </SelectCard>
              ))}
            </div>
          </div>
        )}

        {/* ── Step 1: Choose Doctor ── */}
        {step === 1 && (
          <div className="animate-fadeIn">
            <SectionTitle icon="◈" title="Choose Your Doctor" />
            <div className="flex flex-col gap-4 mt-5">
              {DOCTORS.map((d) => (
                <SelectCard
                  key={d.id}
                  selected={selectedDoctor?.id === d.id}
                  onClick={() => setSelectedDoctor(d)}
                  horizontal
                >
                  <div
                    className="w-12 h-12 rounded-full shrink-0 flex items-center justify-center font-semibold"
                    style={{
                      background: "linear-gradient(135deg, #e8c9b0, #d4a882)",
                      color: "#5a2e12",
                      fontFamily: "'Playfair Display', Georgia, serif",
                    }}
                  >
                    {d.initials}
                  </div>
                  <div>
                    <p className="text-sm font-medium" style={{ color: "#2c1f1a" }}>{d.name}</p>
                    <p className="text-xs mt-0.5" style={{ color: "#b87c5a" }}>{d.spec}</p>
                  </div>
                </SelectCard>
              ))}
            </div>
          </div>
        )}

        {/* ── Step 2: Choose Date & Time ── */}
        {step === 2 && (
          <div className="animate-fadeIn">
            <SectionTitle icon="◇" title="Pick a Schedule" />

            <p className="text-xs tracking-widest uppercase mt-5 mb-3" style={{ color: "#9a6e62" }}>Date</p>
            <div className="flex gap-3 overflow-x-auto pb-2">
              {days.map((d) => {
                const isSelected = selectedDate?.toDateString() === d.toDateString();
                return (
                  <button
                    key={d.toISOString()}
                    onClick={() => setSelectedDate(d)}
                    className="shrink-0 w-16 py-3 rounded-2xl flex flex-col items-center transition-all duration-200"
                    style={{
                      background: isSelected ? "linear-gradient(135deg, #c4865f, #a0613e)" : "#fff",
                      border:     isSelected ? "none" : "1px solid rgba(184,124,90,0.15)",
                      color:      isSelected ? "#fff" : "#5a3e35",
                    }}
                  >
                    <span className="text-xs opacity-70">{DAY_NAMES[d.getDay()]}</span>
                    <span className="text-lg font-semibold mt-0.5" style={{ fontFamily: "'Playfair Display', Georgia, serif" }}>
                      {d.getDate()}
                    </span>
                    <span className="text-xs opacity-70">{MONTH_NAMES[d.getMonth()]}</span>
                  </button>
                );
              })}
            </div>

            <p className="text-xs tracking-widest uppercase mt-6 mb-3" style={{ color: "#9a6e62" }}>Time</p>
            <div className="grid grid-cols-4 gap-3">
              {TIME_SLOTS.map((t) => {
                const isSelected = selectedTime === t;
                return (
                  <button
                    key={t}
                    onClick={() => setSelectedTime(t)}
                    className="py-2.5 rounded-xl text-sm transition-all duration-200"
                    style={{
                      background: isSelected ? "linear-gradient(135deg, #c4865f, #a0613e)" : "#fff",
                      border:     isSelected ? "none" : "1px solid rgba(184,124,90,0.15)",
                      color:      isSelected ? "#fff" : "#5a3e35",
                    }}
                  >
                    {t}
                  </button>
                );
              })}
            </div>
          </div>
        )}

        {/* ── Step 3: Confirm ── */}
        {step === 3 && (
          <div className="animate-fadeIn">
            <SectionTitle icon="◉" title="Confirm Booking" />
            <div
              className="mt-5 rounded-2xl p-7"
              style={{ background: "#fff", border: "1px solid rgba(184,124,90,0.12)" }}
            >
              <p className="text-xs tracking-widest uppercase mb-5" style={{ color: "#b87c5a" }}>Summary</p>
              {[
                { label: "Service",  value: selectedService?.name },
                { label: "Doctor",   value: selectedDoctor?.name  },
                { label: "Date",     value: selectedDate ? `${DAY_NAMES[selectedDate.getDay()]}, ${selectedDate.getDate()} ${MONTH_NAMES[selectedDate.getMonth()]} ${selectedDate.getFullYear()}` : "—" },
                { label: "Time",     value: selectedTime },
                { label: "Duration", value: selectedService?.duration },
                { label: "Price",    value: selectedService?.price },
              ].map((row) => (
                <div key={row.label} className="flex justify-between py-2.5" style={{ borderBottom: "1px solid rgba(184,124,90,0.08)" }}>
                  <span className="text-sm" style={{ color: "#9a6e62" }}>{row.label}</span>
                  <span className="text-sm font-medium" style={{ color: "#2c1f1a" }}>{row.value ?? "—"}</span>
                </div>
              ))}

              {/* Notes */}
              <div className="mt-5">
                <label className="text-xs tracking-widest uppercase block mb-2" style={{ color: "#9a6e62" }}>
                  Additional Notes <span style={{ color: "#c0a090" }}>(optional)</span>
                </label>
                <textarea
                  rows={3}
                  value={notes}
                  onChange={(e) => setNotes(e.target.value)}
                  placeholder="Any skin concerns or requests for your doctor…"
                  className="w-full rounded-xl px-4 py-3 text-sm resize-none outline-none transition-all"
                  style={{
                    background: "#faf8f5",
                    border: "1px solid rgba(184,124,90,0.2)",
                    color: "#2c1f1a",
                  }}
                />
              </div>
            </div>
          </div>
        )}

        {/* Navigation buttons */}
        <div className="flex items-center justify-between mt-8">
          <button
            onClick={handleBack}
            className="px-6 py-2.5 rounded-full text-sm transition-all hover:bg-stone-100"
            style={{ color: "#5a3e35", visibility: step === 0 ? "hidden" : "visible" }}
          >
            ← Back
          </button>

          {step < 3 ? (
            <button
              onClick={handleNext}
              disabled={!canNext}
              className="px-8 py-3 rounded-full text-white text-sm tracking-wide transition-all duration-200 hover:opacity-90 disabled:opacity-40"
              style={{ background: "linear-gradient(135deg, #c4865f, #a0613e)" }}
            >
              Continue →
            </button>
          ) : (
            <button
              onClick={handleSubmit}
              className="px-8 py-3 rounded-full text-white text-sm tracking-wide transition-all duration-200 hover:opacity-90"
              style={{ background: "linear-gradient(135deg, #c4865f, #a0613e)" }}
            >
              Confirm Booking
            </button>
          )}
        </div>
      </div>

      <style>{`
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;1,400;1,500&display=swap');
        @keyframes fadeIn {
          from { opacity: 0; transform: translateY(16px); }
          to   { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeIn { animation: fadeIn 0.35s ease-out both; }
      `}</style>
    </div>
  );
}

// ── Sub-components ───────────────────────────────────────────────────────────

function SectionTitle({ icon, title }) {
  return (
    <div className="flex items-center gap-3">
      <span className="text-xl" style={{ color: "#b87c5a" }}>{icon}</span>
      <h2
        className="text-2xl font-normal"
        style={{ fontFamily: "'Playfair Display', Georgia, serif", color: "#2c1f1a" }}
      >
        {title}
      </h2>
    </div>
  );
}

function SelectCard({ children, selected, onClick, horizontal = false }) {
  return (
    <button
      onClick={onClick}
      className={`text-left p-5 rounded-2xl transition-all duration-200 ${horizontal ? "flex items-center gap-4" : "block"}`}
      style={{
        background: selected ? "rgba(184,124,90,0.08)" : "#fff",
        border: selected ? "1.5px solid #b87c5a" : "1px solid rgba(184,124,90,0.12)",
        boxShadow: selected ? "0 0 0 3px rgba(184,124,90,0.08)" : "none",
      }}
    >
      {children}
    </button>
  );
}

function SuccessScreen({ service, doctor, date, time }) {
  const MONTH_NAMES = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
  const DAY_NAMES   = ["Sun","Mon","Tue","Wed","Thu","Fri","Sat"];
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
          Booking <span className="italic" style={{ color: "#b87c5a" }}>Confirmed</span>
        </h2>
        <p className="text-sm mb-8" style={{ color: "#9a6e62" }}>
          Your appointment has been scheduled. We'll send a reminder closer to your visit.
        </p>
        <div
          className="rounded-2xl p-6 text-left mb-8"
          style={{ background: "#fff", border: "1px solid rgba(184,124,90,0.12)" }}
        >
          {[
            { label: "Service", value: service?.name },
            { label: "Doctor",  value: doctor?.name  },
            { label: "Date",    value: date ? `${DAY_NAMES[date.getDay()]}, ${date.getDate()} ${MONTH_NAMES[date.getMonth()]}` : "—" },
            { label: "Time",    value: time },
          ].map((r) => (
            <div key={r.label} className="flex justify-between py-2.5" style={{ borderBottom: "1px solid rgba(184,124,90,0.08)" }}>
              <span className="text-sm" style={{ color: "#9a6e62" }}>{r.label}</span>
              <span className="text-sm font-medium" style={{ color: "#2c1f1a" }}>{r.value ?? "—"}</span>
            </div>
          ))}
        </div>
        <a
          href="/patient/dashboard"
          className="inline-block px-8 py-3 rounded-full text-white text-sm tracking-wide hover:opacity-90 transition"
          style={{ background: "linear-gradient(135deg, #c4865f, #a0613e)" }}
        >
          Back to Dashboard
        </a>
      </div>
    </div>
  );
}