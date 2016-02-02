web: bin/heroku-php-apache2 web/
worker: php app/console rabbitmq:consumer create_availabilities
scraper: php app/console rabbitmq:consumer scrape_prices
cruncher: php app/console rabbitmq:consumer crunch_attachments
