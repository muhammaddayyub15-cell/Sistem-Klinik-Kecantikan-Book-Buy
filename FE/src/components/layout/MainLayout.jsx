import { useState, useEffect } from "react";
import { Outlet } from "react-router-dom";
import Navbar from "../ui/bar-side/Navbar";
import Sidebar from "../ui/bar-side/Sidebar";

/**
 * MainLayout — root layout for all authenticated routes.
 *
 * Handles:
 * - Mobile: sidebar slides in as overlay (open/close via Navbar hamburger)
 * - Desktop: sidebar always visible; collapsible to icon-only mode
 */
export default function MainLayout() {
  const [sidebarOpen, setSidebarOpen] = useState(false);   // mobile
  const [collapsed, setCollapsed] = useState(false);        // desktop

  const isDesktop = () => window.innerWidth >= 1024;

  // Close mobile sidebar on resize to desktop
  useEffect(() => {
    const onResize = () => {
      if (isDesktop()) setSidebarOpen(false);
    };
    window.addEventListener("resize", onResize);
    return () => window.removeEventListener("resize", onResize);
  }, []);

  const handleMenuToggle = () => {
    if (isDesktop()) {
      setCollapsed((c) => !c);
    } else {
      setSidebarOpen((o) => !o);
    }
  };

  const SIDEBAR_WIDTH = collapsed ? 64 : 240;

  return (
    <div style={{ minHeight: "100vh", background: "#faf8f5" }}>
      {/* Sidebar */}
      <Sidebar
        open={sidebarOpen}
        collapsed={collapsed}
        onClose={() => setSidebarOpen(false)}
      />

      {/* Top Navbar */}
      <Navbar
        onMenuToggle={handleMenuToggle}
        sidebarOpen={sidebarOpen || !collapsed}
      />

      {/* Main content area */}
      <main
        style={{
          paddingTop: 64,
          paddingLeft: 0,
          transition: "padding-left 0.3s ease",
        }}
        className="lg:pl-[var(--sidebar-width,240px)]"
      >
        <div
          style={{
            minHeight: "calc(100vh - 64px)",
            padding: "28px 28px",
          }}
        >
          <Outlet />
        </div>
      </main>
    </div>
  );
}