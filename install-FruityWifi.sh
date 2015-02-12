#!/bin/bash

# CONFIG
# --------------------------------------------------------
# Setup log path. default=/usr/share/fruitywifi/logs
# --------------------------------------------------------
fruitywifi_log_path="/usr/share/fruitywifi/logs"
# --------------------------------------------------------
# 'all' option installs nginx webserver on ports 80 | 443,
# and it also installs FruityWifi on ports 8000 | 8443.
# If 'all' is not specified then only installs 8000 | 8443
# --------------------------------------------------------
fruitywifi_webserver="all"
# --------------------------------------------------------

echo
echo "+------------------------------------------------------------+"
echo "| FruityWifi Installer                                       |"
echo "+------------------------------------------------------------+"

find FruityWifi -type d -exec chmod 755 {} \;
find FruityWifi -type f -exec chmod 644 {} \;

root_path=`pwd`

mkdir -v tmp-install
cd tmp-install

echo
echo "+------------------------------+"
echo "| apt-get update               |"
echo "+------------------------------+"
apt-get update

echo

echo "+------------------------------+"
echo "| Creates user fruitywifi      |"
echo "+------------------------------+"
adduser --disabled-password --quiet --system --home /var/run/fruitywifi --no-create-home --gecos "FruityWifi" --group fruitywifi
mkdir -v -p /var/run/fruitywifi

echo "[fruitywifi user has been created]"
echo

echo "+------------------------------+"
echo "| apt-get install              |"
echo "+------------------------------+"
apt-get -y install gettext make intltool build-essential automake autoconf uuid uuid-dev php5-curl php5-cli dos2unix curl sudo unzip

cmd=`gcc --version|grep "4.7"`
if [[ $cmd == "" ]]
then
    echo "+------------------------------+"
    echo "| Installing gcc 4.7           |"
    echo "+------------------------------+"

    apt-get -y install gcc-4.7
    apt-get -y install g++-4.7
    update-alternatives --install /usr/bin/gcc gcc /usr/bin/gcc-4.7 40 --slave /usr/bin/g++ g++ /usr/bin/g++-4.7

    echo "[gcc setup completed]"

else
    echo "+------------------------------+"
    echo "| gcc 4.7 already installed    |"
    echo "+------------------------------+"
fi

echo

if [ ! -f "/usr/sbin/dnsmasq" ]
then
    echo "+------------------------------+"
    echo "| Installing dnsmasq           |"
    echo "+------------------------------+"

    # INSTALL DNSMASQ
    apt-get -y install dnsmasq

    echo "[dnsmasq setup completed]"

else
    echo "+------------------------------+"
    echo "| dnsmasq already installed    |"
    echo "+------------------------------+"
fi

echo

if [ ! -f "/usr/sbin/hostapd" ]
then
    echo "+------------------------------+"
    echo "| Installing hostapd           |"
    echo "+------------------------------+"

    # INSTALL HOSTAPD
    apt-get -y install hostapd

    echo "[hostapd setup completed]"

else
    echo "+------------------------------+"
    echo "| hostapd already installed    |"
    echo "+------------------------------+"
fi

echo

#if [ ! -f "/usr/sbin/airmon-ng" ] &&  [ ! -f "/usr/local/sbin/airmon-ng" ]
if [[ `aircrack-ng  | grep 'Aircrack-ng' | grep '1.2 rc1'` == "" ]]
then
    echo "+------------------------------+"
    echo "| Installing aircrack-ng       |"
    echo "+------------------------------+"

    apt-get -y install libssl-dev wireless-tools iw

    #Aircrack-ng 1.2-beta1
    #wget http://download.aircrack-ng.org/aircrack-ng-1.2-beta1.tar.gz
    #tar -zxvf aircrack-ng-1.2-beta1.tar.gz
    #cd aircrack-ng-1.2-beta1

    #Aircrack-ng 1.2-rc1
    wget http://download.aircrack-ng.org/aircrack-ng-1.2-rc1.tar.gz
    tar -zxf aircrack-ng-1.2-rc1.tar.gz
    cd aircrack-ng-1.2-rc1

    make
    make install
    ln -s /usr/local/sbin/airmon-ng /usr/sbin/airmon-ng
    ln -s /usr/local/sbin/airbase-ng /usr/sbin/airbase-ng
    #cd ../
    cd tmp-install

    echo "[aircrack-ng setup completed]"

else
    echo "+------------------------------+"
    echo "|aircrack-ng already installed |"
    echo "+------------------------------+"
fi

echo

# BACK TO ROOT-INSTALL FOLDER
cd $root_path

