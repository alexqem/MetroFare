**Example:**

- cd ./MetroFare
- composer intall
- ./vendor/bin/sail up -d


Postman usage:

GET /api/v1/cheapest-route?begin=0&end=5&currencies[]=USDT&currencies[]=ETH&currencies[]=BTC

- **begin** - начальная точка
- **end** - конечная точка
- **currencies[]** 


Command usage:

- sail artisan metro:find-cheapest-route
