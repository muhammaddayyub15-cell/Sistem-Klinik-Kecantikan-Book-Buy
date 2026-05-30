import axios from "axios";

// ─── Konstanta ────────────────────────────────────────────────────────────
// [NOTE] Ganti VITE_API_URL di .env sesuai environment.
//        Contoh: VITE_API_URL=http://localhost:3000/api
const BASE_URL = import.meta.env.VITE_API_URL ?? "http://localhost:3000/api";

// Storage key harus sama dengan yang dipakai di AuthContext.
// [NOTE] Jika key diubah di AuthContext, update juga di sini.
const STORAGE_KEY_TOKEN = "aura_token";

// ─── Instance ─────────────────────────────────────────────────────────────
const api = axios.create({
  baseURL: BASE_URL,
  headers: {
    "Content-Type": "application/json",
  },
  // Timeout 10 detik. Naikkan jika ada endpoint yang lambat (misal upload file).
  timeout: 10_000,
});

// ─── Request Interceptor ──────────────────────────────────────────────────
// Otomatis sisipkan Bearer token dari localStorage ke setiap request.
api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem(STORAGE_KEY_TOKEN);
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => Promise.reject(error)
);

// ─── Response Interceptor ─────────────────────────────────────────────────
api.interceptors.response.use(
  // Success: teruskan response tanpa modifikasi.
  (response) => response,

  (error) => {
    // [NOTE] 401 = token expired atau tidak valid.
    //        Jika menerima 401, hapus session karena kemungkinan sudah tidak valid.
    if (error.response?.status === 401) {
      localStorage.removeItem(STORAGE_KEY_TOKEN);
      localStorage.removeItem("aura_user");
    }

    // Normalisasi pesan error agar konsisten di seluruh app.

    const message =
      error.response?.data?.message ??
      error.message ??
      "Terjadi kesalahan. Silakan coba lagi.";

    // Attach pesan error yang sudah di-normalize ke error object
    // agar mudah diakses di hooks: catch (err) { err.normalizedMessage }
    error.normalizedMessage = message;

    return Promise.reject(error);
  }
);

export default api;