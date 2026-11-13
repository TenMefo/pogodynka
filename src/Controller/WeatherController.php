<?php

namespace App\Controller;

use App\Repository\LocationRepository;
use App\Service\WeatherUtil;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class WeatherController extends AbstractController
{
    #[Route('/weather', name: 'app_weather_index', methods: ['GET'])]
    public function index(LocationRepository $locations): Response
    {
        return $this->render('weather/index.html.twig', [
            'locations' => $locations->findBy([], ['city' => 'ASC', 'country' => 'ASC']),
        ]);
    }

    #[Route(
        '/weather/{country}/{city}',
        name: 'app_weather',
        requirements: ['country' => '[A-Z]{2}']
    )]
    public function city(
        string $country,
        string $city,
        LocationRepository $locations,
        WeatherUtil $weather
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
        $forecast = $weather->getWeatherForLocation($location);

        // Przekaż obie zmienne do widoku.
        return $this->render('weather/city.html.twig', [
            'location' => $location,
            'measurements' => $forecast,
        ]);
    }
}
