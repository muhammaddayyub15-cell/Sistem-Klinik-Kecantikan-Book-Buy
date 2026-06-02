import { createContext, useContext, useState, useEffect, useCallback } from "react";
import { login as apiLogin, register as apiRegister, logout as apiLogout, getMe } from "../api/authApi";

const AuthContext = createContext(null);

const STORAGE_KEY_TOKEN = "aura_token";
const STORAGE_KEY_USER = "aura_user";

export function AuthProvider({ children }) {
  const [user, setUser] = useState(null);
  const [token, setToken] = useState(null);
  const [isLoading, setIsLoading] = useState(true);

  useEffect(() => {
    console.log("AuthContext useEffect running");
    // ── Restore session dari localStorage ──────────────────────────────────
    try {
      const storedToken = localStorage.getItem(STORAGE_KEY_TOKEN);
      const storedUser = localStorage.getItem(STORAGE_KEY_USER);

      console.log("storedToken:", storedToken); // ← tambah ini
      console.log("storedUser:", storedUser);   // ← tambah ini

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

    // ── Listen event unauthorized dari axios interceptor ───────────────────
    // Dipanggil ketika refresh token gagal — clear session,
    // ProtectedRoute otomatis redirect ke /login karena isAuthenticated = false
    const handleUnauthorized = () => {
      setUser(null);
      setToken(null);
      localStorage.removeItem(STORAGE_KEY_TOKEN);
      localStorage.removeItem(STORAGE_KEY_USER);
    };

    window.addEventListener("unauthorized", handleUnauthorized);

    // ← cleanup ini sekarang TERDAFTAR dengan benar
    return () => {
      window.removeEventListener("unauthorized", handleUnauthorized);
    };
  }, []);

  // ── Helper: simpan ke state + localStorage ─────────────────────────────
  const persistSession = useCallback((userData, tokenValue) => {
    setUser(userData);
    setToken(tokenValue);
    localStorage.setItem(STORAGE_KEY_TOKEN, tokenValue);
    localStorage.setItem(STORAGE_KEY_USER, JSON.stringify(userData));
  }, []);

  // ── login ──────────────────────────────────────────────────────────────
  const login = useCallback(async (email, password) => {
    const res = await apiLogin({ email, password });
    console.log("res.data:", res.data);
    console.log("res.data.data:", res.data.data);
    const { user: userData, token: tokenValue } = res.data.data;
    console.log("userData:", userData);
    console.log("tokenValue:", tokenValue);
    persistSession(userData, tokenValue);
    return userData;
  }, [persistSession]);

  // ── register ───────────────────────────────────────────────────────────
  const register = useCallback(async (data) => {
    const res = await apiRegister(data);
    const { user: userData, token: tokenValue } = res.data.data;
    persistSession(userData, tokenValue);
    return userData;
  }, [persistSession]);

  // ── logout ─────────────────────────────────────────────────────────────
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