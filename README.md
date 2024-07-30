# MetroFare

A Laravel 10 project that helps find the cheapest route using various cryptocurrencies.

According to tech requirements https://docs.google.com/document/d/1FHC2oavx-JqKWs1zrtNczYL24aQFF5dLnCujxK-ENdo/edit?usp=sharing

## Getting Started

### Prerequisites

To work with this project, you need to have the following installed on your system:

- **PHP**
- **Composer**
- **Docker** Has to be installed and run.

### Installation

```bash
cd ./MetroFare
./vendor/bin/sail up -d
```

**Command usage:**
```bash
./vendor/bin/sail artisan metro:find-cheapest-route
```

**Postman usage:**

```bash
GET http://localhost:80/api/v1/cheapest-route?begin=0&end=5&currencies[]=USDT&currencies[]=ETH&currencies[]=BTC
```

- **begin**
- **end**
- **currencies[]** (USDT,BTC,ETH)

**Unit test:**
```bash
- ./vendor/bin/sail artisan test tests/Unit/MetroServiceTest.php
```

**Files to overview:**
- app/Services/MetroService.php
- app/Models/MetroFares.php
- app/Services/RatesAndFaresService.php
- app/Http/Controllers/api/MetroController.php
- tests/Unit/MetroServiceTest.php
- app/Http/Requests/CheapestRouteRequest.php
- app/Http/Resources/CheapestRouteResource.php

**Screenshoots**
![Postman usage](https://i.postimg.cc/sgrSxQyL/Screenshot-2024-07-30-at-09-22-18.png)


![Command usage](https://i.postimg.cc/7Znz2HZZ/Screenshot-2024-07-30-at-09-21-43.png)
