#!/bin/bash

echo "installing SSLStrip..."

# INSTALL SSLStrip @xtr4nge fork

apt-get -y install python-twisted


if [ ! -f "/usr/share/fruitywifi/www/modules/sslstrip/includes/sslstrip" ]
then
    wget https://github.com/xtr4nge/sslstrip/archive/master.zip -O sslstrip-master.zip
    unzip sslstrip-master.zip
    chmod 755 sslstrip-master/sslstrip.py
    #mv sslstrip-master /usr/share/FruityWifi-sslstrip
    ln -s sslstrip-master/sslstrip.py sslstrip
fi

chmod 755 sslstrip-master/sslstrip.py
ln -s sslstrip-master/sslstrip.py sslstrip

echo "..DONE.."
exit
