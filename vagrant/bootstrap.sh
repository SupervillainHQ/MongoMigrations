#!/bin/bash
#


#!/bin/bash
#

# INSTALL SECTION - should only be run at first provision/is not yet ready for multiple provision attempts

# avoid upgrading packages that requires interaction (the point of this bootstrap is to avoid interaction)
apt-mark hold grub-common grub-pc grub-pc-bin grub2-common
apt-mark hold kbd keyboard-configuration
apt-mark hold sudo

apt-get update
apt-get upgrade -y

locale-gen "en_US.UTF-8"
update-locale LANG=en_US.UTF-8 LANGUAGE=en_US.UTF-8 LC_MESSAGES=en_US.UTF-8 LC_ALL=en_US.UTF-8
update-alternatives --set editor /usr/bin/vim.basic

apt-get install -y curl vim wget git ntp software-properties-common python-software-properties

# phalcon repo
curl -s https://packagecloud.io/install/repositories/phalcon/stable/script.deb.sh | sudo bash
apt-add-repository ppa:phalcon/stable -y
apt-add-repository ppa:chris-lea/redis-server -y
# mongo repos
apt-key adv --keyserver hkp://keyserver.ubuntu.com:80 --recv 9DA31620334BD75D9DCB49F368818C72E52529D4
echo "deb http://repo.mongodb.org/apt/ubuntu xenial/mongodb-org/4.0 multiverse" | sudo tee /etc/apt/sources.list.d/mongodb-org-4.0.list

apt-get update

apt-get install -y php7.0 php7.0-dev php7.0-zip php7.0-fpm php7.0-cli php7.0-curl php7.0-gd php7.0-intl php7.0-mbstring php7.0-xml
apt-get install -y php-msgpack php-gettext php-xdebug
apt-get install -y gettext nodejs npm zip
apt-get install -y redis-server php-redis

apt-get install -y mongodb-org php-mongodb

# composer
curl -sS http://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
chown root:root /usr/local/bin/composer
chmod +x /usr/local/bin/composer

# phpunit
wget https://phar.phpunit.de/phpunit.phar -O /usr/local/bin/phpunit
chown root:root /usr/local/bin/phpunit
chmod +x /usr/local/bin/phpunit

# phalcon
apt-get install -y php7.0-phalcon

# fix missing node symlink
ln -s /usr/bin/nodejs /usr/local/bin/node


# CONFIG SECTION - should be run at every provision

systemctl start ntp.service

a2enmod setenvif actions rewrite

# fix npm permissions (https://docs.npmjs.com/resolving-eacces-permissions-errors-when-installing-packages-globally)
mkdir /home/vagrant/.npm-global
chown vagrant:vagrant /home/vagrant/.npm-global

npm config set prefix '/home/vagrant/.npm-global'
# ensure relevant bin/ folders is in our path (npm)
grep -q -F 'PATH="/home/vagrant/.npm-global/bin:$PATH"' /home/vagrant/.profile || echo 'PATH="/home/vagrant/.npm-global/bin:$PATH"' >> /home/vagrant/.profile

# install/update node dependencies
#npm install -g aglio

# mongod upstart script
cp /vagrant/mongodb.service /etc/systemd/system/mongodb.service

# Start on reboot
systemctl enable mongod

# Start background service now
systemctl start mongod

systemctl daemon-reload

# same with the cli ini
unlink /etc/php/7.0/cli/php.ini
ln -fs /vagrant/php-cli.ini /etc/php/7.0/cli/php.ini

# create required folders
#mkdir -p /var/backups/tasklift
#chown www-data -R /var/backups/tasklift

# ensure relevant bin/ folders is in our path (composer)
grep -q -F 'PATH="/opt/mongo-migrations/vendor/bin:$PATH"' /home/vagrant/.profile || echo 'PATH="/opt/mongo-migrations/vendor/bin:$PATH"' >> /home/vagrant/.profile

# unmark upgrade packages that requires interaction manually, so you can manually upgrade them via a terminal
apt-mark unhold grub-common grub-pc grub-pc-bin grub2-common
apt-mark unhold kbd keyboard-configuration
apt-mark unhold sudo



