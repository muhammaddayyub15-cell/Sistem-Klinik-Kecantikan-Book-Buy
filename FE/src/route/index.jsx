<Routes>
  {/* Public */}
  <Route path="/" element={<HomePage />} />
  <Route path="/login" element={<LoginPage />} />
  <Route path="/register" element={<RegisterPage />} />

  {/* Patient */}
  <Route element={<ProtectedRoute allowedRoles={['patient']} />}>
    <Route path="/patient/dashboard" element={<PatientDashboard />} />
    <Route path="/patient/booking" element={<BookingPage />} />
    <Route path="/patient/order" element={<OrderPage />} />
  </Route>

  {/* Doctor */}
  <Route element={<ProtectedRoute allowedRoles={['doctor']} />}>
    <Route path="/doctor/dashboard" element={<DoctorDashboard />} />
    <Route path="/doctor/records" element={<RecordPage />} />
  </Route>

  {/* Admin */}
  <Route element={<ProtectedRoute allowedRoles={['admin']} />}>
    <Route path="/admin/dashboard" element={<AdminDashboard />} />
    <Route path="/admin/doctors" element={<DoctorPage />} />
    <Route path="/admin/services" element={<ServicePage />} />
    <Route path="/admin/products" element={<ProductPage />} />
  </Route>
</Routes>