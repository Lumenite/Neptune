#!/bin/bash

set -e

echo "
------------------------------------------------------------
Running NPM for ${PROJECTS} on ${APP_ENV} environment.
------------------------------------------------------------
"

IFS=',' read -r -a projects <<< "$(echo $PROJECTS)"

for project in ${projects[@]}
do
    cd /code/${project}

    if [[ ! -d "/code/$project/node_modules" ]]; then
        yarn
    else
        echo "Node modules already installed. skipping..."
    fi

    if [[ $APP_ENV = 'production' ]]; then
        yarn production
    else
        yarn watch
    fi
done
