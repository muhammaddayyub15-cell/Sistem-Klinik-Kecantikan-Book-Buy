import { useState } from "react";
import { Link, useNavigate } from "react-router-dom";
import { useAuth } from "../../contexts/AuthContext";
import { login } from "../../api/authApi";

/**
 * LoginPage — split layout: decorative panel (left) + form (right)
 * Connects to AuthContext on success → redirects by role
 */
export default function LoginPage() {
  const { login: setAuth } = useAuth();
  const navigate = useNavigate();

  const [form, setForm] = useState({ email: "", password: "" });
  const [showPass, setShowPass] = useState(false);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState("");
  const [focused, setFocused] = useState("");

  const REDIRECT = { patient: "/patient/dashboard", doctor: "/doctor/dashboard", admin: "/admin/dashboard" };

  const handleChange = (e) => {
    setForm((p) => ({ ...p, [e.target.name]: e.target.value }));
    if (error) setError("");
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    if (!form.email || !form.password) {
      setError("Please fill in all fields.");
      return;
    }
    setLoading(true);
    try {
      const res = await login(form);
      setAuth(res.data); // { user, token }
      navigate(REDIRECT[res.data.user.role] ?? "/");
    } catch (err) {
      setError(err?.response?.data?.message ?? "Invalid email or password.");
    } finally {
      setLoading(false);
    }
  };

  const inputStyle = (name) => ({
    width: "100%",
    padding: "13px 16px",
    borderRadius: 12,
    border: focused === name
      ? "1.5px solid #b87c5a"
      : "1.5px solid rgba(184,124,90,0.2)",
    background: focused === name ? "#fff" : "rgba(253,246,239,0.6)",
    fontSize: 14,
    color: "#2c1f1a",
    outline: "none",
    transition: "all 0.2s ease",
    fontFamily: "inherit",
  });

  return (
    <div
      style={{
        minHeight: "100vh",
        display: "flex",
        fontFamily: "'DM Sans', sans-serif",
        background: "#faf8f5",
      }}
    >
      {/* ── LEFT PANEL — decorative ── */}
      <div
        className="hidden lg:flex"
        style={{
          width: "45%",
          flexDirection: "column",
          justifyContent: "space-between",
          padding: "48px",
          background: "linear-gradient(160deg, #f0ddd0 0%, #e4c0a4 45%, #cfa07e 100%)",
          position: "relative",
          overflow: "hidden",
        }}
      >
        {/* Background ornaments */}
        <div
          style={{
            position: "absolute",
            top: -80,
            right: -80,
            width: 320,
            height: 320,
            borderRadius: "50%",
            background: "rgba(255,255,255,0.15)",
          }}
        />
        <div
          style={{
            position: "absolute",
            bottom: -60,
            left: -60,
            width: 240,
            height: 240,
            borderRadius: "50%",
            background: "rgba(90,30,5,0.08)",
          }}
        />
        <div
          style={{
            position: "absolute",
            top: "40%",
            left: "60%",
            fontSize: 120,
            color: "rgba(90,30,5,0.06)",
            lineHeight: 1,
            userSelect: "none",
            fontFamily: "'Playfair Display', Georgia, serif",
          }}
        >
          ✦
        </div>

        {/* Logo */}
        <div style={{ display: "flex", alignItems: "center", gap: 10, position: "relative" }}>
          <span style={{ fontSize: 22, color: "#7a3e22" }}>✦</span>
          <span
            style={{
              fontFamily: "'Playfair Display', Georgia, serif",
              fontSize: 20,
              fontWeight: 500,
              color: "#3a1a0a",
            }}
          >
            Aura Clinic
          </span>
        </div>

        {/* Center quote */}
        <div style={{ position: "relative" }}>
          <p
            style={{
              fontFamily: "'Playfair Display', Georgia, serif",
              fontSize: 36,
              fontWeight: 400,
              lineHeight: 1.3,
              color: "#2c1208",
              marginBottom: 20,
            }}
          >
            Welcome back to your
            <br />
            <em>skin journey.</em>
          </p>
          <p style={{ fontSize: 14, color: "#7a4c36", lineHeight: 1.7, maxWidth: 320 }}>
            Manage your bookings, track your treatments, and connect with your doctor — all in one place.
          </p>

          {/* Decorative dots */}
          <div style={{ display: "flex", gap: 6, marginTop: 28 }}>
            {[1, 0.5, 0.25].map((o, i) => (
              <div
                key={i}
                style={{
                  width: i === 0 ? 24 : 8,
                  height: 8,
                  borderRadius: 20,
                  background: `rgba(90,30,5,${o * 0.5})`,
                }}
              />
            ))}
          </div>
        </div>

        {/* Bottom trust badges */}
        <div style={{ display: "flex", gap: 16, position: "relative" }}>
          {[
            { icon: "✦", text: "5,000+ Patients" },
            { icon: "◈", text: "Certified Doctors" },
            { icon: "◇", text: "Secure & Private" },
          ].map((b) => (
            <div
              key={b.text}
              style={{
                display: "flex",
                alignItems: "center",
                gap: 6,
                padding: "6px 12px",
                borderRadius: 20,
                background: "rgba(255,255,255,0.25)",
                backdropFilter: "blur(8px)",
              }}
            >
              <span style={{ fontSize: 10, color: "#7a3e22" }}>{b.icon}</span>
              <span style={{ fontSize: 11, color: "#3a1a0a", fontWeight: 500 }}>{b.text}</span>
            </div>
          ))}
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
        <div style={{ width: "100%", maxWidth: 400 }}>

          {/* Mobile logo */}
          <div
            className="flex lg:hidden"
            style={{ alignItems: "center", gap: 8, marginBottom: 36 }}
          >
            <span style={{ fontSize: 18, color: "#b87c5a" }}>✦</span>
            <span
              style={{
                fontFamily: "'Playfair Display', Georgia, serif",
                fontSize: 18,
                color: "#2c1f1a",
              }}
            >
              Aura Clinic
            </span>
          </div>

          {/* Heading */}
          <div style={{ marginBottom: 36 }}>
            <p style={{ fontSize: 12, letterSpacing: "0.1em", textTransform: "uppercase", color: "#b87c5a", marginBottom: 8 }}>
              Welcome back
            </p>
            <h1
              style={{
                fontFamily: "'Playfair Display', Georgia, serif",
                fontSize: 32,
                fontWeight: 400,
                color: "#2c1f1a",
                margin: 0,
                lineHeight: 1.2,
              }}
            >
              Sign in to your
              <br />
              <em style={{ color: "#b87c5a" }}>account</em>
            </h1>
          </div>

          {/* Error */}
          {error && (
            <div
              style={{
                padding: "12px 16px",
                borderRadius: 10,
                background: "rgba(176,64,64,0.08)",
                border: "1px solid rgba(176,64,64,0.2)",
                color: "#b04040",
                fontSize: 13,
                marginBottom: 20,
                display: "flex",
                alignItems: "center",
                gap: 8,
              }}
            >
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                <circle cx="12" cy="12" r="10" /><line x1="12" y1="8" x2="12" y2="12" /><line x1="12" y1="16" x2="12.01" y2="16" />
              </svg>
              {error}
            </div>
          )}

          {/* Form */}
          <form onSubmit={handleSubmit} style={{ display: "flex", flexDirection: "column", gap: 16 }}>

            {/* Email */}
            <div>
              <label style={{ display: "block", fontSize: 12, fontWeight: 500, color: "#5a3e35", marginBottom: 7, letterSpacing: "0.03em" }}>
                Email address
              </label>
              <input
                type="email"
                name="email"
                value={form.email}
                onChange={handleChange}
                onFocus={() => setFocused("email")}
                onBlur={() => setFocused("")}
                placeholder="you@example.com"
                autoComplete="email"
                style={inputStyle("email")}
              />
            </div>

            {/* Password */}
            <div>
              <div style={{ display: "flex", justifyContent: "space-between", alignItems: "center", marginBottom: 7 }}>
                <label style={{ fontSize: 12, fontWeight: 500, color: "#5a3e35", letterSpacing: "0.03em" }}>
                  Password
                </label>
                <Link
                  to="/forgot-password"
                  style={{ fontSize: 12, color: "#b87c5a", textDecoration: "none" }}
                >
                  Forgot password?
                </Link>
              </div>
              <div style={{ position: "relative" }}>
                <input
                  type={showPass ? "text" : "password"}
                  name="password"
                  value={form.password}
                  onChange={handleChange}
                  onFocus={() => setFocused("password")}
                  onBlur={() => setFocused("")}
                  placeholder="Enter your password"
                  autoComplete="current-password"
                  style={{ ...inputStyle("password"), paddingRight: 46 }}
                />
                <button
                  type="button"
                  onClick={() => setShowPass(!showPass)}
                  style={{
                    position: "absolute",
                    right: 14,
                    top: "50%",
                    transform: "translateY(-50%)",
                    background: "none",
                    border: "none",
                    cursor: "pointer",
                    padding: 2,
                    color: "#b0907e",
                    display: "flex",
                    alignItems: "center",
                  }}
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
            </div>

            {/* Submit */}
            <button
              type="submit"
              disabled={loading}
              style={{
                width: "100%",
                padding: "14px",
                borderRadius: 12,
                border: "none",
                background: loading
                  ? "rgba(184,124,90,0.5)"
                  : "linear-gradient(135deg, #c4865f, #9a5030)",
                color: "#fff",
                fontSize: 14,
                fontWeight: 500,
                cursor: loading ? "not-allowed" : "pointer",
                transition: "all 0.25s ease",
                marginTop: 4,
                display: "flex",
                alignItems: "center",
                justifyContent: "center",
                gap: 8,
                fontFamily: "inherit",
                letterSpacing: "0.02em",
              }}
            >
              {loading ? (
                <>
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" style={{ animation: "spin 0.8s linear infinite" }}>
                    <path d="M21 12a9 9 0 1 1-6.219-8.56" />
                  </svg>
                  Signing in…
                </>
              ) : (
                <>Sign In <span style={{ fontSize: 16 }}>→</span></>
              )}
            </button>
          </form>

          {/* Divider */}
          <div style={{ display: "flex", alignItems: "center", gap: 12, margin: "24px 0" }}>
            <div style={{ flex: 1, height: 1, background: "rgba(184,124,90,0.15)" }} />
            <span style={{ fontSize: 12, color: "#b0907e" }}>or</span>
            <div style={{ flex: 1, height: 1, background: "rgba(184,124,90,0.15)" }} />
          </div>

          {/* Register link */}
          <p style={{ textAlign: "center", fontSize: 13, color: "#7a5a52" }}>
            Don't have an account?{" "}
            <Link
              to="/register"
              style={{ color: "#b87c5a", fontWeight: 500, textDecoration: "none" }}
            >
              Create one →
            </Link>
          </p>

          {/* Back to home */}
          <div style={{ textAlign: "center", marginTop: 32 }}>
            <Link
              to="/"
              style={{
                fontSize: 12,
                color: "#b0907e",
                textDecoration: "none",
                display: "inline-flex",
                alignItems: "center",
                gap: 4,
              }}
            >
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
        input::-webkit-input-placeholder { color: #c4a898; }
      `}</style>
    </div>
  );
}