#!/bin/bash

echo "installing SSL Split..."

# INSTALL SSL Split

apt-get -y install libevent-dev

if [ ! -f "/usr/share/fruitywifi/www/modules/sslsplit/includes/sslsplit" ]
then
    wget https://github.com/droe/sslsplit/archive/master.zip -O sslsplit-master.zip
    unzip sslsplit-master.zip
    cd sslsplit-master
    make
    #make install
    cp sslsplit .. -v
    cd ..
    rm -rf sslsplit-master
    rm -v sslsplit-master.zip
fi

chmod 755 sslsplit -v


# make certs
if [ ! -f "/usr/share/fruitywifi/www/modules/sslsplit/includes/ca.key" ]
then
    echo "- ca.key"
    #openssl genrsa -out ca.key 4096
fi

if [ ! -f "/usr/share/fruitywifi/www/modules/sslsplit/includes/ca.crt" ]
then
    echo "- ca.key"
    #openssl req -new -x509 -days 1826 -key ca.key -out ca.crt
fi

echo "..DONE.."
exit
