#!/bin/bash
# runs a symfony command in the php container
cd docker-symfony
sudo docker-compose exec php php /var/www/symfony/bin/console "$@"
