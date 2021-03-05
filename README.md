# Test task for EonX
[Test task description](desc.pdf)

## Framework-agnostic business logic
Initially, I thought I would make projects both for lumen and symfony and decided to extract 
all framework-agnostic business logic out into separate package https://github.com/gkerby/eonx-package

It has its own readme explaining architecture, to an extent, tests and stuff and is used in this project as
composer dependency

Unfortunately I just have very little free time to make symfony version. 

But I will if asked :)

## Setup && run
- clone
- create mysql db
- copy .env.example as .env
- modify .env to provide db access data
- composer install
- php artisan doctrine:schema:create
- phpunit
- php artisan customers:import --resultsNumber=200 
- cd public && php -S localhost:8000
- curl http://localhost:8000/customers
- curl http://localhost:8000/customers/1
