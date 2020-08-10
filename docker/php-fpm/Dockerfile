FROM phpdockerio/php72-fpm:latest

MAINTAINER "Mohammed Mudassir<md@thebots.io>"

ARG git_host
ARG git_namespace
ARG projects
# Fix debconf warnings upon build
ARG DEBIAN_FRONTEND="noninteractive"

ENV LSAPI_MAX_PROCESS_TIME=0
ENV GIT_HOST="${git_host}"
ENV GIT_NAMESPACE="${git_namespace}"
ENV PROJECTS="${projects}"
ENV LANG="en_US.UTF-8"
ENV LANGUAGE="en_US:en"
ENV APP_ENV="local"
ENV BRANCH="master"

WORKDIR /code

# Install locales package
RUN apt-get update && apt-get -y --no-install-recommends install libpng-dev cron locales git make \
    php7.2-mysql php7.2-sqlite3 php7.2-intl php7.2-gmp php7.2-gd php7.2-bcmath \
	&& apt-get clean \
	&& rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

# configure locales
RUN ln -sf /etc/locale.alias /usr/share/locale/locale.alias \
    && sed -i -e 's/# en_US.UTF-8 UTF-8/en_US.UTF-8 UTF-8/' /etc/locale.gen \
    && locale-gen

# Adding PHP configuration
ADD ./100-ft-overrides.ini /etc/php/7.2/fpm/conf.d/

# Initializing post installation script
ADD ./setup.sh /etc/setup.sh
RUN chmod +x /etc/setup.sh

RUN usermod -u 1000 www-data && chown -R www-data:www-data /code

CMD /etc/setup.sh php-fpm

EXPOSE 9000