echo "+------------------------------+"
echo "| Installing Nginx             |"
echo "+------------------------------+"

# NGINX INSTALL
apt-get -y install nginx php5-fpm
echo

# SSL
if [ ! -f "/etc/nginx/ssl/nginx.key" ] &&  [ ! -f "/etc/nginx/ssl/nginx.crt" ]
then
    echo "+------------------------------+"
    echo "| Create Nginx ssl certificate |"
    echo "+------------------------------+"
    cd $root_path
    mkdir -p -v /etc/nginx/ssl
    openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout /etc/nginx/ssl/nginx.key -out /etc/nginx/ssl/nginx.crt
fi

# Configure nginx
rm /etc/nginx/sites-enabled/default
mv -v /etc/nginx/nginx.conf /etc/nginx/nginx.conf.bak
cp -v nginx-setup/nginx.conf /etc/nginx/
cp -v nginx-setup/FruityWifi /etc/nginx/sites-enabled/
cp -v nginx-setup/fpm/8000.conf /etc/php5/fpm/pool.d/
cp -v nginx-setup/fpm/8443.conf /etc/php5/fpm/pool.d/

if [ $fruitywifi_webserver == "all" ]
then
    mkdir -p -v /var/www/
    echo "." > /var/www/index.php
    chown -R fruitywifi:fruitywifi /var/www/
    cp -v nginx-setup/default /etc/nginx/sites-enabled/
    cp -v nginx-setup/fpm/80.conf /etc/php5/fpm/pool.d/
    cp -v nginx-setup/fpm/443.conf /etc/php5/fpm/pool.d/
fi

/etc/init.d/nginx restart
/etc/init.d/php5-fpm restart

echo "[nginx setup completed]"
echo

echo "+------------------------------+"
echo "| BACKUP                       |"
echo "+------------------------------+"
cmd=`date +"%Y-%m-%d-%k-%M-%S"`
if [ -d "/usr/share/fruitywifi" ]
then
    mv -v /usr/share/fruitywifi fruitywifi.BAK.$cmd
fi

if [ -d "/usr/share/FruityWifi" ]
then
    mv -v /usr/share/FruityWifi FruityWifi.BAK.$cmd
fi

echo

echo "+------------------------------+"
echo "| Setup FruityWifi             |"
echo "+------------------------------+"
cd $root_path

echo "+------------------------------+"
echo "| Config log path              |"
echo "+------------------------------+"

mkdir -p -v $fruitywifi_log_path

sed -i "s,'LOGPATH'\, \".*\",'LOGPATH'\, \"$fruitywifi_log_path\",g" FruityWifi/www/config/config.php

EXEC="s,^log-facility=.*,log-facility="$fruitywifi_log_path"/dnsmasq.log,g"
sed -i $EXEC FruityWifi/conf/dnsmasq.conf

EXEC="s,^dhcp-leasefile=.*,dhcp-leasefile="$fruitywifi_log_path"/dhcp.leases,g"
sed -i $EXEC FruityWifi/conf/dnsmasq.conf

EXEC="s,^Defaults logfile =.*,Defaults logfile = "$fruitywifi_log_path"/sudo.log,g"
sed -i $EXEC sudo-setup/fruitywifi

echo "[logs setup completed]"
echo

echo "+------------------------------+"
echo "| Setup Sudo                   |"
echo "+------------------------------+"
cd $root_path
cp -a -v sudo-setup/fruitywifi /etc/sudoers.d/

echo "[sudo setup completed]"
echo

mkdir -v -p /usr/share/fruitywifi/
cp -a ./FruityWifi/* /usr/share/fruitywifi/
#ln -s $fruitywifi_log_path /usr/share/FruityWifiwww/logs
#ln -s /usr/share/fruitywifi/ /usr/share/FruityWifi

# Change Permissions
chown -R fruitywifi:fruitywifi /usr/share/fruitywifi
chown -R fruitywifi:fruitywifi $log_path

echo

# START/STOP SERVICES
echo "+------------------------------+"
echo "| Start Services               |"
echo "+------------------------------+"
update-rc.d ssh defaults
update-rc.d nginx defaults
update-rc.d php5-fpm defaults
update-rc.d ntp defaults

/etc/init.d/nginx restart
/etc/init.d/php5-fpm restart


apt-get -y remove ifplugd
#update-rc.d ifplugd disable 2 3 4 5
#service ifplugd stop

echo

#echo "GitHub: https://github.com/xtr4nge/FruityWifi"
#echo "Twitter: @xtr4nge, @FruityWifi"
echo "ENJOY!"
echo
