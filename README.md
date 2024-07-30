# MetroFare

A Laravel 10 project that helps find the cheapest route using various cryptocurrencies.

## Getting Started

### Prerequisites

To work with this project, you need to have the following installed on your system:

- **PHP**: Required for running Laravel applications.
- **Composer**: Dependency manager for PHP.

### Installation

#### 1. Unzip the project

```bash
unzip MetroFare.zip
cd MetroFare
```

**Example:**
```bash
- cd ./MetroFare
- ./vendor/bin/sail up -d
```

**Postman usage:**

GET http://localhost:80/api/v1/cheapest-route?begin=0&end=5&currencies[]=USDT&currencies[]=ETH&currencies[]=BTC

- **begin** - начальная точка
- **end** - конечная точка
- **currencies[]** - Поддерживаемы (USDT,BTC,ETH)


**Command usage:**
```bash
- ./vendor/bin/sail artisan metro:find-cheapest-route
```

![Postman usage](https://i.postimg.cc/sgrSxQyL/Screenshot-2024-07-30-at-09-22-18.png)


![Command usage](https://i.postimg.cc/7Znz2HZZ/Screenshot-2024-07-30-at-09-21-43.png)
