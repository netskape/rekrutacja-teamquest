instalacja i konfiguracja:

1. Zainstaluj serwer mysql i php8.1 z niezbędnymi rozszerzeniami
2. Zainstaluj nginx i odpowiednio skonfiguruj - przykładowy plik konfiguracyjny nginx-rekrutacja.conf lub użyj APACHE2
2. Zaloguj się do serwera mysql jako root i wykonaj polecenia:
   CREATE DATABASE rekrutacja
   CREATE USER 'rekrutacja'@'localhost' IDENTIFIED BY 'xwa5wA4';
   GRANT ALL PRIVILEGES ON rekrutacja.* TO 'rekrutacja'@'localhost';
3. Zainstaluj niezbędne bilioteki uruchamiając: composer install
4. Uruchom migrację danych do DB poleceniem: composer migrations:migrate




Info odnoścnie rozwiązań:
 - wysyłka maila realizowana za pomoca narzędzia sendmail na ubuntu (jest możliwość wykorzystania rzeczywistego smtp, trzeba odkomentować konfig)
 - aby przetestować zmianę hasła po upływie N dni nalezy pobawić się wartością daysAfterChangePasswordRequired w /config/autoload/global.php
 - plik csv: users.csv
