<?php

namespace App\Events\Booking;

use App\Models\Booking\Booking;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookingCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    // tambah property booking agar listener bisa akses data booking
    public Booking $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }
}