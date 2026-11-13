<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Measurement;
use App\Service\WeatherUtil;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

final class WeatherApiController extends AbstractController
{
    #[Route('/api/v1/weather', name: 'app_weather_api', methods: ['GET'])]
    public function index(
        WeatherUtil $weather,
        #[MapQueryParameter] string $country,
        #[MapQueryParameter] string $city,
        #[MapQueryParameter] string $format = 'json',
        #[MapQueryParameter] bool $twig = false,
    ): Response {
        if (!in_array($format, ['json', 'csv'], true)) {
            return $this->json(['error' => 'Dozwolone formaty to json i csv.'], 400);
        }

        try {
            $measurements = $weather->getWeatherForCountryAndCity($country, $city);
        } catch (\DomainException $exception) {
            return $this->json(['error' => $exception->getMessage()], 404);
        }

        $data = [
            'city' => $city,
            'country' => $country,
            'measurements' => array_map(static fn (Measurement $measurement): array => [
                'date' => $measurement->getDate()->format('Y-m-d'),
                'celsius' => $measurement->getCelsius(),
                'fahrenheit' => $measurement->getFahrenheit(),
            ], $measurements),
        ];

        if ($twig) {
            $response = $this->render("weather_api/index.$format.twig", $data);
            $response->headers->set('Content-Type', $format === 'csv'
                ? 'text/csv; charset=UTF-8'
                : 'application/json');

            return $response;
        }

        if ($format === 'csv') {
            $lines = ['city,country,date,celsius,fahrenheit'];
            foreach ($data['measurements'] as $measurement) {
                $lines[] = sprintf(
                    '"%s","%s",%s,%s,%s',
                    str_replace('"', '""', $city),
                    str_replace('"', '""', $country),
                    $measurement['date'],
                    $measurement['celsius'],
                    $measurement['fahrenheit'],
                );
            }

            return new Response(implode("\r\n", $lines)."\r\n", headers: [
                'Content-Type' => 'text/csv; charset=UTF-8',
            ]);
        }

        return $this->json($data);
    }
}
