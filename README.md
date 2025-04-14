## How to run the project

    Basic way (requires PHP 8.2)
        - copy the .env.example and name it .env
        - composer install
        - run php artisan key:generate
        - composer install  
        - php artisan migrate

    Using docker
        - docker-compose build
        - docker-compose up
        - docker-compose exec laravel.test sh (to open the container terminal)
        - composer install
        - cp .env.example .env
        - php artisan key:generate
        - php artisan migrate
        - docker-compose restart


## Note
    There should be a job running, maybe monthly, to calculate the monthly balance 
    and store it in the balances table, reducing the amount of work needed to calculate the actual balance
