#!/bin/bash
# Using Trusty64 Ubuntu

export app_base=/var/www
export tmp_base=$app_base/www_tmp
export www_base=$app_base/vagrant

#
# Add Vagrant user to the sudoers group
#
echo 'vagrant ALL=(ALL) NOPASSWD: ALL' >> /etc/sudoers

#
# Set up swap partition
#
fallocate -l 2G /swapfile
chmod 600 /swapfile
mkswap /swapfile
swapon /swapfile
echo "/swapfile   none    swap    sw    0   0" >> /etc/fstab

# Aptitude helper functions (if they don't already exist)
apt-get -y install software-properties-common python-software-properties

#
# Custom PPA repos
#

# PHP 5.6
#sudo add-apt-repository -y ppa:ondrej/php5-5.6

# Phalcon PPA
add-apt-repository -y ppa:phalcon/stable

# MariaDB PPA
sudo apt-key adv --recv-keys --keyserver hkp://keyserver.ubuntu.com:80 0xcbcb082a1bb943db
sudo add-apt-repository 'deb [arch=amd64,i386] http://nyc2.mirrors.digitalocean.com/mariadb/repo/10.1/ubuntu trusty main'
# touch /etc/apt/sources.list.d/pgdg.list
# echo -e "deb http://apt.postgresql.org/pub/repos/apt/ trusty-pgdg main" | tee -a /etc/apt/sources.list.d/pgdg.list > /dev/null
# wget --quiet -O - https://www.postgresql.org/media/keys/ACCC4CF8.asc | apt-key add -

# rm /var/lib/apt/lists/* -f
apt-get update


#
# Setup locales
#
echo -e "LC_CTYPE=en_US.UTF-8\nLC_ALL=en_US.UTF-8\nLANG=en_US.UTF-8\nLANGUAGE=en_US.UTF-8" | tee -a /etc/environment > /dev/null
locale-gen en_US en_US.UTF-8
dpkg-reconfigure locales

export LANGUAGE=en_US.UTF-8
export LANG=en_US.UTF-8
export LC_ALL=en_US.UTF-8

#
# Trigger mlocate reindex.
#
updatedb

#
# Hostname
#
hostnamectl set-hostname phalcon-vm

#
# MySQL with root:<no password>
#
export DEBIAN_FRONTEND=noninteractive
apt-get -q -y install mariadb-server mariadb-client php5-mysql

#
# Apache
#
apt-get -y install apache2 libapache2-mod-php5


#
# PHP
#
apt-get install -y php5 php5-cli php-pear php5-mcrypt php5-curl php5-intl php5-gd php5-imagick
php5enmod mcrypt intl curl

# Update PECL channel
pecl channel-update pecl.php.net

#
# Apc
#
# apt-get -y install php-apc php5-apcu
# echo 'apc.enable_cli = 1' | tee -a /etc/php5/mods-available/apcu.ini > /dev/null

#
# Memcached
#
apt-get install -y memcached php5-memcached php5-memcache

#
# MongoDB
#
#apt-get install -y mongodb-clients mongodb-server
#pecl install mongo < /dev/null &
#echo 'extension = mongo.so' | tee /etc/php5/mods-available/mongo.ini > /dev/null

#
# PostgreSQL with postgres:postgres
# but "psql -U postgres" command don't ask password
#
#apt-get install -y postgresql-9.4 php5-pgsql
#cp /etc/postgresql/9.4/main/pg_hba.conf /etc/postgresql/9.4/main/pg_hba.bkup.conf
#sudo -u postgres psql -c "ALTER USER postgres PASSWORD 'postgres'" > /dev/null
#sed -i.bak -E 's/local\s+all\s+postgres\s+peer/local\t\tall\t\tpostgres\t\ttrust/g' /etc/postgresql/9.4/main/pg_hba.conf
#service postgresql restart

#
# SQLite
#
#apt-get -y install sqlite php5-sqlite

#
# Beanstalkd
#
#apt-get -y install beanstalkd

#
# YAML
#
#apt-get install libyaml-dev
#(CFLAGS="-O1 -g3 -fno-strict-aliasing"; pecl install yaml < /dev/null &)
#echo 'extension = yaml.so' | tee /etc/php5/mods-available/yaml.ini > /dev/null
#php5enmod yaml

#
# Sphinx Search Server
#
apt-get -y install sphinxsearch

#
# Utilities
#
apt-get -y install curl htop git unzip vim

#
# Install Phalcon Framework
#

apt-get -y install php5-phalcon

#
# Redis
#
# Allow us to remote from Vagrant with port
#
apt-get install -y redis-server redis-tools php5-redis
cp /etc/redis/redis.conf /etc/redis/redis.bkup.conf
sed -i 's/bind 127.0.0.1/bind 0.0.0.0/' /etc/redis/redis.conf
service redis-server restart

