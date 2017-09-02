#!/bin/bash
# "just fix it", with optional composer install

# http://stackoverflow.com/a/246128
SOURCE="${BASH_SOURCE[0]}"
while [ -h "$SOURCE" ]; do # resolve $SOURCE until the file is no longer a symlink
  DIR="$( cd -P "$( dirname "$SOURCE" )" && pwd )"
  SOURCE="$(readlink "$SOURCE")"
  [[ $SOURCE != /* ]] && SOURCE="$DIR/$SOURCE" # if $SOURCE was a relative symlink, we need to resolve it relative to the path where the symlink file was located
done
DIR="$( cd -P "$( dirname "$SOURCE" )" && pwd )"

cd "$DIR/.."

sudo rm -rf "./var/cache/dev/*"
sudo rm -rf "./var/cache/prod/*"

cd "./docker-symfony"

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
