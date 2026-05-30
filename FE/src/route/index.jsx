import { lazy, Suspense } from "react";
import { Routes, Route, Navigate } from "react-router-dom";
import ProtectedRoute from "./ProtectedRoute";

// ─── Layout ───────────────────────────────────────────────────────────────
import MainLayout from "../components/layout/MainLayout";

// ─── Lazy imports ─────────────────────────────────────────────────────────
// [NOTE] Semua page di-lazy load agar bundle awal lebih kecil.
//        Hanya halaman yang benar-benar diakses user yang akan di-download.
//        Jika ada page yang sangat sering diakses (misal DashboardPage),
//        bisa di-eager import — tapi lazy dulu sampai ada masalah performa nyata.

// Public
const HomePage     = lazy(() => import("../pages/auth/HomePage"));
const AboutPage    = lazy(() => import("../pages/AboutPage"));
const ProductsPage = lazy(() => import("../pages/ProductsPage"));

// [NOTE] LoginPage dan RegisterPage diletakkan di pages/auth/ sesuai
//        struktur direktori di FRONTEND_ARCHITECTURE.md.
const LoginPage    = lazy(() => import("../pages/auth/LoginPage"));
const RegisterPage = lazy(() => import("../pages/auth/RegisterPage"));

// Patient
const PatientDashboard = lazy(() => import("../pages/patient/DashboardPage"));
const BookingPage      = lazy(() => import("../pages/patient/BookingPage"));
const OrderPage        = lazy(() => import("../pages/patient/OrderPage"));

// Doctor
const DoctorDashboard = lazy(() => import("../pages/doctor/DashboardPage"));
const RecordPage      = lazy(() => import("../pages/doctor/RecordPage"));

// Admin
const AdminDashboard = lazy(() => import("../pages/admin/DashboardPage"));
const DoctorPage     = lazy(() => import("../pages/admin/DoctorPage"));
const ServicePage    = lazy(() => import("../pages/admin/ServicePage"));
const ProductPage    = lazy(() => import("../pages/admin/ProductPage"));

// ─── Fallback saat lazy load ──────────────────────────────────────────────
// [NOTE] Suspense fallback ini hanya muncul saat chunk JS page sedang di-download.
//        Bukan pengganti loading state di dalam page itu sendiri.
//        Ganti dengan komponen LoadingSpinner jika sudah dibuat.
const PageLoader = () => (
  <div style={{ display: "grid", placeItems: "center", minHeight: "100vh" }}>
    <span>Loading...</span>
  </div>
);

// ─── AppRoutes ────────────────────────────────────────────────────────────
const AppRoutes = () => {
  return (
    <Suspense fallback={<PageLoader />}>
      <Routes>

        {/* ── Public ──────────────────────────────────────────────────── */}
        <Route path="/"         element={<HomePage />} />
        <Route path="/about"    element={<AboutPage />} />       {/* [FIX] tambah */}
        <Route path="/products" element={<ProductsPage />} />   {/* [FIX] tambah */}
        <Route path="/login"    element={<LoginPage />} />
        <Route path="/register" element={<RegisterPage />} />

        {/* ── Authenticated routes — semua pakai MainLayout ────────────
            [NOTE] MainLayout (Navbar + Sidebar + Outlet) di-wrap di sini,
                   bukan di dalam ProtectedRoute, agar layout hanya render
                   sekali untuk semua protected route tanpa remount.
                   Urutan nesting: ProtectedRoute → MainLayout → page.        */}

        {/* Patient */}
        <Route element={<ProtectedRoute allowedRoles={["patient"]} />}>
          <Route element={<MainLayout />}>
            <Route path="/patient/dashboard" element={<PatientDashboard />} />
            <Route path="/patient/booking"   element={<BookingPage />} />
            <Route path="/patient/order"     element={<OrderPage />} />
          </Route>
        </Route>

        {/* Doctor */}
        <Route element={<ProtectedRoute allowedRoles={["doctor"]} />}>
          <Route element={<MainLayout />}>
            <Route path="/doctor/dashboard" element={<DoctorDashboard />} />
            <Route path="/doctor/records"   element={<RecordPage />} />
          </Route>
        </Route>

        {/* Admin */}
        <Route element={<ProtectedRoute allowedRoles={["admin"]} />}>
          <Route element={<MainLayout />}>
            <Route path="/admin/dashboard" element={<AdminDashboard />} />
            <Route path="/admin/doctors"   element={<DoctorPage />} />
            <Route path="/admin/services"  element={<ServicePage />} />
            <Route path="/admin/products"  element={<ProductPage />} />
          </Route>
        </Route>

        {/* ── Catch-all: redirect ke home ──────────────────────────────
            [NOTE] Tangkap semua path yang tidak dikenal daripada blank page.
                   Ganti dengan <NotFoundPage /> jika sudah dibuat.          */}
        <Route path="*" element={<Navigate to="/" replace />} />

      </Routes>
    </Suspense>
  );
};

export default AppRoutes;