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


**Example:**

- cd ./MetroFare
- composer intall
- ./vendor/bin/sail up -d


Postman usage:

GET http://localhost:80/api/v1/cheapest-route?begin=0&end=5&currencies[]=USDT&currencies[]=ETH&currencies[]=BTC

- **begin** - начальная точка
- **end** - конечная точка
- **currencies[]** 


Command usage:

- sail artisan metro:find-cheapest-route
