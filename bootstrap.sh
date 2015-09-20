#!/usr/bin/env bash
export DEBIAN_FRONTEND=noninteractive

# packages
apt-get update
apt-get install -y mysql-server-5.5 php5-mysql git nginx php5-fpm

# database
echo "
CREATE DATABASE acserver;
GRANT ALL ON acserver.* TO acserver@'%';
FLUSH PRIVILEGES;
" | mysql -u root

mysql -u acserver acserver < /vagrant/blank.mysql
cp /etc/mysql/my.cnf /tmp/my.cnf
sudo sed s/127.0.0.1/0.0.0.0/g < /tmp/my.cnf > /etc/mysql/my.cnf
sudo service mysql

# app setup
mkdir -p /var/www
ln -fs /vagrant /var/www/acserver
cp /vagrant/application/config/database.php.vagrant /vagrant/application/config/database.php

# web server
cp /vagrant/acserver.nginx /etc/nginx/sites-available/acserver
ln -fs /etc/nginx/sites-available/acserver /etc/nginx/sites-enabled/acserver
sudo service nginx restart

