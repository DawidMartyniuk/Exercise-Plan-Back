# Flex Plan — Backend

Krótko: backend Laravel dla aplikacji frontendowej "Flex Plan" — REST API do zarządzania planami ćwiczeń, sesjami treningowymi i zasobami (obrazy/gify).

## Najważniejsze
- Framework: Laravel
- API: JSON REST + Swagger (L5-Swagger)
- Autoryzacja: JWT (tymon/jwt-auth)
- Storage: lokalny disk `public` (storage/app/public → public/storage)

## Funkcje
- Zarządzanie planami treningowymi (exercise_table + exercise_rows_data + exercise_rows)
- Tworzenie i aktualizacja sesji treningowych (training_sessions, training_exercises, training_sets)
- Upload obrazów (storage/gifs)
- Reset hasła (password_resets)
- Dokumentacja API: /api/documentation lub /docs (L5-Swagger)

## Wymagania
- PHP 8.x
- Composer
- MySQL
- Node/npm (opcjonalnie, frontend build)
- Windows — polecenia poniżej przeznaczone dla PowerShell/CMD

## Quick start (Windows)
1. Klon repo:
   ```powershell
   git clone <repo-url>
   cd exercise_plan_back
   ```

2. Instalacja zależności:
   ```powershell
   composer install
   ```

3. Skopiuj plik środowiska i ustaw zmienne:
   ```powershell
   copy .env.example .env
   ```
   Ustaw DB_*, APP_URL (np. http://127.0.0.1:8000), JWT_SECRET (php artisan jwt:secret) i konfigurację mail.

4. Wygeneruj klucz aplikacji:
   ```powershell
   php artisan key:generate
   ```

5. Migracje:
   ```powershell
   php artisan migrate
   ```

6. (Jeśli używasz storage public) utwórz link:
   ```powershell
   php artisan storage:link
   ```
   Uwaga: dla CORS lepiej serwować pliki przez trasę Laravel (/gifs/...) albo usunąć public/storage symlink aby Laravel obsługiwał żądania i dodał nagłówki CORS.

7. Uruchom serwer deweloperski:
   ```powershell
 php artisan serve --host=0.0.0.0 --port=8000 
   ```

Jeśli generator wyrzuca błędy (np. brakujące klasy),:
- stwórz brakujące klasy (np. App\Mail\TestMail) lub
- wyklucz foldery w config/l5-swagger.php → scanOptions → exclude

## Przykładowe requesty
- Login:
  POST /api/login
  Body JSON: { "email":"...","password":"..." }

- Pobierz plan:
  GET /api/plan  (Authorization: Bearer <token>)

- Upload obrazka w ExerciseController.create:
  użyj form-data z kluczem `gif` (plik), backend zapisuje do storage/gifs i zwraca URL.

## CORS / obrazy (ważne)
- Jeśli frontend (np. Flutter Web) ładuje obrazki przez XHR, odpowiedź musi zawierać nagłówek Access-Control-Allow-Origin.
- Najprostsze:
  - Serwuj pliki przez trasę Laravel `/gifs/{filename}` i dodaj nagłówki CORS w response().
  - LUB: skonfiguruj serwer (Apache/Nginx) aby ustawił nagłówki dla plików w public/storage.

## Zmienne środowiskowe (przykład .env)
APP_NAME=FlexPlan
APP_ENV=local
APP_KEY=base64:...
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=flexplan
DB_USERNAME=root
DB_PASSWORD=

JWT_SECRET=...

MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="no-reply@flexplan.local"

## Testy
- Uruchom testy (jeśli dodane):
  ```powershell
  ./vendor/bin/phpunit
  ```

## Contributing
- Fork → feature branch → pull request
- Utrzymuj migracje i aktualizuj dokumentację Swagger jeśli dodajesz endpointy

## Troubleshooting (częste problemy)
- Błędy migracji FK/indeksów: sprawdź strukturę tabel (DESCRIBE / SHOW CREATE TABLE) i popraw migracje dodając warunki Schema::hasColumn / checks.
- L5-Swagger: jeśli generator zgłasza brak @OA\PathItem lub nieznane klasy — usuń/napraw adnotacje albo wyklucz katalogi z konfiguracji.
- CORS: sprawdź czy nagłówek Access-Control-Allow-Origin znajduje się w odpowiedzi (dla XHR).

## License & Contact
- License: MIT
- Kontakt: [dawidmartyniuk1@gmail.com]
