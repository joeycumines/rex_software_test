#!/bin/bash

# "just fix it", with optional composer install

cd docker-symfony

if [ "--composer" == "$1" ]; then
    sudo docker-compose exec php composer install
fi

sudo docker-compose exec php php /var/www/symfony/bin/console doctrine:cache:clear-metadata
sudo docker-compose exec php php /var/www/symfony/bin/console doctrine:cache:clear-query
sudo docker-compose exec php php /var/www/symfony/bin/console doctrine:cache:clear-result
sudo docker-compose exec php php /var/www/symfony/bin/console doctrine:schema:update --force
sudo docker-compose exec php php /var/www/symfony/bin/console cache:clear --env=prod
sudo docker-compose exec php php /var/www/symfony/bin/console cache:clear --env=dev
sudo docker-compose exec php php /var/www/symfony/bin/console assets:install --symlink
# only suitable for testing / dev environments
cd ..
sudo chmod -R 0777 var
