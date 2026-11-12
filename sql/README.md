# Dane przykładowe Pogodynki

W PhpStorm otwórz konsolę SQL źródła SQLite używanego przez aplikację (domyślnie `var/data_dev.db`). W trybie automatycznego zatwierdzania uruchom cały skrypt `uzupelnij-pomiary.sql` w jednej sesji. Tabele `location` i `measurement` muszą już istnieć.

Jeśli pomiarów jest mniej niż pięć, skrypt dopisze brakujące rekordy z przykładową temperaturą i przyszłą datą. Jeżeli jest ich co najmniej pięć, pozostawi wszystkie. Skrypt nie odświeża dat starych rekordów, a prognoza na stronie pokazuje tylko daty późniejsze niż dzisiaj.

Ostatnie zapytanie zwraca zawartość tabeli pomiarów z nazwą miasta. To ten wynik, a nie drzewo kolumn, pokazuje dane potrzebne do zrzutu. Jeżeli polecenie kolejnego laboratorium wymaga zrzutów kodu odpowiedzialnego za prognozę, uwzględnij `WeatherController.php`, `LocationRepository.php` i `MeasurementRepository.php`.
