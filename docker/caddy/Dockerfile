FROM abiosoft/caddy:no-stats

MAINTAINER "Mohammed Mudassir<md@thebots.io>"

ARG projects=""
ARG version="0.11.4"
ARG tls_email_id="self_signed"
ENV PROJECTS="${projects}"
ENV TLS_EMAIL_ID="${tls_email_id}"

LABEL caddy_version="$version"

RUN apk add --no-cache \
    mailcap \
    bash \
    ca-certificates \
    git \
    openssh-client \
    tzdata

RUN mkdir /etc/caddy

# validate install
RUN /usr/bin/caddy -version
RUN /usr/bin/caddy -plugins

RUN > /etc/Caddyfile

RUN mkdir /var/log/caddy
VOLUME /root/.caddy /srv
COPY ./boot.sh /etc/boot.sh

WORKDIR /code

CMD ["/etc/boot.sh", "--conf", "/etc/Caddyfile", "--log", "stdout", "--agree=true"]

EXPOSE 80 443 2015
