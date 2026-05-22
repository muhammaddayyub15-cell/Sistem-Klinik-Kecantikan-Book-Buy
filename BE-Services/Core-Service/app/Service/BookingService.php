<?php

namespace App\Service;

use App\Service\Repositories\BookingRepository;
use App\Shared\Base\BaseService;

class BookingService extends BaseService
{
    public function __construct(BookingRepository $bookingRepository)
    {
        parent::__construct($bookingRepository);
    }
}
