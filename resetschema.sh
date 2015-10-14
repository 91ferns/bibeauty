#!/bin/bash

php app/console doctrine:schema:drop --force
php app/console doctrine:schema:update --force
php app/console doctrine:fixtures:load



heroku run php app/console doctrine:schema:drop --force
heroku run php app/console doctrine:schema:update --force
heroku run php app/console doctrine:fixtures:load
