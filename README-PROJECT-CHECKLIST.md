# 📊 Project Tracker Checklist - Core Service

## ✅ Status Legend
- ✅ Done
- ⚠️ Partial
- ❌ Not Started

---

## 📁 Controllers
- AuthController.php → ✅
- BookingController.php → ✅
- DoctorController.php → ✅
- MedicalController.php → ✅
- PatientController.php → ⚠️
- ServiceController.php → ⚠️

---

## 📁 Request Validation
- LoginRequest.php → ✅
- RegisterRequest.php → ✅
- StoreBookingRequest.php → ✅
- UpdateBookingStatusRequest.php → ✅
- StoreDoctorRequest.php → ✅
- UpdateDoctorRequest.php → ✅
- StorePatientRequest.php → ⚠️
- UpdatePatientRequest.php → ⚠️
- StoreMedicalRecordRequest.php → ❌
- StorePrescriptionRequest.php → ❌

---

## 📁 Models
- User.php → ✅
- Patient.php → ✅
- Doctor.php → ✅
- DoctorSchedule.php → ✅
- Specialization.php → ✅
- Booking.php → ✅
- MedicalRecord.php → ⚠️
- Prescription.php → ❌
- Service.php → ⚠️
- ServiceCategory.php → ⚠️

---

## 📁 Repositories
- BookingRepository.php → ✅
- DoctorRepository.php → ✅
- MedicalRepository.php → ❌
- PatientRepository.php → ⚠️
- ServiceRepository.php → ⚠️
- UserRepository.php → ✅

---

## 📁 Services
- AuthService.php → ✅
- BookingService.php → ✅
- DoctorService.php → ✅
- MedicalService.php → ❌
- PatientService.php → ⚠️
- ServiceService.php → ⚠️
- UserService.php → ✅

---

## 📁 Events
- BookingCreated.php → ✅

---

## 📁 Middleware
- AuthenticateApi.php → ✅
- RoleMiddleware.php → ✅

---

## 📁 Database
- Users → ✅
- Patients → ⚠️
- Doctors → ⚠️
- Bookings → ⚠️
- Medical Records → ⚠️
- Prescriptions → ❌
- Services → ⚠️
- Seeder → ❌

---

# 📘 Project Summary

## Progress
**~60% Completed**

## 🔁 Main Flow

### Booking Flow
User → Booking → Doctor & Service → Validation → Save → Event

### Medical Flow
Booking selesai → Input medical record → Tambah prescription

### Auth Flow
Register → Login → Token → Middleware

---

## 🚨 Priority Fix
1. Relasi database (FK & UNIQUE)
2. Booking conflict validation
3. Medical record flow
4. Role-based access

---

## 🚀 Next Steps
1. Fix DB & Seeder
2. Improve Booking Logic
3. Implement Medical Flow
4. Refactor Auth & Role
