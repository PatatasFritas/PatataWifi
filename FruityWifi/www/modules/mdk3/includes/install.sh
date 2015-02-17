#!/bin/bash

mkdir tmp_install
cd tmp_install


if [ ! -f "./mdk3-v6/mdk3" ]
then
    echo
    echo "Downloading mdk3..."
    #wget http://homepages.tu-darmstadt.de/~p_larbig/wlan/mdk3-v6.tar.bz2
    wget http://www.fruitywifi.com/downloads/mdk3-v6.tar.bz2

    echo
    echo "Extracting mdk3..."
    bunzip2 mdk3-v6.tar.bz2
    tar xvf mdk3-v6.tar

    echo
    echo "Compiling mdk3"
    cd mdk3-v6/
    make
    cp mdk3 /usr/bin/
    cd ../
fi

chmod 755 ./mdk3-v6/mdk3
cp ./mdk3-v6/mdk3cd .. /usr/bin/

#echo
#echo "Installing libssl-dev..."
#apt-get -y install libssl-dev

#echo
#echo "Downloading aircrack-ng..."
#wget http://download.aircrack-ng.org/aircrack-ng-1.2-beta1.tar.gz

#echo
#echo "Extracting aircrack-ng..."
#tar -zxvf aircrack-ng-1.2-beta1.tar.gz

#echo
#echo "Compiling aircrack-ng..."
#cd aircrack-ng-1.2-beta1
#make
#cd ../

echo
echo "..DONE.."
