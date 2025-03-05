FROM nginx:latest

RUN apt-get update \
	&& apt-get -y install openssl \
	&& rm -rf /var/lib/apt/lists/* \
RUN mkdir /var/www/project
COPY ./public /var/www/project/public
COPY ./docker/nginx/config/vhost.prod.conf /etc/nginx/conf.d/default.conf
COPY ./docker/nginx/certs /etc/nginx/certs

#WORKDIR /var/www/project/docker/nginx/certs
#RUN openssl req -subj '/CN=localhost' -x509 -newkey rsa:4096 -nodes -keyout /etc/nginx/certs/key.pem -out /etc/nginx/certs/cert.pem -days 365
