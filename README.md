# symfonytest

#requirements

php >= 7.2

mysql 5.6

#launch

clone this repository

open console 

run: composer update

set up mysql access in .env file

run:

php bin/console doctrine:database:create

php bin/console doctrine:migrations:migrate

php bin/console doctrine:fixtures:load
