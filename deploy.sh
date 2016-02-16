#!/usr/bin/env bash

cd /var/www/html

composer install --no-dev

cd util

php cli.php cache:clear
php doctrine.php orm:schema-tool:update --force --complete
