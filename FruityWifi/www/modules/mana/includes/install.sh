#!/bin/bash

echo "installing Hostapd/Mana Dependencies..."
# DEP HOSTAPD-MANA

apt-get -y install hostapd
apt-get -y install libnl1 libnl-dev libssl-dev

#apt-get -y install libnl-dev libssl-dev #isc-dhcp-server tinyproxy  apache2 macchanger python-dnspython python-pcapy dsniff stunnel4

apt-get -y install gcc-4.7
apt-get -y install g++-4.7
update-alternatives --install /usr/bin/gcc gcc /usr/bin/gcc-4.7 40 --slave /usr/bin/g++ g++ /usr/bin/g++-4.7


if [ ! -f "/usr/share/fruitywifi/www/modules/mana/includes/hostapd" ]
then
    echo "installing Hostapd/Mana..."
    # INSTALL HOSTAPD-MANA
    wget https://github.com/sensepost/mana/archive/master.zip -O hostapd-mana.zip

    unzip hostapd-mana.zip

    cd mana-master
    make

    cp hostapd-manna/hostapd/hostapd ../
    cp hostapd-manna/hostapd/hostapd_cli ../
fi

chmod 755 hostapd
chmod 755 hostapd_cli

echo "..DONE.."
exit
