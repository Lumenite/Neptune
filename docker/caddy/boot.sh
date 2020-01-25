#!/bin/bash

# ------------------------------------------------------------------------------------------------------------
# This file will evaluate which project is running and create caddy configuration file.
# ------------------------------------------------------------------------------------------------------------

# Evaluate everything in the global scope
set -e

# Get the projects and convert it into array
IFS=',' read -r -a projects <<< "$(echo $PROJECTS)"

for project in ${projects[@]}
do
    echo -e "import /etc/caddy/$project.conf" >> /etc/Caddyfile
done

exec caddy $@
