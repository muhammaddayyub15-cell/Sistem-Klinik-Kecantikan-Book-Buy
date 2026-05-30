import { useState } from "react";
import { Link, useNavigate } from "react-router-dom";
import { useAuth } from "../../contexts/AuthContext";
import { register } from "../../api/authApi";

/**
 * RegisterPage — 3-step registration flow:
 *   Step 1: Choose role (patient / doctor)
 *   Step 2: Personal information
 *   Step 3: Account credentials + submit
 */

const STEPS = ["Role", "Details", "Account"];

const ROLES = [
  {
    value: "patient",
    icon: (
      <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.4">
        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
        <circle cx="12" cy="7" r="4" />
      </svg>
    ),
    label: "Patient",
    desc: "Book treatments, track your progress, shop skincare products.",
  },
  {
    value: "doctor",
    icon: (
      <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.4">
        <path d="M8 2v4M16 2v4M3 10h18M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z" />
        <path d="M12 13v4M10 15h4" />
      </svg>
    ),
    label: "Doctor",
    desc: "Manage your schedule, consult patients, create medical records.",
  },
];

export default function RegisterPage() {
  const { login: setAuth } = useAuth();
  const navigate = useNavigate();

  const [step, setStep] = useState(0);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState("");
  const [focused, setFocused] = useState("");
  const [showPass, setShowPass] = useState(false);
  const [showConfirm, setShowConfirm] = useState(false);

  const [form, setForm] = useState({
    role: "",
    name: "",
    phone: "",
    date_of_birth: "",
    gender: "",
    email: "",
    password: "",
    confirmPassword: "",
  });

  const set = (key, val) => {
    setForm((p) => ({ ...p, [key]: val }));
    if (error) setError("");
  };

  const REDIRECT = { patient: "/patient/dashboard", doctor: "/doctor/dashboard" };

  // ── Validation per step ──
  const validateStep = () => {
    if (step === 0) {
      if (!form.role) { setError("Please select your role."); return false; }
    }
    if (step === 1) {
      if (!form.name.trim()) { setError("Full name is required."); return false; }
      if (!form.phone.trim()) { setError("Phone number is required."); return false; }
      if (!form.date_of_birth) { setError("Date of birth is required."); return false; }
      if (!form.gender) { setError("Gender is required."); return false; }
    }
    if (step === 2) {
      if (!form.email.trim()) { setError("Email is required."); return false; }
      if (!/\S+@\S+\.\S+/.test(form.email)) { setError("Enter a valid email address."); return false; }
      if (form.password.length < 8) { setError("Password must be at least 8 characters."); return false; }
      if (form.password !== form.confirmPassword) { setError("Passwords do not match."); return false; }
    }
    setError("");
    return true;
  };

  const handleNext = () => {
    if (!validateStep()) return;
    setStep((s) => s + 1);
  };

  const handleBack = () => {
    setError("");
    setStep((s) => s - 1);
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    if (!validateStep()) return;
    setLoading(true);
    try {
      const { confirmPassword, ...payload } = form;
      const res = await register(payload);
      setAuth(res.data);
      navigate(REDIRECT[res.data.user.role] ?? "/");
    } catch (err) {
      setError(err?.response?.data?.message ?? "Registration failed. Please try again.");
    } finally {
      setLoading(false);
    }
  };

  // ── Shared input style ──
  const inputStyle = (name) => ({
    width: "100%",
    padding: "13px 16px",
    borderRadius: 12,
    border: focused === name ? "1.5px solid #b87c5a" : "1.5px solid rgba(184,124,90,0.2)",
    background: focused === name ? "#fff" : "rgba(253,246,239,0.6)",
    fontSize: 14,
    color: "#2c1f1a",
    outline: "none",
    transition: "all 0.2s ease",
    fontFamily: "inherit",
    boxSizing: "border-box",
  });

  const labelStyle = {
    display: "block",
    fontSize: 12,
    fontWeight: 500,
    color: "#5a3e35",
    marginBottom: 7,
    letterSpacing: "0.03em",
  };

  // ── Step content ──
  const StepContent = () => {
    if (step === 0) return (
      <div style={{ display: "flex", flexDirection: "column", gap: 14 }}>
        <p style={{ fontSize: 13, color: "#7a5a52", marginBottom: 4 }}>
          This helps us personalise your experience.
        </p>
        {ROLES.map((r) => (
          <button
            key={r.value}
            type="button"
            onClick={() => set("role", r.value)}
            style={{
              display: "flex",
              alignItems: "center",
              gap: 16,
              padding: "18px 20px",
              borderRadius: 14,
              border: form.role === r.value
                ? "1.5px solid #b87c5a"
                : "1.5px solid rgba(184,124,90,0.2)",
              background: form.role === r.value
                ? "rgba(184,124,90,0.07)"
                : "rgba(253,246,239,0.4)",
              cursor: "pointer",
              textAlign: "left",
              transition: "all 0.2s ease",
              width: "100%",
              fontFamily: "inherit",
            }}
          >
            <div
              style={{
                width: 52,
                height: 52,
                borderRadius: 14,
                display: "flex",
                alignItems: "center",
                justifyContent: "center",
                background: form.role === r.value
                  ? "rgba(184,124,90,0.15)"
                  : "rgba(184,124,90,0.07)",
                color: form.role === r.value ? "#b87c5a" : "#9a7065",
                flexShrink: 0,
                transition: "all 0.2s",
              }}
            >
              {r.icon}
            </div>
            <div>
              <p style={{
                fontSize: 15, fontWeight: 500, margin: "0 0 4px",
                color: form.role === r.value ? "#2c1f1a" : "#5a3e35",
              }}>
                {r.label}
              </p>
              <p style={{ fontSize: 12.5, color: "#9a7065", margin: 0, lineHeight: 1.5 }}>
                {r.desc}
              </p>
            </div>
            {form.role === r.value && (
              <div style={{ marginLeft: "auto", flexShrink: 0 }}>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#b87c5a" strokeWidth="2.5">
                  <polyline points="20 6 9 17 4 12" />
                </svg>
              </div>
            )}
          </button>
        ))}
      </div>
    );

    if (step === 1) return (
      <div style={{ display: "flex", flexDirection: "column", gap: 16 }}>
        {/* Full name */}
        <div>
          <label style={labelStyle}>Full name</label>
          <input
            type="text"
            value={form.name}
            onChange={(e) => set("name", e.target.value)}
            onFocus={() => setFocused("name")}
            onBlur={() => setFocused("")}
            placeholder="e.g. Sari Dewi"
            style={inputStyle("name")}
          />
        </div>

        {/* Phone */}
        <div>
          <label style={labelStyle}>Phone number</label>
          <input
            type="tel"
            value={form.phone}
            onChange={(e) => set("phone", e.target.value)}
            onFocus={() => setFocused("phone")}
            onBlur={() => setFocused("")}
            placeholder="+62 812 3456 7890"
            style={inputStyle("phone")}
          />
        </div>

        {/* DOB + Gender row */}
        <div style={{ display: "grid", gridTemplateColumns: "1fr 1fr", gap: 12 }}>
          <div>
            <label style={labelStyle}>Date of birth</label>
            <input
              type="date"
              value={form.date_of_birth}
              onChange={(e) => set("date_of_birth", e.target.value)}
              onFocus={() => setFocused("dob")}
              onBlur={() => setFocused("")}
              style={inputStyle("dob")}
            />
          </div>
          <div>
            <label style={labelStyle}>Gender</label>
            <select
              value={form.gender}
              onChange={(e) => set("gender", e.target.value)}
              onFocus={() => setFocused("gender")}
              onBlur={() => setFocused("")}
              style={{ ...inputStyle("gender"), cursor: "pointer" }}
            >
              <option value="">Select</option>
              <option value="female">Female</option>
              <option value="male">Male</option>
              <option value="other">Other</option>
            </select>
          </div>
        </div>

        {/* Doctor-only: specialization */}
        {form.role === "doctor" && (
          <div>
            <label style={labelStyle}>Specialization</label>
            <select
              value={form.specialization ?? ""}
              onChange={(e) => set("specialization", e.target.value)}
              onFocus={() => setFocused("spec")}
              onBlur={() => setFocused("")}
              style={{ ...inputStyle("spec"), cursor: "pointer" }}
            >
              <option value="">Select specialization</option>
              <option value="aesthetic_dermatology">Aesthetic Dermatology</option>
              <option value="laser_skin">Laser & Skin Expert</option>
              <option value="cosmetic_physician">Cosmetic Physician</option>
              <option value="general_skin">General Skincare</option>
            </select>
          </div>
        )}
      </div>
    );

    if (step === 2) return (
      <div style={{ display: "flex", flexDirection: "column", gap: 16 }}>
        {/* Email */}
        <div>
          <label style={labelStyle}>Email address</label>
          <input
            type="email"
            value={form.email}
            onChange={(e) => set("email", e.target.value)}
            onFocus={() => setFocused("email")}
            onBlur={() => setFocused("")}
            placeholder="you@example.com"
            autoComplete="email"
            style={inputStyle("email")}
          />
        </div>

        {/* Password */}
        <div>
          <label style={labelStyle}>Password</label>
          <div style={{ position: "relative" }}>
            <input
              type={showPass ? "text" : "password"}
              value={form.password}
              onChange={(e) => set("password", e.target.value)}
              onFocus={() => setFocused("password")}
              onBlur={() => setFocused("")}
              placeholder="Min. 8 characters"
              autoComplete="new-password"
              style={{ ...inputStyle("password"), paddingRight: 46 }}
            />
            <button
              type="button"
              onClick={() => setShowPass(!showPass)}
              style={{ position: "absolute", right: 14, top: "50%", transform: "translateY(-50%)", background: "none", border: "none", cursor: "pointer", color: "#b0907e", display: "flex" }}
              aria-label={showPass ? "Hide password" : "Show password"}
            >
              {showPass ? (
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5">
                  <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24" />
                  <line x1="1" y1="1" x2="23" y2="23" />
                </svg>
              ) : (
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                  <circle cx="12" cy="12" r="3" />
                </svg>
              )}
            </button>
          </div>

          {/* Password strength */}
          {form.password && (
            <div style={{ marginTop: 8 }}>
              <div style={{ display: "flex", gap: 4, marginBottom: 4 }}>
                {[1, 2, 3, 4].map((lvl) => {
                  const strength = form.password.length < 8 ? 1
                    : form.password.length < 10 ? 2
                    : /[A-Z]/.test(form.password) && /[0-9]/.test(form.password) ? 4 : 3;
                  return (
                    <div
                      key={lvl}
                      style={{
                        flex: 1,
                        height: 3,
                        borderRadius: 4,
                        background: lvl <= strength
                          ? strength === 1 ? "#b04040"
                          : strength === 2 ? "#d4874e"
                          : strength === 3 ? "#9a7e14"
                          : "#3a7a5a"
                          : "rgba(184,124,90,0.15)",
                        transition: "background 0.3s",
                      }}
                    />
                  );
                })}
              </div>
              <p style={{ fontSize: 11, color: "#9a7065" }}>
                {form.password.length < 8 ? "Too short"
                  : form.password.length < 10 ? "Weak — try adding more characters"
                  : /[A-Z]/.test(form.password) && /[0-9]/.test(form.password) ? "Strong password ✓"
                  : "Good — add uppercase & numbers for stronger security"}
              </p>
            </div>
          )}
        </div>

        {/* Confirm password */}
        <div>
          <label style={labelStyle}>Confirm password</label>
          <div style={{ position: "relative" }}>
            <input
              type={showConfirm ? "text" : "password"}
              value={form.confirmPassword}
              onChange={(e) => set("confirmPassword", e.target.value)}
              onFocus={() => setFocused("confirm")}
              onBlur={() => setFocused("")}
              placeholder="Re-enter your password"
              autoComplete="new-password"
              style={{
                ...inputStyle("confirm"),
                paddingRight: 46,
                border: form.confirmPassword && form.confirmPassword !== form.password
                  ? "1.5px solid rgba(176,64,64,0.4)"
                  : form.confirmPassword && form.confirmPassword === form.password
                  ? "1.5px solid rgba(58,122,90,0.4)"
                  : inputStyle("confirm").border,
              }}
            />
            <button
              type="button"
              onClick={() => setShowConfirm(!showConfirm)}
              style={{ position: "absolute", right: 14, top: "50%", transform: "translateY(-50%)", background: "none", border: "none", cursor: "pointer", color: "#b0907e", display: "flex" }}
            >
              {showConfirm ? (
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5">
                  <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24" />
                  <line x1="1" y1="1" x2="23" y2="23" />
                </svg>
              ) : (
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                  <circle cx="12" cy="12" r="3" />
                </svg>
              )}
            </button>
          </div>
        </div>

        {/* Terms */}
        <p style={{ fontSize: 12, color: "#9a7065", lineHeight: 1.6 }}>
          By creating an account, you agree to our{" "}
          <a href="/terms" style={{ color: "#b87c5a", textDecoration: "none" }}>Terms of Service</a>
          {" "}and{" "}
          <a href="/privacy" style={{ color: "#b87c5a", textDecoration: "none" }}>Privacy Policy</a>.
        </p>
      </div>
    );
  };

  return (
    <div
      style={{
        minHeight: "100vh",
        display: "flex",
        fontFamily: "'DM Sans', sans-serif",
        background: "#faf8f5",
      }}
    >
      {/* ── LEFT PANEL ── */}
      <div
        className="hidden lg:flex"
        style={{
          width: "42%",
          flexDirection: "column",
          justifyContent: "space-between",
          padding: "48px",
          background: "linear-gradient(160deg, #2c1208 0%, #5a2e12 50%, #8b4c34 100%)",
          position: "relative",
          overflow: "hidden",
        }}
      >
        {/* Ornament rings */}
        <div style={{ position: "absolute", top: -100, right: -100, width: 360, height: 360, borderRadius: "50%", border: "1px solid rgba(255,255,255,0.06)" }} />
        <div style={{ position: "absolute", top: -60, right: -60, width: 260, height: 260, borderRadius: "50%", border: "1px solid rgba(255,255,255,0.04)" }} />
        <div style={{ position: "absolute", bottom: -80, left: -80, width: 280, height: 280, borderRadius: "50%", border: "1px solid rgba(255,255,255,0.05)" }} />

        {/* Glow dot */}
        <div style={{ position: "absolute", top: "30%", left: "60%", width: 200, height: 200, borderRadius: "50%", background: "radial-gradient(circle, rgba(184,124,90,0.2) 0%, transparent 70%)" }} />

        {/* Logo */}
        <div style={{ display: "flex", alignItems: "center", gap: 10, position: "relative", zIndex: 1 }}>
          <span style={{ fontSize: 22, color: "#e8c9b0" }}>✦</span>
          <span style={{ fontFamily: "'Playfair Display', Georgia, serif", fontSize: 20, fontWeight: 500, color: "#f5ede4" }}>
            Aura Clinic
          </span>
        </div>

        {/* Steps visual */}
        <div style={{ position: "relative", zIndex: 1 }}>
          <p style={{ fontSize: 11, letterSpacing: "0.12em", textTransform: "uppercase", color: "rgba(232,201,176,0.6)", marginBottom: 32 }}>
            Registration steps
          </p>

          {STEPS.map((s, i) => {
            const done = i < step;
            const active = i === step;
            return (
              <div key={s} style={{ display: "flex", alignItems: "flex-start", gap: 16, marginBottom: i < STEPS.length - 1 ? 0 : 0 }}>
                <div style={{ display: "flex", flexDirection: "column", alignItems: "center" }}>
                  <div
                    style={{
                      width: 32,
                      height: 32,
                      borderRadius: "50%",
                      display: "flex",
                      alignItems: "center",
                      justifyContent: "center",
                      background: done ? "#b87c5a" : active ? "rgba(184,124,90,0.2)" : "rgba(255,255,255,0.06)",
                      border: active ? "1.5px solid #b87c5a" : done ? "none" : "1.5px solid rgba(255,255,255,0.1)",
                      transition: "all 0.3s",
                      flexShrink: 0,
                    }}
                  >
                    {done ? (
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#fff" strokeWidth="2.5">
                        <polyline points="20 6 9 17 4 12" />
                      </svg>
                    ) : (
                      <span style={{ fontSize: 12, fontWeight: 500, color: active ? "#e8c9b0" : "rgba(255,255,255,0.3)" }}>
                        {i + 1}
                      </span>
                    )}
                  </div>
                  {i < STEPS.length - 1 && (
                    <div
                      style={{
                        width: 1,
                        height: 36,
                        background: done ? "#b87c5a" : "rgba(255,255,255,0.08)",
                        transition: "background 0.4s",
                        margin: "4px 0",
                      }}
                    />
                  )}
                </div>
                <div style={{ paddingTop: 6 }}>
                  <p style={{
                    fontSize: 14,
                    fontWeight: active ? 500 : 400,
                    color: active ? "#e8c9b0" : done ? "rgba(232,201,176,0.8)" : "rgba(255,255,255,0.3)",
                    margin: 0,
                    transition: "color 0.3s",
                  }}>
                    {s}
                  </p>
                </div>
              </div>
            );
          })}
        </div>

        {/* Bottom quote */}
        <div style={{ position: "relative", zIndex: 1 }}>
          <p style={{ fontFamily: "'Playfair Display', Georgia, serif", fontSize: 20, color: "rgba(232,201,176,0.7)", fontStyle: "italic", lineHeight: 1.5, margin: 0 }}>
            "Your skin tells your story. Let us help you write a beautiful one."
          </p>
        </div>
      </div>

      {/* ── RIGHT PANEL — form ── */}
      <div
        style={{
          flex: 1,
          display: "flex",
          alignItems: "center",
          justifyContent: "center",
          padding: "40px 24px",
        }}
      >
        <div style={{ width: "100%", maxWidth: 420 }}>

          {/* Mobile logo */}
          <div className="flex lg:hidden" style={{ alignItems: "center", gap: 8, marginBottom: 32 }}>
            <span style={{ fontSize: 18, color: "#b87c5a" }}>✦</span>
            <span style={{ fontFamily: "'Playfair Display', Georgia, serif", fontSize: 18, color: "#2c1f1a" }}>Aura Clinic</span>
          </div>

          {/* Mobile step dots */}
          <div className="flex lg:hidden" style={{ gap: 8, marginBottom: 24 }}>
            {STEPS.map((_, i) => (
              <div key={i} style={{
                height: 4, borderRadius: 4,
                width: i === step ? 24 : 8,
                background: i <= step ? "#b87c5a" : "rgba(184,124,90,0.2)",
                transition: "all 0.3s",
              }} />
            ))}
          </div>

          {/* Step header */}
          <div style={{ marginBottom: 28 }}>
            <p style={{ fontSize: 12, letterSpacing: "0.1em", textTransform: "uppercase", color: "#b87c5a", marginBottom: 8 }}>
              Step {step + 1} of {STEPS.length}
            </p>
            <h1 style={{ fontFamily: "'Playfair Display', Georgia, serif", fontSize: 30, fontWeight: 400, color: "#2c1f1a", margin: 0, lineHeight: 1.2 }}>
              {step === 0 && <>I am a…</>}
              {step === 1 && <>Tell us about<br /><em style={{ color: "#b87c5a" }}>yourself</em></>}
              {step === 2 && <>Create your<br /><em style={{ color: "#b87c5a" }}>account</em></>}
            </h1>
          </div>

          {/* Error */}
          {error && (
            <div style={{
              padding: "12px 16px", borderRadius: 10, background: "rgba(176,64,64,0.08)",
              border: "1px solid rgba(176,64,64,0.2)", color: "#b04040", fontSize: 13,
              marginBottom: 20, display: "flex", alignItems: "center", gap: 8,
            }}>
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                <circle cx="12" cy="12" r="10" /><line x1="12" y1="8" x2="12" y2="12" /><line x1="12" y1="16" x2="12.01" y2="16" />
              </svg>
              {error}
            </div>
          )}

          {/* Dynamic step content */}
          <form onSubmit={step === 2 ? handleSubmit : (e) => { e.preventDefault(); handleNext(); }}>
            <StepContent />

            {/* Navigation buttons */}
            <div style={{ display: "flex", gap: 10, marginTop: 28 }}>
              {step > 0 && (
                <button
                  type="button"
                  onClick={handleBack}
                  style={{
                    padding: "13px 20px",
                    borderRadius: 12,
                    border: "1.5px solid rgba(184,124,90,0.25)",
                    background: "transparent",
                    color: "#5a3e35",
                    fontSize: 14,
                    cursor: "pointer",
                    fontFamily: "inherit",
                    display: "flex",
                    alignItems: "center",
                    gap: 6,
                    transition: "all 0.2s",
                  }}
                >
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                    <path d="M19 12H5M12 19l-7-7 7-7" />
                  </svg>
                  Back
                </button>
              )}

              <button
                type="submit"
                disabled={loading}
                style={{
                  flex: 1,
                  padding: "13px",
                  borderRadius: 12,
                  border: "none",
                  background: loading ? "rgba(184,124,90,0.5)" : "linear-gradient(135deg, #c4865f, #9a5030)",
                  color: "#fff",
                  fontSize: 14,
                  fontWeight: 500,
                  cursor: loading ? "not-allowed" : "pointer",
                  transition: "all 0.25s ease",
                  display: "flex",
                  alignItems: "center",
                  justifyContent: "center",
                  gap: 8,
                  fontFamily: "inherit",
                }}
              >
                {loading ? (
                  <>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" style={{ animation: "spin 0.8s linear infinite" }}>
                      <path d="M21 12a9 9 0 1 1-6.219-8.56" />
                    </svg>
                    Creating account…
                  </>
                ) : step < 2 ? (
                  <>Continue <span style={{ fontSize: 16 }}>→</span></>
                ) : (
                  <>Create Account <span style={{ fontSize: 16 }}>→</span></>
                )}
              </button>
            </div>
          </form>

          {/* Divider + login link */}
          <div style={{ display: "flex", alignItems: "center", gap: 12, margin: "24px 0 16px" }}>
            <div style={{ flex: 1, height: 1, background: "rgba(184,124,90,0.15)" }} />
            <span style={{ fontSize: 12, color: "#b0907e" }}>or</span>
            <div style={{ flex: 1, height: 1, background: "rgba(184,124,90,0.15)" }} />
          </div>

          <p style={{ textAlign: "center", fontSize: 13, color: "#7a5a52" }}>
            Already have an account?{" "}
            <Link to="/login" style={{ color: "#b87c5a", fontWeight: 500, textDecoration: "none" }}>
              Sign in →
            </Link>
          </p>

          <div style={{ textAlign: "center", marginTop: 28 }}>
            <Link to="/" style={{ fontSize: 12, color: "#b0907e", textDecoration: "none", display: "inline-flex", alignItems: "center", gap: 4 }}>
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                <path d="M19 12H5M12 19l-7-7 7-7" />
              </svg>
              Back to homepage
            </Link>
          </div>
        </div>
      </div>

      <style>{`
        @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500&family=Playfair+Display:ital,wght@0,400;0,500;1,400&display=swap');
        @keyframes spin { to { transform: rotate(360deg); } }
        input::placeholder { color: #c4a898; }
        select option { background: #faf8f5; color: #2c1f1a; }
      `}</style>
    </div>
  );
}