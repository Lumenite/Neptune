FROM node:lts-alpine3.11

MAINTAINER "Mohammed Mudassir<md@thebots.io>"
ARG app_env="local"
ARG projects=""

ENV APP_ENV="${app_env}"
ENV PROJECTS="${projects}"

WORKDIR /code

RUN apk add bash &&\
    apk upgrade &&\
    rm -rf /var/cache/apk/*

# Initializing post installation script
COPY ./setup.sh /etc/setup.sh
RUN chmod +x /etc/setup.sh

RUN chown -R node:node /code
USER node


CMD /etc/setup.sh
