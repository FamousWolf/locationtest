<?php

namespace App\Service;

use App\Entity\Location;
use App\Repository\LocationRepository;

class LocationService
{
    public function __construct(
        protected LocationRepository $locationRepository
    )
    {}

    /**
     * Add a new location
     *
     * @param string $name
     * @param float $latitude
     * @param float $longitude
     * @return Location
     */
    public function addLocation(string $name, float $latitude, float $longitude): Location
    {
        $location = new Location();
        $location->setName($name);
        $location->setLatitude($latitude);
        $location->setLongitude($longitude);

        $this->locationRepository->add($location);

        return $location;
    }
}