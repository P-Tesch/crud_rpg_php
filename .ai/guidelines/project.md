# Project Overview

A web app for managing tabletop RPG character sheets and dice rolls (`crud_rpg_php`). Players edit their sheet and roll in real time. Game master/admin data is managed through a Filament panel. The project is in early development, so expect structure and conventions to change.

## System Documentation

The RPG system rules and game design docs (sheets, attributes, schools, spells, sonatas, miracles, mystic eyes, etc.) are here: https://drive.google.com/drive/folders/1TB1rPKTv7qRdFhKAb8BwywwTOcX46e3M?usp=sharing

Read them before changing game logic, such as roll calculations, sheet rules, or creation points.

## Stack

- **Backend:** Laravel on Octane (FrankenPHP), PostgreSQL, Redis (cache), Reverb (broadcasting).
- **Frontend:** Inertia + Vue 3 + TypeScript, Tailwind CSS + daisyUI, Laravel Echo, Laravel Precognition.
- **Admin:** Filament panel (`app/Filament`, `app/Providers/Filament/AdminPanelProvider.php`).
- **Tooling:** Pest, Larastan/PHPStan, Pint, Debugbar, IDE Helper.

## Layout

- `app/Models`: game domain models. Most relate to `Sheet` through pivot tables.
- `app/Entities`: plain PHP objects such as `SheetEntity` (the sheet payload sent to the frontend) and `RollHistory`.
- `app/Helpers/RollHelper.php`: dice roll logic.
- `routes/api.php`: JSON endpoints used by the sheet page (sheets, rolls, lookup lists, live heartbeat).
- `routes/channels.php`: `rolls` and `heartbeat` broadcast channels.
- `resources/js/Pages/vue`: Inertia pages (`sheet.vue`, `login.vue`), with sheet sections in `components/`.

## Local Environment

Everything runs through Docker Compose (`app`, `pgsql`, `redis`, `reverb`, `vite`). Run Artisan, Composer, and test commands inside the `rpg-app` container, e.g. `docker exec rpg-app php artisan test --compact`.

Composer scripts: `composer test`, `composer lint` (PHPStan), `composer format` (Pint), `composer ide` (regenerates model IDE helpers), `composer ci`.
