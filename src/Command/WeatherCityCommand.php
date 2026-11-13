<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Measurement;
use App\Service\WeatherUtil;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('weather:city', 'Wyświetla prognozę pogody według kraju i miejscowości.')]
class WeatherCityCommand
{
    public function __construct(private readonly WeatherUtil $weather)
    {
    }

    public function __invoke(
        SymfonyStyle $io,
        #[Argument('Dwuliterowy kod kraju')] string $country,
        #[Argument('Nazwa miejscowości')] string $city,
    ): int {
        try {
            $measurements = $this->weather->getWeatherForCountryAndCity($country, $city);
        } catch (\DomainException $exception) {
            $io->error($exception->getMessage());

            return Command::FAILURE;
        }

        $io->writeln(sprintf('Miejscowość: %s (%s)', $city, strtoupper($country)));
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
