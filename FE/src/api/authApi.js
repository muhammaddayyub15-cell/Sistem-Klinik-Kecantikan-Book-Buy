import api from "./axios";

// ─── Auth API ─────────────────────────────────────────────────────────────
//        Semua fungsi return raw axios response.
//        Destructuring res.data dilakukan di AuthContext, bukan di sini.
//
// Payload yang diharapkan dari backend:
//   POST /auth/login    → { user: { id, name, email, role }, token: string }
//   POST /auth/register → { user: { id, name, email, role }, token: string }
//   POST /auth/logout   → 200 OK (body diabaikan)
//   POST /auth/refresh  → { token: string }

// ── Login ─────────────────────────────────────────────────────────────────
// @param {{ email: string, password: string }} credentials
export const login = (credentials) =>
  api.post("/auth/login", credentials);

// ── Register ──────────────────────────────────────────────────────────────
// @param {{ name: string, email: string, password: string, role?: string }} data
// role default ke 'patient' di backend jika tidak dikirim.
export const register = (data) =>
  api.post("/auth/register", data);

// ── Logout ───────────────────────────────────────────────────────────────
//        Backend diharapkan invalidate token di sisi server (blacklist/revoke).
//        Jika backend tidak punya endpoint logout, fungsi ini tetap
//        dipanggil oleh AuthContext — error-nya akan di-catch dan diabaikan.
export const logout = () =>
  api.post("/auth/logout");

// ── Refresh Token ─────────────────────────────────────────────────────────
// @param {string} currentToken — token lama yang akan di-refresh
//    Dikirim lewat Authorization header oleh axios interceptor secara otomatis.
//        Parameter currentToken di sini hanya untuk eksplisitness;
//        backend bisa baca dari header atau body sesuai implementasi.
export const refreshToken = (currentToken) =>
  api.post("/auth/refresh", { token: currentToken });