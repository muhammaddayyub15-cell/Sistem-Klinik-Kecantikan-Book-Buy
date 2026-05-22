<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Service\Repositories\BookingRepository;
use App\Service\Repositories\DoctorRepository;
use App\Service\Repositories\MedicalRepository;
use App\Service\Repositories\PatientRepository;
use App\Service\Repositories\ServiceRepository;
use App\Service\Repositories\UserRepository;

// -- Import semua Service --
use App\Service\AuthService;
use App\Service\BookingService;
use App\Service\DoctorService;
use App\Service\MedicalService;
use App\Service\PatientService;
use App\Service\ServiceService;
use App\Service\UserService;
use App\Service\SendBookingToOrderService;
use App\Events\Booking\BookingCreated;
use App\Listeners\SendBookingToOrderListener;
use App\Shared\Base\BaseRepository;
use App\Shared\Base\BaseService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * STATUS: Event BookingCreated sudah ada
     * CATATAN: Jika listener SendBookingToOrderListener belum dibuat,
     *          jalankan: php artisan make:listener SendBookingToOrderListener
     *          lalu isi dengan logika memanggil SendBookingToOrderService.
     */
    protected $listen = [
        BookingCreated::class => [
            SendBookingToOrderListener::class,
        ],
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        // ── BINDING REPOSITORY ──────────────────────────────────────
        // STATUS: Semua Repository sudah ada di app/Service/Repositories/
        // POLA: $this->app->singleton(Abstract::class, Concrete::class)
        //       Singleton = satu instance digunakan sepanjang request lifecycle

        $this->app->singleton(BookingRepository::class, function ($app) {
            // BookingRepository butuh model Booking
            // ERD: BOOKINGS table → doctor_id, patient_id, docsched_id, service_id
            return new BookingRepository();
        });

        $this->app->singleton(DoctorRepository::class, function ($app) {
            // ERD: DOCTORS table → user_id, spec_id, license_no, is_available
            return new DoctorRepository();
        });

        $this->app->singleton(MedicalRepository::class, function ($app) {
            // ERD: MEDICAL_RECORDS + PRESCRIPTIONS table
            return new MedicalRepository();
        });

        $this->app->singleton(PatientRepository::class, function ($app) {
            // ERD: PATIENTS table → user_id, dob, gender, blood_type, address
            return new PatientRepository();
        });

        $this->app->singleton(ServiceRepository::class, function ($app) {
            // ERD: SERVICES + SERVICES_CATEGORIES table
            return new ServiceRepository();
        });

        $this->app->singleton(UserRepository::class, function ($app) {
            // ERD: USERS table → full_name, email, phone, role, password
            return new UserRepository();
        });

        // ── BINDING SERVICE ──────────────────────────────────────────
        // STATUS: Semua Service sudah ada di app/Service/
        // CATATAN: Service di-inject Repository via constructor,
        //          pastikan constructor di BaseService menerima Repository.

        $this->app->singleton(AuthService::class, function ($app) {
            // AuthService butuh UserRepository untuk validasi login/register
            return new AuthService(
                $app->make(UserRepository::class)
            );
        });

        $this->app->singleton(BookingService::class, function ($app) {
            // BookingService butuh BookingRepository + DoctorRepository
            // (untuk cek jadwal dokter saat booking)
            return new BookingService(
                $app->make(BookingRepository::class),
                $app->make(DoctorRepository::class)
            );
        });

        $this->app->singleton(DoctorService::class, function ($app) {
            return new DoctorService(
                $app->make(DoctorRepository::class)
            );
        });

        $this->app->singleton(MedicalService::class, function ($app) {
            // MedicalService butuh MedicalRepository + BookingRepository
            // (untuk validasi booking sebelum buat medical record)
            return new MedicalService(
                $app->make(MedicalRepository::class),
                $app->make(BookingRepository::class)
            );
        });

        $this->app->singleton(PatientService::class, function ($app) {
            return new PatientService(
                $app->make(PatientRepository::class)
            );
        });

        $this->app->singleton(ServiceService::class, function ($app) {
            // CATATAN: Nama "ServiceService" memang terkesan ganda,
            // ini karena entity-nya bernama "Service" (layanan klinik).
            // Pertimbangkan rename menjadi "ClinicServiceService" atau
            // "TreatmentService" untuk kejelasan di masa mendatang.
            return new ServiceService(
                $app->make(ServiceRepository::class)
            );
        });

        $this->app->singleton(UserService::class, function ($app) {
            return new UserService(
                $app->make(UserRepository::class)
            );
        });

        // ── BINDING SEND BOOKING TO ORDER SERVICE ───────────────────
        // STATUS: File SendBookingToOrderService.php sudah dibuat
        // FUNGSI: Service ini dipanggil saat event BookingCreated fired,
        //         untuk mengirim data booking ke Order & Payment Service.
        // ARSITEKTUR: Core Service DB → (HTTP/Queue) → Order & Payment Service DB
        //             Lihat ERD: BOOKINGS → ORDERS (booking_id sebagai FK)

        $this->app->singleton(SendBookingToOrderService::class, function ($app) {
            return new SendBookingToOrderService();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * STATUS: Tidak ada konfigurasi khusus saat ini
     * CATATAN: Tambahkan logic di sini jika butuh setup
     *          setelah semua service ter-register, misalnya:
     *          - Mendaftarkan custom validation rules
     *          - Setup observer untuk model
     *          - Konfigurasi Eloquent global scopes
     */
    public function boot(): void
    {
        // -- Contoh: Register Observer jika dibutuhkan --
        // \App\Models\Booking\Booking::observe(\App\Observers\BookingObserver::class);
    }
}