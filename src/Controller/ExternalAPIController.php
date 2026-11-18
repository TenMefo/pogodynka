<?php

namespace App\Controller;

use App\Form\ExternalAPIType;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ExternalAPIController extends AbstractController
{
    #[Route('/external/api', name: 'app_external_api', methods: ['GET', 'POST'])]
    public function index(Request $request, HttpClientInterface $client, LoggerInterface $logger): Response
    {
        $form = $this->createForm(ExternalAPIType::class, [
            'latitude' => 53.429,
            'longitude' => 14.553,
        ]);
        $form->handleRequest($request);
        $weatherData = null;
        $error = null;

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            try {
                $weatherData = $client->request('GET', 'https://api.open-meteo.com/v1/forecast', [
                    'query' => [
                        'latitude' => $data['latitude'],
                        'longitude' => $data['longitude'],
                        'daily' => 'temperature_2m_max,temperature_2m_min,precipitation_sum',
                        'timezone' => 'UTC',
                    ],
                    'timeout' => 10,
                    'max_duration' => 15,
                ])->toArray();
                $daily = $weatherData['daily'] ?? [];
                $dates = $daily['time'] ?? null;
                if (!is_array($dates) || $dates === []) {
                    throw new \UnexpectedValueException('Brak dat prognozy.');
                }
                foreach (['temperature_2m_max', 'temperature_2m_min', 'precipitation_sum'] as $field) {
                    if (!is_array($daily[$field] ?? null) || count($daily[$field]) !== count($dates)) {
                        throw new \UnexpectedValueException('Niekompletna prognoza.');
                    }
                }
            } catch (ExceptionInterface | \UnexpectedValueException $exception) {
                $logger->warning('Nie udało się pobrać prognozy Open-Meteo.', ['exception' => $exception]);
                $weatherData = null;
                $error = 'Nie udało się pobrać kompletnej prognozy. Spróbuj ponownie za chwilę.';
            }
        }

        return $this->render('external_api/index.html.twig', [
            'form' => $form,
            'weather_data' => $weatherData,
            'error' => $error,
        ]);
    }
}
