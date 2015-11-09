#!/bin/bash

sudo apt-get update -y
sudo apt-get install -y apache2 && apt-get install -y git
sudo apt-get install -y php5-cli
sudo apt-get install -y curl php5-curl
sudo apt-get install -y php5-mysql

git clone https://github.com/Tonakiga/itmo-444-application-setup.git
git clone https://github.com/Tonakiga/itmo-444-images.git

sudo mv ./itmo-444-application-setup/index.html /var/www/html
sudo mv ./itmo-444-application-setup/page2.html /var/www/html
sudo mv ./itmo-444-images /var/www/images
sudo mv ./itmo-444-application-setup/*.php

curl -sS https://getcomposer.org/installer | sudo php &> /tmp/getcomposer.txt

sudo php composer.phar require aws/aws-sdk-php &> /tmp/runcomposer.txt

sudo mv vendor /var/www/html &> /tmp/movevendor.txt

sudo php /var/www/html/setup.php &> /tmp/database-setup.txt

sudo echo "Hello World!" > /tmp/hello.txt
