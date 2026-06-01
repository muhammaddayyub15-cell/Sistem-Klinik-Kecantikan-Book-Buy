import { createContext, useContext, useState, useEffect, useCallback } from "react";
import { login as apiLogin, register as apiRegister, logout as apiLogout, getMe } from "../api/authApi";

// [FIX] apiRefreshToken dihapus — Sanctum token-based tidak punya endpoint refresh.

const AuthContext = createContext(null);

const STORAGE_KEY_TOKEN = "aura_token";
const STORAGE_KEY_USER  = "aura_user";

export function AuthProvider({ children }) {
  const [user,      setUser]      = useState(null);
  const [token,     setToken]     = useState(null);
  const [isLoading, setIsLoading] = useState(true);

  // ── Inisialisasi: restore session dari localStorage ──────────────────────
  // [FIX] Tidak lagi async + tidak panggil apiRefreshToken.
  // Sanctum token valid sampai di-revoke oleh backend (logout).
  // Validasi token dilakukan implisit: jika 401, interceptor axios
  // akan clear storage dan redirect ke /login secara otomatis.
  useEffect(() => {
    try {
      const storedToken = localStorage.getItem(STORAGE_KEY_TOKEN);
      const storedUser  = localStorage.getItem(STORAGE_KEY_USER);

      if (storedToken && storedUser) {
        setToken(storedToken);
        setUser(JSON.parse(storedUser));
        getMe().catch(() => {
          localStorage.removeItem(STORAGE_KEY_TOKEN);
          localStorage.removeItem(STORAGE_KEY_USER);
          setUser(null);
          setToken(null);
        });

      }
    } catch (err) {
      console.error("[AuthContext] Failed to restore session:", err);
      localStorage.removeItem(STORAGE_KEY_TOKEN);
      localStorage.removeItem(STORAGE_KEY_USER);
    } finally {
      setIsLoading(false);
    }
  }, []);

  // ── Helper: simpan ke state + localStorage ───────────────────────────────
  const persistSession = useCallback((userData, tokenValue) => {
    setUser(userData);
    setToken(tokenValue);
    localStorage.setItem(STORAGE_KEY_TOKEN, tokenValue);
    localStorage.setItem(STORAGE_KEY_USER, JSON.stringify(userData));
  }, []);

  // ── login ────────────────────────────────────────────────────────────────
  // [FIX] Return userData agar LoginPage bisa baca .role untuk redirect.
  // LoginPage cukup: const userData = await login(email, password)
  const login = useCallback(async (email, password) => {
    const res = await apiLogin({ email, password });
    const { user: userData, token: tokenValue } = res.data;
    persistSession(userData, tokenValue);
    return userData;
  }, [persistSession]);

  // ── register ─────────────────────────────────────────────────────────────
  // [FIX] Return userData agar RegisterPage bisa baca .role untuk redirect.
  const register = useCallback(async (data) => {
    const res = await apiRegister(data);
    const { user: userData, token: tokenValue } = res.data;
    persistSession(userData, tokenValue);
    return userData;
  }, [persistSession]);

  // ── logout ───────────────────────────────────────────────────────────────
  const logout = useCallback(async () => {
    try {
      await apiLogout();
    } catch {
      // Token sudah expired di server — tetap bersihkan session di client.
    } finally {
      setUser(null);
      setToken(null);
      localStorage.removeItem(STORAGE_KEY_TOKEN);
      localStorage.removeItem(STORAGE_KEY_USER);
    }
  }, []);

  const value = {
    user,
    token,
    isAuthenticated: !!user && !!token,
    isLoading,
    login,
    register,
    logout,
  };

  return (
    <AuthContext.Provider value={value}>
      {children}
    </AuthContext.Provider>
  );
}

export const useAuth = () => {
  const ctx = useContext(AuthContext);
  if (!ctx) {
    throw new Error("useAuth must be used within <AuthProvider>. Wrap your app with <AuthProvider> in main.jsx.");
  }
  return ctx;
};