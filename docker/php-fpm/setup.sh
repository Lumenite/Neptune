#!/bin/bash

# ------------------------------------------------------------------------------------------------------------
# Main purpose of this file is to run all the necessary things to setup the project once the container is started
# for first time. Example if ft-api.executed file is present then it will not try to setup it again
# ------------------------------------------------------------------------------------------------------------

IFS=',' read -r -a projects <<< $(echo $PROJECTS)

if [[ ! -d "/code/variables" ]]; then
    mkdir /code/variables
fi

# Pre-installation things
composer global require hirak/prestissimo

for project in ${projects[@]}
do
    if [[ ! -d /code/$project ]]; then
        git clone https://${GIT_HOST}/${GIT_NAMESPACE}/$project.git /code/$project --recurse-submodules
        cd /code/$project

        if [[ $APP_ENV == "staging" ]]; then
            echo "Checkout out $BRANCH branch..."
            git checkout $BRANCH
        fi
    else
        echo "Project $project already exists. Skipping cloning..."
    fi

    if [[ ! -f "/code/variables/${project}.executed" ]]; then
        if [[ $APP_ENV = "production" ]]; then
            composer install --no-dev
        else
            if [[ ! -f ".env" ]]; then
                cp .env.sample .env
            fi

            composer install
            php artisan key:generate
        fi

        php artisan migrate

        chmod -R 0777 /code/${project}/storage
        touch /code/variables/${project}.executed
    fi
done

if [[ -d "/config/php-fpm" ]]; then
    cp -R /config/php-fpm/* /etc/php/7.2/fpm/conf.d/
fi

exec $@
