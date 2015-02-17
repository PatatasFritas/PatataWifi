#!/bin/bash

echo "installing Responder..."
apt-get -y install python-openssl

if [ ! -d "Responder-master" ]
then
    wget https://github.com/SpiderLabs/Responder/archive/master.zip -O Responder-master.zip
    unzip Responder-master.zip
fi

/bin/sed -i 's/^SQL.*/SQL = On/g' Responder-master/Responder.conf
/bin/sed -i 's/^SMB.*/SMB = On/g' Responder-master/Responder.conf
/bin/sed -i 's/^FTP.*/FTP = On/g' Responder-master/Responder.conf
/bin/sed -i 's/^LDAP.*/LDAP = On/g' Responder-master/Responder.conf

/bin/sed -i 's/^HTTP .*/HTTP = Off/g' Responder-master/Responder.conf
/bin/sed -i 's/^HTTPS.*/HTTPS = Off/g' Responder-master/Responder.conf
/bin/sed -i 's/^DNS.*/DNS = Off/g' Responder-master/Responder.conf

/bin/sed -i 's/^SessionLog.*/SessionLog = \/usr\/share\/FruityWifi\/logs\/responder.log/g' Responder-master/Responder.conf

chmod 755 Responder-master/Responder.py

/bin/sed -i 's/#! \/usr\/bin\/env python/#!\/usr\/bin\/python/g' Responder-master/Responder.py

echo "..DONE.."
exit
