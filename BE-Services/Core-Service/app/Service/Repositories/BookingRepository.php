<?php

namespace App\Service\Repositories;

use App\Models\Booking\Booking;
use App\Shared\Base\BaseRepository;

class BookingRepository extends BaseRepository
{
    public function __construct(Booking $booking)
    {
        parent::__construct($booking);
    }
}
