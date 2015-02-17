#!/bin/bash

echo "installing Hostapd/Karma Dependencies..."
# DEP HOSTAPD-KARMA
apt-get -y install gcc-4.7
apt-get -y install g++-4.7
update-alternatives --install /usr/bin/gcc gcc /usr/bin/gcc-4.7 40 --slave /usr/bin/g++ g++ /usr/bin/g++-4.7

apt-get -y install hostapd
apt-get -y install libnl1 libnl-dev libssl-dev

if [ ! -f "/usr/share/fruitywifi/www/modules/karma/includes/hostapd" ]
then
    echo "installing Hostapd/Karma..."
    # INSTALL HOSTAPD-KARMA
    wget http://www.digininja.org/files/hostapd-1.0-karma.tar.bz2 -O hostapd-1.0-karma.tar.bz2

    bunzip2 hostapd-1.0-karma.tar.bz2
    tar xvf hostapd-1.0-karma.tar
    cd hostapd-1.0-karma/hostapd
    make

    cp hostapd ../../
    cp hostapd_cli ../../
else
    echo "Hostapd/Karma already installed"
    chmod +x /usr/share/fruitywifi/www/modules/karma/includes/hostapd
    chmod +x /usr/share/fruitywifi/www/modules/karma/includes/hostapd_cli
fi

echo "..DONE.."
exit
