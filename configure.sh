#!/usr/bin/env bash

# Look for passed parameters.
# --update
# --upgrade
# Both statements are usable with --composer

UPDATE=false
UPGRADE=false
COMPOSER=false

while test $# -gt 0
do
    case "$1" in
        --update) UPDATE=true
            ;;
        --upgrade) UPGRADE=true
            ;;
        --composer) COMPOSER=true
            ;;
        --h) echo "Three modes can be used: '--composer', '--update', '--upgrade'."
            ;;
        *)  echo "Unknown option"
            exit 0
            ;;
    esac
    shift
done

if "$COMPOSER"; then
    #php composer.phar self-update
    php composer.phar update --prefer-stable
fi

if "$UPDATE" || "$UPGRADE"; then
    # Install dependencies
    php composer.phar install

    # Generate entities
    php bin/console doctrine:generate:entities AppBundle

    if "$UPDATE"; then
        php bin/console doctrine:schema:update --force
    elif "$UPGRADE"; then
        php bin/console doctrine:database:drop --force
        php bin/console doctrine:database:create
        php bin/console doctrine:schema:update --force
        php bin/console doctrine:fixtures:load
    fi

    # Clear caches
    php bin/console --env=dev cache:warmup
    php bin/console --env=dev --no-warmup cache:clear

    php bin/console --env=prod cache:warmup
    php bin/console --env=prod --no-warmup cache:clear

    php bin/console assets:install --symlink
    php bin/console assetic:dump

    # Print version to yml file
    echo "parameters:" > app/config/version.yml
    { echo "    version: " & git rev-list --count master; } | tr "\n" " " >> app/config/version.yml

    php composer.phar dump-autoload --optimize --no-dev --classmap-authoritative
fi