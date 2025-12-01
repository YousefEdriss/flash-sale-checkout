## HOW TO RUN

composer install
php artisan migrate --seed
php artisan serve
php artisan test

### Guarantees:
- No oversell using DB row locking
- Holds reduce visible availability immediately
- Expired holds auto-release
- Payment webhook idempotent and safe if received multiple times
