<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Measurement;
use App\Repository\LocationRepository;
use App\Service\WeatherUtil;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('weather:location', 'Wyświetla prognozę pogody według ID miejscowości.')]
class WeatherLocationCommand
{
    public function __construct(
        private readonly LocationRepository $locations,
        private readonly WeatherUtil $weather,
    ) {
    }

    public function __invoke(
        SymfonyStyle $io,
        #[Argument('ID miejscowości')] int $id,
    ): int {
        $location = $this->locations->find($id);
        if ($location === null) {
            $io->error(sprintf('Nie znaleziono miejscowości o ID %d.', $id));

            return Command::FAILURE;
        }

        $measurements = $this->weather->getWeatherForLocation($location);
        $io->writeln(sprintf('Miejscowość: %s (%s)', $location->getCity(), $location->getCountry()));

        $io->table(['Data i godzina', 'Temperatura (°C)', 'Wilgotność (%)', 'Ciśnienie (hPa)'], array_map(
            static fn (Measurement $measurement): array => [
                $measurement->getDate()->format('Y-m-d H:i'),
                $measurement->getCelsius(),
                $measurement->getHumidity() ?? 'Brak danych',
                $measurement->getPressureHpa() ?? 'Brak danych',
            ],
            $measurements,
        ));

        return Command::SUCCESS;
    }
}
