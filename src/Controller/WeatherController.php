<?php

namespace App\Controller;

use App\Repository\LocationRepository;
use App\Repository\MeasurementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class WeatherController extends AbstractController
{
    #[Route(
        '/weather/{country}/{city}',
        name: 'app_weather',
        requirements: ['country' => '[A-Z]{2}']
    )]
    public function city(
        string $country,
        string $city,
        LocationRepository $locations,
        MeasurementRepository $measurements
    ): Response {
        // Znajdź miejscowość w bazie.
        $location = $locations->findOneBy([
            'city' => $city,
            'country' => $country,
        ]);

        // Jeśli nie istnieje, wyświetl błąd 404.
        if ($location === null) {
            throw $this->createNotFoundException(
                'Nie znaleziono miejscowości.'
            );
        }

        // Pobierz prognozę dla znalezionego miasta.
        $forecast = $measurements->findByLocation($location);

        // Przekaż obie zmienne do widoku.
        return $this->render('weather/city.html.twig', [
            'location' => $location,
            'measurements' => $forecast,
        ]);
    }
}
