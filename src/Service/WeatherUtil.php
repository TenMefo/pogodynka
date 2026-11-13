<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Location;
use App\Entity\Measurement;
use App\Repository\LocationRepository;
use App\Repository\MeasurementRepository;

class WeatherUtil
{
    public function __construct(
        private readonly LocationRepository $locations,
        private readonly MeasurementRepository $measurements,
    ) {
    }

    /** @return Measurement[] */
    public function getWeatherForLocation(Location $location): array
    {
        return $this->measurements->findByLocation($location);
    }

    /** @return Measurement[] */
    public function getWeatherForCountryAndCity(string $countryCode, string $city): array
    {
        $location = $this->locations->findOneBy([
            'country' => strtoupper($countryCode),
            'city' => $city,
        ]);

        if ($location === null) {
            throw new \DomainException(sprintf('Nie znaleziono miejscowości %s (%s).', $city, $countryCode));
        }

        return $this->getWeatherForLocation($location);
    }
}