#
# MySQL configuration
# Allow us to remote from Vagrant with port
#
cp /etc/mysql/my.cnf /etc/mysql/my.bkup.cnf

# Note: Since the MySQL bind-address has a tab character I comment out the end line
sed -i 's/bind-address/bind-address = 0.0.0.0#/' /etc/mysql/my.cnf

#
# Grant all privilege to root for remote access
#
mysql -u root -Bse "GRANT ALL PRIVILEGES ON *.* TO 'root'@'%' IDENTIFIED BY '' WITH GRANT OPTION;"
mysql -u root -Bse "CREATE DATABASE fa_sandbox CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

service mysql restart

#
# Create Cache Folders
#

mkdir -p $tmp_base
mkdir -p $tmp_base/cache
mkdir -p $tmp_base/sessions
mkdir -p $tmp_base/proxies

touch $tmp_base/access.log
touch $tmp_base/error.log
touch $tmp_base/php_errors.log

chown -R www-data:www-data $tmp_base/
chmod -R 777 $tmp_base

#
# Create Config Files
#

if [ ! -f $www_base/app/config/apis.conf.php ]
then
	cp $www_base/app/config/apis.conf.sample.php $www_base/app/config/apis.conf.php
fi

if [ ! -f $www_base/app/config/db.conf.php ]
then
	cp $www_base/app/config/db.conf.sample.php $www_base/app/config/db.conf.php
fi

if [ ! -f $www_base/app/config/cache.conf.php ]
then
	cp $www_base/app/config/cache.conf.sample.php $www_base/app/config/cache.conf.php
fi

if [ ! -f $www_base/app/config/sphinx.conf.php ]
then
	cp $www_base/app/config/sphinx.conf.sample.php $www_base/app/config/sphinx.conf.php
fi

#
# Composer for PHP
#
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer

#
# Apache VHost
#
cd ~
echo '<VirtualHost *:80>
    DocumentRoot /var/www/vagrant/web
    ErrorLog  /var/www/www_tmp/error.log
    CustomLog /var/www/www_tmp/access.log combined

    SetEnv FA_APPLICATION_ENV development
</VirtualHost>

<Directory "/var/www/vagrant/web">
    Options Indexes Followsymlinks
    AllowOverride All
    Require all granted
</Directory>' > vagrant.conf

mv vagrant.conf /etc/apache2/sites-available
a2enmod rewrite

#
# Update PHP Error Reporting
#
sudo sed -i 's/short_open_tag = Off/short_open_tag = On/' /etc/php5/apache2/php.ini
sudo sed -i 's/error_reporting = E_ALL & ~E_DEPRECATED & ~E_STRICT/error_reporting = E_ALL/' /etc/php5/apache2/php.ini
sudo sed -i 's/display_errors = Off/display_errors = On/' /etc/php5/apache2/php.ini

#  Append session save location to /tmp to prevent errors in an odd situation..
sudo sed -i '/\[Session\]/a session.save_path = "/tmp"' /etc/php5/apache2/php.ini

#
# Update PHP max size for uploads
#

echo "upload_max_filesize = 10M" >> /etc/php5/apache2/conf.d/user.ini
echo "post_max_size = 10M" >> /etc/php5/apache2/conf.d/user.ini

#
# Reload apache
#
sudo a2ensite vagrant
sudo a2dissite 000-default
sudo service apache2 restart
sudo service mongodb restart

#
# Cleanup
#
sudo apt-get autoremove -y

sudo usermod -a -G www-data vagrant

#
# Site Spin-Up
#

# Initialize Composer
cd $www_base
composer install

# Clean up Composer's Phalcon dependencies
rm -rf $www_base/vendor/phalcon/devtools/ide/0.*
rm -rf $www_base/vendor/phalcon/devtools/ide/1.*

# Drop and recreate database using Doctrine as authoritative source.
cd $www_base/util

sudo -u vagrant php doctrine.php orm:schema-tool:drop --force
sudo -u vagrant php doctrine.php orm:schema-tool:create

# Clear the application cache.
sudo -u vagrant php cli.php cache:clear

# Create development environment user
sudo -u vagrant php cli.php dev:deploy

# Set up Sphinx
service cron stop
service sphinxsearch stop

cp $www_base/util/vagrant_sphinx.conf /etc/sphinxsearch/sphinx.conf
sed -i "s/START=no/START=yes/" /etc/default/sphinxsearch
sed -i "s/--chuid sphinxsearch/--chuid root/" /etc/init.d/sphinxsearch
sed -i "s/--user sphinxsearch/--user root/" /etc/init.d/sphinxsearch

crontab $www_base/util/vagrant_sphinx_cron

indexer --all

service sphinxsearch start
service cron start

# Set this as the default directory
echo "cd /var/www/vagrant" >> /home/vagrant/.bashrc