# Flash Sale Checkout (Minimal Laravel App)

## Overview
This repository implements a small API for a flash-sale checkout system:
- Seeded product with finite stock
- Create temporary holds (2 minutes)
- Create orders from holds (one-time)
- Payment webhook that's idempotent and out-of-order safe
- Scheduler that releases expired holds

## Setup (local)
1. Clone repo
2. Copy `.env.example` -> `.env` and update DB credentials
3. Run:
   ```bash
   composer install
   php artisan key:generate
   php artisan migrate --seed
   php artisan serve
