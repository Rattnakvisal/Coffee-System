# Coffee System ☕

**Coffee Shop Management & Point of Sale**

ប្រព័ន្ធគ្រប់គ្រងហាងកាហ្វេ និងការលក់នៅបញ្ជរ ដែលបង្កើតដោយ Laravel។ មានផ្ទាំង Admin សម្រាប់គ្រប់គ្រងអាជីវកម្ម និងផ្ទាំង Cashier សម្រាប់ជ្រើសរើសភេសជ្ជៈ បង្កើតការកុម្ម៉ង់ និងទទួលប្រាក់ជា USD/KHR តាម Cash ឬ KHQR។

**Laravel 12 · PHP · MySQL · Blade · Tailwind CSS 4 · Vite 7**

## Contents

- [មុខងារសំខាន់ៗ](#features)
- [បច្ចេកវិទ្យា និងតម្រូវការ](#requirements)
- [ដំឡើងលើម៉ាស៊ីនផ្ទាល់](#local-setup)
- [ដំឡើងជាមួយ Docker](#docker-setup)
- [ទិន្នន័យ និងគណនីសាកល្បង](#demo-data-and-accounts)
- [របៀបប្រើប្រាស់](#usage)
- [កំណត់ការទូទាត់](#payment-configuration)
- [រចនាសម្ព័ន្ធ Project](#project-structure)
- [ទិន្នន័យសំខាន់ៗ](#database)
- [ពាក្យបញ្ជាសម្រាប់អភិវឌ្ឍ](#development-commands)
- [ការធ្វើតេស្ត](#tests)
- [ដំណោះស្រាយបញ្ហាទូទៅ](#troubleshooting)
- [លក្ខណៈនៃប្រព័ន្ធបច្ចុប្បន្ន](#current-behavior)

## Features

### Admin

| ផ្នែក | មុខងារ |
| --- | --- |
| Dashboard | មើលស្ថិតិការលក់ ស្វែងរក និងទទួល notifications អំពី orders និង attendance |
| Products | បន្ថែម កែប្រែ លុបផលិតផល បញ្ចូលរូបភាព កំណត់តម្លៃ និងបើក/បិទទំហំ Small, Medium, Large |
| Categories | គ្រប់គ្រងប្រភេទផលិតផល និងស្ថានភាព active |
| Discounts | កំណត់ភាគរយបញ្ចុះតម្លៃតាមផលិតផល |
| Users | គ្រប់គ្រងព័ត៌មានអ្នកប្រើប្រាស់ និងតួនាទី Admin/Cashier |
| Attendance | មើលវត្តមាន និងម៉ោង check-in របស់ cashier |
| Inventory | តាមដានប្រាក់ចូលពីការលក់ និងកត់ត្រាប្រាក់ចំណាយ |
| Reports | ច្រោះរបាយការណ៍តាមកាលបរិច្ឆេទ cashier វិធីទូទាត់ និងស្ថានភាព; export CSV សម្រាប់ Excel និង Print / Save as PDF |
| Settings | កែព័ត៌មានផ្ទាល់ខ្លួន រូបភាព profile និងពាក្យសម្ងាត់ |

### Cashier / POS

- ស្វែងរកផលិតផល និងច្រោះតាម category។
- ជ្រើសទំហំ កម្រិតស្ករ និងចំនួន រួចដាក់ក្នុង cart។
- គណនាសរុប ការបញ្ចុះតម្លៃ និងប្រាក់ត្រូវបង់។
- ទទួលសាច់ប្រាក់ USD, KHR ឬលាយទាំងពីរ និងគណនាប្រាក់អាប់។
- ទទួល KHQR ដោយបញ្ចូល transaction reference និងបញ្ជាក់ទទួលប្រាក់។
- កត់វត្តមានប្រចាំថ្ងៃ និងមើលប្រវត្តិការកុម្ម៉ង់។

## Requirements

| បច្ចេកវិទ្យា | កំណែ / ការប្រើប្រាស់ |
| --- | --- |
| PHP | **8.3+** សម្រាប់ dependencies រួមទាំង tests; Docker ប្រើ **8.4** |
| Composer | 2.x សម្រាប់ដំឡើង PHP dependencies |
| Laravel | 12.x; កំណែជាក់លាក់រក្សាទុកក្នុង `composer.lock` |
| MySQL | 8.0 ជាមូលដ្ឋានទិន្នន័យក្នុង Docker |
| Node.js | **20.19+ ក្នុងសាខា 20.x ឬ 22.12+** តាម requirement របស់ Vite ក្នុង lockfile |
| Frontend | Blade, JavaScript, Tailwind CSS 4, Vite 7, Lucide icons |
| Tests | Pest 4 / PHPUnit 12 និង SQLite in-memory |
| Docker (ជម្រើស) | Docker Desktop និង Docker Compose v2 |

PHP ត្រូវមាន extensions ដែល Laravel ត្រូវការ រួមមាន `pdo_mysql`, `mbstring`, `xml` និង `fileinfo`។ សម្រាប់ tests ត្រូវមាន `pdo_sqlite` ផងដែរ។ ទោះ `composer.json` អនុញ្ញាត PHP `^8.2` ក៏ដោយ dev dependencies បច្ចុប្បន្នត្រូវការ PHP 8.3 ឡើងទៅ។

## Local Setup

ពាក្យបញ្ជាខាងក្រោមសម្រាប់ **Windows PowerShell**។ បើក Terminal នៅក្នុង root របស់ project ដែលមានឯកសារ `artisan`។

### 1. រៀបចំ dependencies និង environment

```powershell
if (-not (Test-Path .env)) { Copy-Item .env.example .env }
composer install
npm ci
```

### 2. បង្កើត database និងកំណត់ .env

បើក MySQL ហើយបង្កើត database ថ្មី ប្រសិនបើមិនទាន់មាន៖

```sql
CREATE DATABASE coffee CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

កែតម្លៃខាងក្រោមក្នុង `.env` ឱ្យត្រូវនឹង MySQL លើម៉ាស៊ីនរបស់អ្នក៖

```dotenv
APP_NAME="Coffee System"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000
APP_TIMEZONE=Asia/Phnom_Penh

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=coffee
DB_USERNAME=root
DB_PASSWORD=
```

បើ MySQL មាន password ឬប្រើ port ផ្សេង សូមកែ `DB_PASSWORD` និង `DB_PORT` តាមនោះ។ `.env.example` ប្រើ database សម្រាប់ session, cache និង queue ជាលំនាំដើម។

### 3. រៀបចំ application

សម្រាប់ការដំឡើងថ្មី ដែលចង់បានទិន្នន័យសាកល្បង៖

```powershell
php artisan key:generate
php artisan config:clear
php artisan migrate --seed
php artisan storage:link
```

`storage:link` អនុញ្ញាតឱ្យ browser បង្ហាញរូបផលិតផល និង profile ដែល upload ទៅ `storage/app/public`។ សម្រាប់ database ដែលមានទិន្នន័យប្រើប្រាស់រួច សូមប្រើ `php artisan migrate` ហើយអាន [ចំណាំអំពី seeder](#demo-data-and-accounts) មុនបន្ថែម demo data។

### 4. ដំណើរការ project

បើក Terminal ទីមួយ៖

```powershell
php artisan serve --host=127.0.0.1 --port=8000
```

បើក Terminal ទីពីរ៖

```powershell
npm run dev
```

| ទំព័រ | អាសយដ្ឋាន |
| --- | --- |
| Cashier POS | <http://127.0.0.1:8000> |
| Admin Login | <http://127.0.0.1:8000/login/admin> |
| Admin Dashboard | <http://127.0.0.1:8000/admin> |
| Cashier Attendance | <http://127.0.0.1:8000/cashier/attendance> |
| Order History | <http://127.0.0.1:8000/cashier/history> |

បន្ទាប់ពីដំឡើងរួច អាចប្រើ `composer run dev` ដើម្បីបើក Laravel server, queue listener និង Vite ក្នុងពេលតែមួយ។

## Docker Setup

Project មាន services `app` (PHP 8.4-FPM), `web` (Nginx), `db` (MySQL 8.0) និង `vite` ដែលបើកតាម profile `frontend`។ ច្រក web លំនាំដើមគឺ **8001** ហើយអាចកែតាម `APP_PORT`។

### 1. កំណត់ database connection

**Compose បច្ចុប្បន្នភ្ជាប់ `app` ទៅ MySQL លើ host តាម `host.docker.internal:3308`។** តម្លៃក្នុង `app.environment` របស់ [docker-compose.yml](docker-compose.yml) មានអាទិភាពលើ `.env`។

បើចង់ប្រើ MySQL service `db` ដែលមានស្រាប់ក្នុង Compose សូមកែ **តែ DB keys ក្នុង `services.app.environment`** ឱ្យត្រូវនឹង service នោះ មុនដំណើរការ៖

```yaml
DB_CONNECTION: mysql
DB_HOST: db
DB_PORT: 3306
DB_DATABASE: coffee
DB_USERNAME: laravel
DB_PASSWORD: laravel
```

តម្លៃ `laravel` ខាងលើត្រូវនឹងគណនី development ក្នុង `db.environment` បច្ចុប្បន្ន។ បើប្ដូរគណនី ត្រូវកំណត់ទាំងពីរកន្លែងឱ្យត្រូវគ្នា។ បើបន្តប្រើ MySQL លើ host ត្រូវធានាថា server ដំណើរការលើ port `3308` និង DB credentials ក្នុង Compose ត្រឹមត្រូវ។

### 2. បើក containers និងរៀបចំ application

```powershell
if (-not (Test-Path .env)) { Copy-Item .env.example .env }
docker compose up -d --build
docker compose exec app php artisan config:clear
docker compose exec app php artisan migrate --seed
docker compose exec app php artisan storage:link
```

Entrypoint ដំឡើង Composer dependencies បើមិនទាន់មាន និងបង្កើត `APP_KEY` បើវាទទេ។ Migration និង frontend build ត្រូវរត់ដោយឡែក។

### 3. Build frontend assets

ប្រើ Node container ដើម្បីបង្កើត `public/build`៖

```powershell
docker compose --profile frontend run --rm --no-deps vite npm ci
docker compose --profile frontend run --rm --no-deps vite npm run build
```

បន្ទាប់មកបើក POS តាម <http://localhost:8001> ឬ Admin តាម <http://localhost:8001/login/admin>។ បើកែ `APP_PORT` សូមប្រើ port ថ្មីក្នុង URL។

សម្រាប់ frontend hot reload អាចបើក Vite service ជំនួសការប្រើ build៖

```powershell
docker compose --profile frontend up -d vite
```

Compose mount source code ពីម៉ាស៊ីន host។ ការកែ CSS/JS ត្រូវ build assets ម្ដងទៀត ឬប្រើ Vite; ការ rebuild PHP image មួយមុខមិនបង្កើត assets ទេ។

### ពាក្យបញ្ជា Docker ដែលប្រើញឹកញាប់

```powershell
docker compose ps
docker compose logs -f app web
docker compose exec app php artisan migrate:status
docker compose down
```

`docker compose down` ទុក database volume នៅដដែល។ ជៀសវាងបន្ថែម `-v` ប្រសិនបើចង់រក្សាទិន្នន័យ។ Redis និង Mailpit មិនមានជា service ក្នុង Compose បច្ចុប្បន្នទេ។

## Demo Data and Accounts

[DatabaseSeeder.php](database/seeders/DatabaseSeeder.php) រៀបចំទិន្នន័យដូចខាងក្រោម៖

- តួនាទី **Admin** និង **Cashier**។
- Admin ម្នាក់ និង cashier ពីរនាក់។
- Categories ចំនួន **5** និងផលិតផលគំរូចំនួន **9** ជាមួយតម្លៃតាមទំហំ រូបភាព និង discounts។
- Attendance សម្រាប់ Dara Sok នៅថ្ងៃដែលរត់ seeder។
- Orders គំរូចំនួន **2** ប្រសិនបើតារាង `orders` មិនទាន់មានទិន្នន័យ។

| តួនាទី | Email | Password |
| --- | --- | --- |
| Admin | `admin@coffee.test` | `admin12345` |
| Cashier — Dara Sok | `dara.sok@coffee.test` | `password123` |
| Cashier — Sreypov Meas | `sreypov.meas@coffee.test` | `password123` |

**Admin ចូលតាម `/login/admin`។** Cashier records ប្រើសម្រាប់ attendance និងទិន្នន័យពាក់ព័ន្ធ; project បច្ចុប្បន្នមិនមានទំព័រ `/login/cashier` ទេ។

Migration ក៏បង្កើត default Admin ដោយឡែកផងដែរ។ Password ក្នុងតារាងគឺសម្រាប់គណនីដែលទើបបង្កើត; បើបានប្ដូរ password រួច ត្រូវប្រើតម្លៃថ្មី។ ប្ដូរ default Admin password ក្នុង Settings មុនប្រើជាក់ស្ដែង។

សម្រាប់បន្ថែម demo data ក្រោយ migrate រួច៖

```powershell
php artisan db:seed
```

**ចំណាំ៖** Seeder ប្រើ `updateOrCreate` សម្រាប់ផលិតផលគំរូ ដូច្នេះការរត់ម្ដងទៀតអាចកែតម្លៃ រូបភាព និងព័ត៌មានរបស់ផលិតផលដែលមាន slug ដូចគ្នា។ រូបភាពគំរូខ្លះទាញពី Unsplash ដូច្នេះត្រូវការអ៊ីនធឺណិតសម្រាប់បង្ហាញ។

## Usage

### សាកល្បងការលក់

1. បើក `/cashier/attendance` ហើយ check-in cashier សម្រាប់ថ្ងៃបច្ចុប្បន្ន។
2. បើក `/` ឬ `/cashier` រួចស្វែងរកភេសជ្ជៈ ឬជ្រើស category។
3. ជ្រើសទំហំ កម្រិតស្ករ និងចំនួន រួចបន្ថែមទៅ cart។
4. ពិនិត្យសរុប និងការបញ្ចុះតម្លៃ រួចជ្រើសវិធីទូទាត់។
5. សម្រាប់ Cash បញ្ចូលប្រាក់ USD/KHR ដែលបានទទួល និងជ្រើសរូបិយប័ណ្ណប្រាក់អាប់។
6. សម្រាប់ KHQR ពិនិត្យការទទួលប្រាក់ក្នុង app ធនាគាររបស់ហាង បញ្ចូល reference និងធីកបញ្ជាក់។
7. បញ្ចប់ order ហើយពិនិត្យលទ្ធផលនៅ `/cashier/history`។

### ចូលគ្រប់គ្រងហាង

1. បើក `/login/admin` ហើយចូលដោយគណនី Admin។
2. រៀបចំ Categories និង Products មុនលក់ផលិតផលថ្មី។
3. ពិនិត្យ Reports សម្រាប់ការលក់ និង Inventory សម្រាប់ប្រាក់ចូល/ចំណាយ។
4. ប្រើ Settings ដើម្បីកែ profile និង password។

## Payment Configuration

កំណត់ការទូទាត់ក្នុង `.env`៖

```dotenv
POS_KHR_PER_USD=4000
POS_CARD_ENABLED=false
POS_KHQR_MERCHANT_NAME="Purr's Coffee"
POS_KHQR_USD_IMAGE=
POS_KHQR_KHR_IMAGE=
```

| Key | អត្ថន័យ |
| --- | --- |
| `POS_KHR_PER_USD` | ចំនួនរៀលក្នុង 1 USD តាមអត្រាដែលហាងកំណត់ |
| `POS_CARD_ENABLED` | បើក Card នៅពេលហាងមាន terminal; ត្រូវបញ្ជាក់ទទួលប្រាក់ និងបញ្ចូល reference |
| `POS_KHQR_MERCHANT_NAME` | ឈ្មោះហាងដែលបង្ហាញក្នុងផ្ទាំង KHQR |
| `POS_KHQR_USD_IMAGE` | Path រូប KHQR ពិតសម្រាប់ USD ឧទាហរណ៍ `/images/payments/khqr-usd.png` |
| `POS_KHQR_KHR_IMAGE` | Path រូប KHQR ពិតសម្រាប់ KHR ឧទាហរណ៍ `/images/payments/khqr-khr.png` |

ដាក់រូប KHQR ដែលចេញដោយធនាគាររបស់ហាងក្នុង `public/images/payments/`។ បើទុក image keys ទទេ cashier អាចប្រើ KHQR stand នៅបញ្ជរ។ ក្រោយកែ environment សូមរត់ `php artisan config:clear`។

តម្លៃលក់ និងរបាយការណ៍គិតជា USD។ `orders.payment_details` រក្សាទុកអត្រានៅពេលលក់ ប្រាក់ដែលទទួល ប្រាក់អាប់ និង reference តាមប្រភេទការទូទាត់។ អត្រាប្ដូរប្រាក់មិនទាញដោយស្វ័យប្រវត្តិ ហើយ KHQR/Card ជាការបញ្ជាក់ដោយ cashier ដោយមិនទាន់ភ្ជាប់ bank API។

អានលម្អិតអំពីការបង្គត់ប្រាក់ ការបញ្ជាក់ទូទាត់ និង payment migration នៅ [ការទូទាត់សម្រាប់ POS នៅកម្ពុជា](docs/PAYMENTS_KH.md)។

## Project Structure

```text
Coffee-System/
├── app/
│   ├── Http/Controllers/
│   │   ├── Admin/             # Dashboard, CRUD, reports, settings
│   │   └── Cashier/           # POS, cart, checkout, attendance, history
│   ├── Http/Middleware/      # Role middleware
│   ├── Models/               # Eloquent models
│   └── Services/             # Payment logic and admin header data
├── config/payments.php       # USD/KHR, KHQR and card configuration
├── database/
│   ├── migrations/           # Schema and default roles/admin
│   └── seeders/              # Demo users, products and orders
├── docker/                   # PHP entrypoint and Nginx configuration
├── docs/PAYMENTS_KH.md        # Payment guide in Khmer
├── resources/
│   ├── css/                  # Application and POS styles
│   ├── js/                   # Frontend interactions and payment UI
│   └── views/                # Blade layouts, admin and cashier pages
├── routes/
│   ├── web.php               # Login and cashier routes
│   └── admin.php             # Admin routes protected by auth + role
├── tests/                    # Pest feature and unit tests
├── .env.example              # Local configuration template
├── docker-compose.yml        # Docker services and volumes
└── vite.config.js            # Frontend build configuration
```

## Database

| តារាង | តួនាទី |
| --- | --- |
| `roles` | តួនាទី និងស្ថានភាព active |
| `users` | គណនី ព័ត៌មានផ្ទាល់ខ្លួន និងតួនាទី |
| `categories` | ប្រភេទផលិតផល |
| `products` | តម្លៃតាមទំហំ ស្ថានភាព រូបភាព និងភាគរយបញ្ចុះតម្លៃ |
| `orders` | លេខ order សរុប ការទូទាត់ ស្ថានភាព និងពេលលក់ |
| `order_items` | ផលិតផល ទំហំ ស្ករ ចំនួន និងតម្លៃក្នុង order នីមួយៗ |
| `cashier_attendances` | វត្តមាន និងម៉ោង check-in ប្រចាំថ្ងៃ |
| `inventory_transactions` | ប្រតិបត្តិការប្រាក់ចូល/ចំណាយ |

ទំនាក់ទំនងសំខាន់ៗ៖ Role មួយមាន Users ច្រើន; Category មួយមាន Products ច្រើន; Order មួយមាន Order Items ច្រើន; Cashier អាចមាន Orders និង Attendance ច្រើន។ Laravel ក៏មានតារាងសម្រាប់ sessions, cache និង jobs ផងដែរ។

## Development Commands

| ពាក្យបញ្ជា | គោលបំណង |
| --- | --- |
| `composer run dev` | បើក Laravel, queue listener និង Vite រួមគ្នា |
| `npm run dev` | ដំណើរការ Vite ជាមួយ hot reload |
| `npm run build` | បង្កើត assets ទៅ `public/build` |
| `php artisan migrate` | អនុវត្ត migrations ដែលនៅសល់ |
| `php artisan migrate:status` | មើលស្ថានភាព migrations |
| `php artisan route:list --except-vendor` | មើល routes របស់ application |
| `php artisan config:clear` | លុប config cache ក្រោយកែ `.env` |
| `php artisan view:clear` | លុប Blade view cache |
| `php artisan storage:link` | ភ្ជាប់ public storage សម្រាប់រូបភាព |
| `php vendor/bin/pint --test` | ពិនិត្យ PHP code style ដោយមិនកែ code |

បន្ទាប់ពី build assets រួច អាចបើក `php artisan serve` ដោយមិនចាំបាច់បើក Vite។ បើមាន `public/hot` ពី Vite session ចាស់ សូមដោះស្រាយតាម [Troubleshooting](#troubleshooting)។

## Tests

រត់ test suite៖

```powershell
composer run test
```

ឬរត់តែ payment tests៖

```powershell
php artisan test --compact tests/Feature/CashierPaymentTest.php
```

Tests បច្ចុប្បន្នគ្របដណ្ដប់លើការបង្ហាញ POS, Cash ជា USD/KHR/លាយ, ប្រាក់អាប់, ការបដិសេធប្រាក់មិនគ្រប់, precision និងអត្រាខុស, KHQR reference/confirmation និងការបើកឬបិទ Card។

[phpunit.xml](phpunit.xml) កំណត់ `APP_ENV=testing` និង SQLite `:memory:` ដោយប្រើ array session/cache និង synchronous queue។ ប្រើ test configuration នេះពេលរត់ suite; បើ Laravel មាន config cache សូមរត់ `php artisan config:clear` មុន។

## Troubleshooting

| បញ្ហា | អ្វីដែលត្រូវពិនិត្យ / ដំណោះស្រាយ |
| --- | --- |
| `SQLSTATE` / connection refused | ពិនិត្យ MySQL service និង `DB_*`; ក្នុង Docker ពិនិត្យ `app.environment` ដែល override `.env` រួច recreate `app` និង clear config |
| `No application encryption key` | សម្រាប់ការដំឡើងថ្មី រត់ `php artisan key:generate` |
| Table មិនទាន់មាន / `/login/admin` ចេញ 404 | រត់ `php artisan migrate` ហើយពិនិត្យថា role `admin` មានស្ថានភាព active |
| POS មិនអនុញ្ញាតឱ្យដាក់ cart ឬបញ្ចប់ order | Check-in នៅ `/cashier/attendance` សម្រាប់ថ្ងៃបច្ចុប្បន្ន |
| `Vite manifest not found` | រត់ `npm ci` និង `npm run build` ឬបើក `npm run dev` |
| Browser ព្យាយាមភ្ជាប់ `:5173` តែ Vite បិទ | បើក Vite ឡើងវិញ ឬលុប `public/hot` ដែលនៅសល់ រួច build assets |
| រូប upload មិនបង្ហាញ | រត់ `php artisan storage:link` និងពិនិត្យ file ក្នុង `storage/app/public` |
| `.env` ថ្មីមិនមានប្រសិទ្ធភាព | រត់ `php artisan config:clear`; ក្នុង Docker ត្រូវពិនិត្យ Compose environment ផង |
| Test បង្ហាញ `could not find driver` | បើក `pdo_sqlite` ក្នុង `php.ini` របស់ PHP CLI |
| Port កំពុងត្រូវបានប្រើ | Local: ប្ដូរ `--port` និង `APP_URL`; Docker: កែ `APP_PORT` ក្នុង `.env` រួចរត់ Compose ឡើងវិញ |

សម្រាប់ប្រើ built assets ក្រោយបិទ Vite៖

```powershell
if (Test-Path public/hot) { Remove-Item -LiteralPath public/hot }
npm run build
```

ក្នុង Docker ប្រើពាក្យបញ្ជា build ក្នុង [Docker Setup](#docker-setup) ហើយប្រើ prefix `docker compose exec app` សម្រាប់ Artisan commands។

## Current Behavior

- Admin routes ការពារដោយ `auth` និង `role:admin`។ Cashier routes បើកដោយគ្មាន login middleware តាម workflow បច្ចុប្បន្ន ហើយ cart/checkout ត្រូវការឱ្យមាន attendance យ៉ាងហោចណាស់មួយនៅថ្ងៃនោះ។
- Attendance មិនមែនជា cashier login ទេ។ Order ថ្មីអាចមាន `cashier_id` ទទេ ព្រោះ code ភ្ជាប់ cashier តែពេលមាន authenticated user ដែលមាន role `cashier`។
- Inventory បច្ចុប្បន្នតាមដានហិរញ្ញវត្ថុ៖ ប្រាក់ចូលយកពី orders និងប្រាក់ចេញកត់ត្រាដោយ Admin។ មិនទាន់មានការកាត់ស្តុកគ្រាប់កាហ្វេ ទឹកដោះគោ ឬវត្ថុធាតុដើមតាមរូបមន្តទេ។
- ប៊ូតុង export Excel ផ្ដល់ឯកសារ `.csv`។ Export PDF បើកទំព័រសម្រាប់ **Print / Save as PDF** របស់ browser។
- Docker configuration បច្ចុប្បន្នប្រើ `APP_ENV=local` និង `APP_DEBUG=true` សម្រាប់ development។
