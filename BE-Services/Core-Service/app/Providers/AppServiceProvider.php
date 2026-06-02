<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// -- Import semua Model --
use App\Models\Booking\Booking;
use App\Models\Doctor\Doctor;
use App\Models\Medical\MedicalRecord;
use App\Models\Medical\Prescription;
use App\Models\Patient\Patient;
use App\Models\Service\Service;
use App\Models\User\User;

// -- Import semua Repository --
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

// -- Import Event & Listener --
use App\Events\Booking\BookingCreated;
use App\Listeners\SendBookingToOrderListener;

class AppServiceProvider extends ServiceProvider
{
    protected $listen = [
        BookingCreated::class => [
            SendBookingToOrderListener::class,
        ],
    ];

    public function register(): void
    {
        // ── BINDING REPOSITORY ──────────────────────────────────────

        $this->app->singleton(BookingRepository::class, function () {
            return new BookingRepository(new Booking());
        });

        $this->app->singleton(DoctorRepository::class, function () {
            return new DoctorRepository(new Doctor());
        });

        $this->app->bind(\App\Service\Repositories\ScheduleRepository::class,
            fn ($app) => new \App\Service\Repositories\ScheduleRepository(
                new \App\Models\Doctor\DoctorSchedule()
        ));

        $this->app->singleton(MedicalRepository::class, function () {
            return new MedicalRepository(new MedicalRecord(), new Prescription());
        });

        $this->app->singleton(PatientRepository::class, function () {
            return new PatientRepository(new Patient());
        });

        $this->app->singleton(ServiceRepository::class, function () {
            return new ServiceRepository(new Service());
        });

        $this->app->singleton(UserRepository::class, function () {
            return new UserRepository(new User());
        });

        // ── BINDING SERVICE ──────────────────────────────────────────

        $this->app->singleton(AuthService::class, function ($app) {
            return new AuthService(
                $app->make(UserRepository::class)
            );
        });

        $this->app->singleton(BookingService::class, function ($app) {
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

        $this->app->bind(\App\Service\ScheduleService::class,
            fn ($app) => new \App\Service\ScheduleService(
                $app->make(\App\Service\Repositories\ScheduleRepository::class),
                $app->make(\App\Service\Repositories\DoctorRepository::class),
            ));

        $this->app->bind(\App\Service\BookingService::class,
            fn ($app) => new \App\Service\BookingService(
                $app->make(\App\Service\Repositories\BookingRepository::class),
                $app->make(\App\Service\Repositories\ScheduleRepository::class),
            ));

        $this->app->singleton(MedicalService::class, function ($app) {
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
            return new ServiceService(
                $app->make(ServiceRepository::class)
            );
        });

        $this->app->singleton(UserService::class, function ($app) {
            return new UserService(
                $app->make(UserRepository::class)
            );
        });

        $this->app->singleton(SendBookingToOrderService::class, function () {
            return new SendBookingToOrderService();
        });
    }

    public function boot(): void
    {
        //
    }
}