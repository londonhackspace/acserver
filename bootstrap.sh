#!/usr/bin/env bash

# packages
apt-get update
apt-get install -y mysql-server php5-mysql avahi-daemon git nginx php5-fpm

# app setup
ln -s /vagrant /var/www/acserver

# web server
cp acserver.nginx /etc/nginx/sites-available/acserver
ln -s /etc/nginx/sites-available/acserver /etc/nginx/sites-enabled/acserver
service nginx restart

