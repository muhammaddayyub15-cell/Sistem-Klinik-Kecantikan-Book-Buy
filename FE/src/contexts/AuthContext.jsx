import { createContext, useContext, useState, useEffect, useCallback } from "react";
import { login as apiLogin, register as apiRegister, logout as apiLogout, refreshToken as apiRefreshToken } from "../api/authApi";

const AuthContext = createContext(null);

// ─── Storage keys ─────────────────────────────────────────────────────────
// Konstanta agar tidak ada typo jika key perlu diubah di masa depan.
const STORAGE_KEY_TOKEN = "aura_token";
const STORAGE_KEY_USER  = "aura_user";

// ─── Provider ─────────────────────────────────────────────────────────────

export function AuthProvider({ children }) {
  const [user,      setUser]      = useState(null);
  const [token,     setToken]     = useState(null);


  //        Tanpa ini, user yang refresh halaman akan selalu di-kick ke /login.
  const [isLoading, setIsLoading] = useState(true);

  // ── Inisialisasi: restore session dari localStorage ──────────────────────
  useEffect(() => {
    const restore = async () => {
      try {
        const storedToken = localStorage.getItem(STORAGE_KEY_TOKEN);
        const storedUser  = localStorage.getItem(STORAGE_KEY_USER);

        if (storedToken && storedUser) {
          // [NOTE] Coba refresh token untuk memastikan session masih valid.
          //        Jika endpoint /auth/refresh tidak ada, hapus blok try-catch
          //        ini dan langsung set dari storage (less secure tapi lebih simple).
          try {
            const res = await apiRefreshToken(storedToken);
            const freshToken = res.data?.token ?? storedToken;

            setToken(freshToken);
            setUser(JSON.parse(storedUser));
            localStorage.setItem(STORAGE_KEY_TOKEN, freshToken);
          } catch {
            // [NOTE] Refresh gagal (token expired) → bersihkan storage.
            //        User akan diarahkan ke /login oleh ProtectedRoute.
            clearStorage();
          }
        }
      } catch (err) {
        console.error("[AuthContext] Failed to restore session:", err);
        clearStorage();
      } finally {
        setIsLoading(false);
      }
    };

    restore();
  }, []);

  // ── Helper: simpan ke state + localStorage ───────────────────────────────
  const persistSession = useCallback((userData, tokenValue) => {
    setUser(userData);
    setToken(tokenValue);
    localStorage.setItem(STORAGE_KEY_TOKEN, tokenValue);
    localStorage.setItem(STORAGE_KEY_USER, JSON.stringify(userData));
  }, []);

  // ── Helper: hapus session ────────────────────────────────────────────────
  const clearStorage = () => {
    localStorage.removeItem(STORAGE_KEY_TOKEN);
    localStorage.removeItem(STORAGE_KEY_USER);
  };

  // ── login ────────────────────────────────────────────────────────────────
  const login = useCallback(async (email, password) => {
    const res = await apiLogin({ email, password });
    const { user: userData, token: tokenValue } = res.data;
    persistSession(userData, tokenValue);
  }, [persistSession]);

  // ── register ─────────────────────────────────────────────────────────────
  const register = useCallback(async (data) => {
    const res = await apiRegister(data);
    const { user: userData, token: tokenValue } = res.data;
    persistSession(userData, tokenValue);
  }, [persistSession]);

  // ── logout ───────────────────────────────────────────────────────────────
  const logout = useCallback(async () => {
    try {
      await apiLogout();
    } catch {
        // [NOTE] Logout API gagal (misal karena token expired) → tetap bersihkan session.
    } finally {
      setUser(null);
      setToken(null);
      clearStorage();
    }
  }, []);

  // ── Nilai yang di-expose ke consumers ────────────────────────────────────
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

// ─── Custom hook ──────────────────────────────────────────────────────────
// Custom hook untuk akses context dengan lebih nyaman di komponen lain.
export const useAuth = () => {
  const ctx = useContext(AuthContext);
  if (!ctx) {
    throw new Error("useAuth must be used within <AuthProvider>. Wrap your app with <AuthProvider> in main.jsx.");
  }
  return ctx;
};