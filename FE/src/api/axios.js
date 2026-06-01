import axios from "axios";

const BASE_URL = import.meta.env.VITE_API_URL ?? "http://localhost:8000/api";
const STORAGE_KEY_TOKEN = "aura_token";

const api = axios.create({
  baseURL: BASE_URL,
  headers: { "Content-Type": "application/json" },
  timeout: 15000,
});

// Helper
export const getToken = () => localStorage.getItem(STORAGE_KEY_TOKEN);
export const setToken = (token) =>
  localStorage.setItem(STORAGE_KEY_TOKEN, token);
export const clearAuth = () => {
  localStorage.removeItem(STORAGE_KEY_TOKEN);
  localStorage.removeItem("aura_user");
};

// Request
api.interceptors.request.use((config) => {
  const token = getToken();
  if (token) config.headers.Authorization = `Bearer ${token}`;
  return config;
});

// Response
api.interceptors.response.use(
  (res) => res,
  (error) => {
    if (error.response?.status === 401) {
      clearAuth();
      window.dispatchEvent(new Event("unauthorized"));
    }

    error.normalizedMessage =
      error.response?.data?.message ||
      "Terjadi kesalahan. Silakan coba lagi.";

    return Promise.reject(error);
  }
);

export default api;