<?
include_once dirname(__FILE__)."/../config/config.php";

require_once WWWPATH."includes/login_check.php";
require_once WWWPATH."includes/filter_getpost.php";
include_once WWWPATH."includes/functions.php";

function macaddress($interface) {
	exec_log("/sbin/ifconfig -a $interface |grep 'HWaddr'", $output);
    if(preg_match("/[0-9a-fA-F]{2}:[0-9a-fA-F]{2}:[0-9a-fA-F]{2}:[0-9a-fA-F]{2}:[0-9a-fA-F]{2}:[0-9a-fA-F]{2}/", $output[0], $macs)) {
		$mac = $macs[0];
		return $mac;
    } else {
	    return NULL;
	    //die("Mac no encontrada.");
    }
}

#echo $io_out_iface;
#echo $io_in_iface;

$service = @$_GET['service'];
$action = @$_GET['action'];

$bin_killall = "/usr/bin/killall";

#sed -i 's/interface=.*/interface=wlan0/g' /usr/share/fruitywifi/conf/dnsmasq.conf

// HOSTAPD
if($service == "wireless"  and $ap_mode == "1") {
    if ($action == "start") {
//!Start 1

        exec_fruitywifi("$bin_killall hostapd");
        exec_fruitywifi("/bin/rm /var/run/hostapd-phy0/$io_in_iface");
        exec_fruitywifi("$bin_killall dnsmasq");

        exec_fruitywifi("/sbin/ifconfig $io_in_iface up");
        exec_fruitywifi("/sbin/ifconfig $io_in_iface up $io_in_ip netmask 255.255.255.0");

        exec_fruitywifi("echo 'nameserver $io_in_ip\nnameserver 8.8.8.8' > /etc/resolv.conf ");

        exec_fruitywifi("/usr/sbin/dnsmasq -C /usr/share/fruitywifi/conf/dnsmasq.conf");

        if ($hostapd_secure == 1) {
            //REPLACE SSID
            exec_fruitywifi("/bin/sed -i 's/^ssid=.*/ssid=".$hostapd_ssid."/g' /usr/share/fruitywificonf/hostapd-secure.conf");

            //REPLACE IFACE
            exec_fruitywifi("/bin/sed -i 's/^interface=.*/interface=".$io_in_iface."/g' /usr/share/fruitywificonf/hostapd-secure.conf");

            //REPLACE WPA_PASSPHRASE
            exec_fruitywifi("sed -i 's/wpa_passphrase=.*/wpa_passphrase=".$hostapd_wpa_passphrase."/g' /usr/share/fruitywificonf/hostapd-secure.conf");

            //EXTRACT MACADDRESS
	        $mac = macaddress($io_in_iface);
            //REPLACE MAC
            exec_fruitywifi("/bin/sed -i 's/^bssid=.*/bssid=".$mac."/g' /usr/share/fruitywifi/conf/hostapd-secure.conf");

			//Run hostapd
            exec_fruitywifi("/usr/sbin/hostapd -P /var/run/hostapd-phy0 -B /usr/share/fruitywifi/conf/hostapd-secure.conf");
        } else {
            //REPLACE SSID
            exec_fruitywifi("/bin/sed -i 's/^ssid=.*/ssid=".$hostapd_ssid."/g' /usr/share/fruitywificonf/hostapd.conf");

            //REPLACE IFACE
            exec_fruitywifi("/bin/sed -i 's/^interface=.*/interface=".$io_in_iface."/g' /usr/share/fruitywificonf/hostapd.conf");

            //REPLACE WPA_PASSPHRASE
            exec_fruitywifi("sed -i 's/wpa_passphrase=.*/wpa_passphrase=".$hostapd_wpa_passphrase."/g' /usr/share/fruitywificonf/hostapd.conf");

            //EXTRACT MACADDRESS
	        $mac = macaddress($io_in_iface);

            //REPLACE BSSID
            exec_fruitywifi("/bin/sed -i 's/^bssid=.*/bssid=".$mac."/g' /usr/share/fruitywifi/conf/hostapd.conf");

			//Run hostapd
            exec_fruitywifi("/usr/sbin/hostapd -P /var/run/hostapd-phy0 -B /usr/share/fruitywifi/conf/hostapd.conf");
        }

        exec_fruitywifi("/sbin/iptables -F");
        exec_fruitywifi("/sbin/iptables -t nat -F");
        exec_fruitywifi("/sbin/iptables -t mangle -F");
        exec_fruitywifi("/sbin/iptables -X");
        exec_fruitywifi("/sbin/iptables -t nat -X");
        exec_fruitywifi("/sbin/iptables -t mangle -X");

        exec_fruitywifi("/bin/echo 1 > /proc/sys/net/ipv4/ip_forward");
        exec_fruitywifi("/sbin/iptables -t nat -A POSTROUTING -o $io_out_iface -j MASQUERADE");

        // CLEAN DHCP log
        exec_fruitywifi("echo '' > /usr/share/fruitywifi/logs/dhcp.leases");

    } else if($action == "stop") {
//!Stop 1
        exec_fruitywifi("$bin_killall hostapd");
        exec_fruitywifi("/bin/rm /var/run/hostapd-phy0/$io_in_iface");
        exec_fruitywifi("$bin_killall dnsmasq");

        exec_fruitywifi("ip addr flush dev $io_in_iface");

		exec_fruitywifi("/sbin/ifconfig $io_in_iface down");

        exec_fruitywifi("/sbin/iptables -F");
        exec_fruitywifi("/sbin/iptables -t nat -F");
        exec_fruitywifi("/sbin/iptables -t mangle -F");
        exec_fruitywifi("/sbin/iptables -X");
        exec_fruitywifi("/sbin/iptables -t nat -X");
        exec_fruitywifi("/sbin/iptables -t mangle -X");

    }
}

// AIRCRACK
if($service == "wireless" and $ap_mode == "2") { // AIRCRACK (airbase-ng)
    if ($action == "start") {
//!Start 2
        exec_fruitywifi("/usr/bin/sudo /usr/sbin/airmon-ng stop mon0");

        exec_fruitywifi("$bin_killall airbase-ng");

        exec_fruitywifi("$bin_killall dnsmasq");

        exec_fruitywifi("echo 'nameserver $io_in_ip\nnameserver 8.8.8.8' > /etc/resolv.conf ");

        exec_fruitywifi("/usr/bin/sudo /usr/sbin/airmon-ng start $io_in_iface");

        //$exec = "/usr/sbin/airbase-ng -e $hostapd_ssid -c 2 mon0 > /dev/null &"; //-P (all)
        exec_fruitywifi("/usr/sbin/airbase-ng -e $hostapd_ssid -c 2 mon0 > /tmp/airbase.log &");

        //$exec = "/sbin/ifconfig at0 up 10.0.0.1 netmask 255.255.255.0";

        sleep(1);

        exec_fruitywifi("/sbin/ifconfig at0 up");
        exec_fruitywifi("/sbin/ifconfig at0 up $io_in_ip netmask 255.255.255.0");

        exec_fruitywifi("/usr/sbin/dnsmasq -C /usr/share/fruitywifi/conf/dnsmasq.conf");

        exec_fruitywifi("/sbin/iptables -F");
        exec_fruitywifi("/sbin/iptables -t nat -F");
        exec_fruitywifi("/sbin/iptables -t mangle -F");
        exec_fruitywifi("/sbin/iptables -X");
        exec_fruitywifi("/sbin/iptables -t nat -X");
        exec_fruitywifi("/sbin/iptables -t mangle -X");

        exec_fruitywifi("/bin/echo 1 > /proc/sys/net/ipv4/ip_forward");
        exec_fruitywifi("/sbin/iptables -t nat -A POSTROUTING -o $io_out_iface -j MASQUERADE");

        // CLEAN DHCP log
        exec_fruitywifi("echo '' > /usr/share/fruitywifi/logs/dhcp.leases");

    } else if($action == "stop") {

        exec_fruitywifi("$bin_killall airbase-ng");

        exec_fruitywifi("$bin_killall dnsmasq");

        exec_fruitywifi("/usr/bin/sudo /usr/sbin/airmon-ng stop mon0");

        exec_fruitywifi("ip addr flush dev at0");

        exec_fruitywifi("/sbin/ifconfig at0 down");

        exec_fruitywifi("/sbin/iptables -F");
        exec_fruitywifi("/sbin/iptables -t nat -F");
        exec_fruitywifi("/sbin/iptables -t mangle -F");
        exec_fruitywifi("/sbin/iptables -X");
        exec_fruitywifi("/sbin/iptables -t nat -X");
        exec_fruitywifi("/sbin/iptables -t mangle -X");

    }
}

// HOSTAPD MANA
if($service == "wireless"  and $ap_mode == "3") {
    if ($action == "start") {

        //unmanaged-devices=mac:<realmac>;interface-name:wlan2
        $mac = macaddress($io_in_iface);
        $ispresent = exec_log("grep '^unmanaged-devices' /etc/NetworkManager/NetworkManager.conf");

        exec_fruitywifi("sed -i '/unmanaged/d' /etc/NetworkManager/NetworkManager.conf");
        exec_fruitywifi("sed -i '/\[keyfile\]/d' /etc/NetworkManager/NetworkManager.conf");

        if ($ispresent == "") {
            exec_fruitywifi("echo '[keyfile]' >> /etc/NetworkManager/NetworkManager.conf");

            exec_fruitywifi("echo 'unmanaged-devices=mac:".$mac.";interface-name:".$io_in_iface."' >> /etc/NetworkManager/NetworkManager.conf");
        }

        exec_fruitywifi("$bin_killall hostapd");

        exec_fruitywifi("/bin/rm /var/run/hostapd-phy0/$io_in_iface");

        exec_fruitywifi("$bin_killall dnsmasq");

        exec_fruitywifi("/sbin/ifconfig $io_in_iface up");
        exec_fruitywifi("/sbin/ifconfig $io_in_iface up $io_in_ip netmask 255.255.255.0");

        exec_fruitywifi("echo 'nameserver $io_in_ip\nnameserver 8.8.8.8' > /etc/resolv.conf ");

        exec_fruitywifi("/usr/sbin/dnsmasq -C /usr/share/fruitywifi/conf/dnsmasq.conf");

        //Verifies if mana-hostapd is installed
        if ($hostapd_secure == 1) {

            if (file_exists("/usr/share/fruitywifi/www/modules/mana/includes/hostapd")) {
                include "/usr/share/fruitywifi/www/modules/mana/_info_.php";

                //REPLACE SSID
                exec_fruitywifi("/bin/sed -i 's/^ssid=.*/ssid=".$hostapd_ssid."/g' $mod_path/includes/conf/hostapd-secure.conf");

                //REPLACE IFACE
                exec_fruitywifi("/bin/sed -i 's/^interface=.*/interface=".$io_in_iface."/g' $mod_path/includes/conf/hostapd-secure.conf");

                //REPLACE WPA_PASSPHRASE
                exec_fruitywifi("sed -i 's/wpa_passphrase=.*/wpa_passphrase=".$hostapd_wpa_passphrase."/g' $mod_path/includes/conf/hostapd-secure.conf");

                //EXTRACT MACADDRESS
				$mac = macaddress($io_in_iface);

                //REPLACE MAC
                exec_fruitywifi("/bin/sed -i 's/^bssid=.*/bssid=".$mac."/g' $mod_path/includes/conf/hostapd-secure.conf");

                exec_fruitywifi("$bin_hostapd $mod_path/includes/conf/hostapd-secure.conf >> $mod_logs &");
            } else {
                exec_fruitywifi("/usr/sbin/hostapd -P /var/run/hostapd-phy0 -B /usr/share/fruitywifi/conf/hostapd-secure.conf");
            }

        } else {

            if (file_exists("/usr/share/fruitywifi/www/modules/mana/includes/hostapd")) {
                include "/usr/share/fruitywifi/www/modules/mana/_info_.php";

                //REPLACE SSID
                exec_fruitywifi("/bin/sed -i 's/^ssid=.*/ssid=".$hostapd_ssid."/g' $mod_path/includes/conf/hostapd.conf");

                //REPLACE IFACE
                exec_fruitywifi("/bin/sed -i 's/^interface=.*/interface=".$io_in_iface."/g' $mod_path/includes/conf/hostapd.conf");

                //EXTRACT MACADDRESS
				$mac = macaddress($io_in_iface);

                //REPLACE MAC
                exec_fruitywifi("/bin/sed -i 's/^bssid=.*/bssid=".$mac."/g' $mod_path/includes/conf/hostapd.conf");

                exec_fruitywifi("$bin_hostapd $mod_path/includes/conf/hostapd.conf >> $mod_logs &");
            } else {
                exec_fruitywifi("/usr/sbin/hostapd -P /var/run/hostapd-phy0 -B /usr/share/fruitywifi/conf/hostapd.conf");
            }

        }

        exec_fruitywifi("/sbin/iptables -F");
        exec_fruitywifi("/sbin/iptables -t nat -F");
        exec_fruitywifi("/sbin/iptables -t mangle -F");
        exec_fruitywifi("/sbin/iptables -X");
        exec_fruitywifi("/sbin/iptables -t nat -X");
        exec_fruitywifi("/sbin/iptables -t mangle -X");

        exec_fruitywifi("/bin/echo 1 > /proc/sys/net/ipv4/ip_forward");
        exec_fruitywifi("/sbin/iptables -t nat -A POSTROUTING -o $io_out_iface -j MASQUERADE");

        // CLEAN DHCP log
        exec_fruitywifi("echo '' > /usr/share/fruitywifi/logs/dhcp.leases");

    } else if($action == "stop") {

        // REMOVE lines from NetworkManager
        exec_fruitywifi("sed -i '/unmanaged/d' /etc/NetworkManager/NetworkManager.conf");
        exec_fruitywifi("sed -i '/\[keyfile\]/d' /etc/NetworkManager/NetworkManager.conf");

        exec_fruitywifi("$bin_killall hostapd");

        exec_fruitywifi("/bin/rm /var/run/hostapd-phy0/$io_in_iface");

        exec_fruitywifi("$bin_killall dnsmasq");

        exec_fruitywifi("ip addr flush dev $io_in_iface");

        exec_fruitywifi("/sbin/ifconfig $io_in_iface down");

        exec_fruitywifi("/sbin/iptables -F");
        exec_fruitywifi("/sbin/iptables -t nat -F");
        exec_fruitywifi("/sbin/iptables -t mangle -F");
        exec_fruitywifi("/sbin/iptables -X");
        exec_fruitywifi("/sbin/iptables -t nat -X");
        exec_fruitywifi("/sbin/iptables -t mangle -X");

    }
}

// HOSTAPD KARMA
if($service == "wireless"  and $ap_mode == "4") {
    if ($action == "start") {

        //unmanaged-devices=mac:<realmac>;interface-name:wlan2

        $mac = macaddress($io_in_iface);
        $ispresent = exec("grep '^unmanaged-devices' /etc/NetworkManager/NetworkManager.conf");

        exec_fruitywifi("sed -i '/unmanaged/d' /etc/NetworkManager/NetworkManager.conf");
        exec_fruitywifi("sed -i '/[keyfile]/d' /etc/NetworkManager/NetworkManager.conf");

        if ($ispresent == "") {
            exec_fruitywifi("echo '[keyfile]' >> /etc/NetworkManager/NetworkManager.conf");
            exec_fruitywifi("echo 'unmanaged-devices=mac:".$mac.";interface-name:".$io_in_iface."' >> /etc/NetworkManager/NetworkManager.conf");
        }

        exec_fruitywifi("$bin_killall hostapd");

        exec_fruitywifi("/bin/rm /var/run/hostapd-phy0/$io_in_iface");

        exec_fruitywifi("$bin_killall dnsmasq");

        exec_fruitywifi("/sbin/ifconfig $io_in_iface up");
        exec_fruitywifi("/sbin/ifconfig $io_in_iface up $io_in_ip netmask 255.255.255.0");

        exec_fruitywifi("echo 'nameserver $io_in_ip\nnameserver 8.8.8.8' > /etc/resolv.conf ");

        exec_fruitywifi("/usr/sbin/dnsmasq -C /usr/share/fruitywifi/conf/dnsmasq.conf");

        //Verifies if karma-hostapd is installed
        if ($hostapd_secure == 1) {

            if (file_exists("/usr/share/fruitywifi/www/modules/karma/includes/hostapd")) {
                include "/usr/share/fruitywifi/www/modules/karma/_info_.php";

                //REPLACE SSID
                exec_fruitywifi("/bin/sed -i 's/^ssid=.*/ssid=".$hostapd_ssid."/g' $mod_path/includes/conf/hostapd-secure.conf");

                //REPLACE IFACE
                exec_fruitywifi("/bin/sed -i 's/^interface=.*/interface=".$io_in_iface."/g' $mod_path/includes/conf/hostapd-secure.conf");

                //REPLACE WPA_PASSPHRASE
                exec_fruitywifi("sed -i 's/wpa_passphrase=.*/wpa_passphrase=".$hostapd_wpa_passphrase."/g' $mod_path/includes/conf/hostapd-secure.conf");

                //EXTRACT MACADDRESS
                $mac = macaddress($io_in_iface);

                //REPLACE MAC
                exec_fruitywifi("/bin/sed -i 's/^bssid=.*/bssid=".$mac."/g' $mod_path/includes/conf/hostapd-secure.conf");

                exec_fruitywifi("$bin_hostapd $mod_path/includes/conf/hostapd-secure.conf >> $mod_logs &");
            } else {
                exec_fruitywifi("/usr/sbin/hostapd -P /var/run/hostapd-phy0 -B /usr/share/fruitywifi/conf/hostapd-secure.conf");
            }

        } else {

            if (file_exists("/usr/share/fruitywifi/www/modules/karma/includes/hostapd")) {
                include "/usr/share/fruitywifi/www/modules/karma/_info_.php";

                //REPLACE SSID
                exec_fruitywifi("/bin/sed -i 's/^ssid=.*/ssid=".$hostapd_ssid."/g' $mod_path/includes/conf/hostapd.conf");

                //REPLACE IFACE
                exec_fruitywifi("/bin/sed -i 's/^interface=.*/interface=".$io_in_iface."/g' $mod_path/includes/conf/hostapd.conf");

                //EXTRACT MACADDRESS
                $mac = macaddress($io_in_iface);

                //REPLACE MAC
                exec_fruitywifi("/bin/sed -i 's/^bssid=.*/bssid=".$mac."/g' $mod_path/includes/conf/hostapd.conf");

                exec_fruitywifi("$bin_hostapd $mod_path/includes/conf/hostapd.conf >> $mod_logs &");
            } else {
                exec_fruitywifi("/usr/sbin/hostapd -P /var/run/hostapd-phy0 -B /usr/share/fruitywifi/conf/hostapd.conf");
            }

        }

        exec_fruitywifi("/sbin/iptables -F");
        exec_fruitywifi("/sbin/iptables -t nat -F");
        exec_fruitywifi("/sbin/iptables -t mangle -F");
        exec_fruitywifi("/sbin/iptables -X");
        exec_fruitywifi("/sbin/iptables -t nat -X");
        exec_fruitywifi("/sbin/iptables -t mangle -X");

        exec_fruitywifi("/bin/echo 1 > /proc/sys/net/ipv4/ip_forward");
        exec_fruitywifi("/sbin/iptables -t nat -A POSTROUTING -o $io_out_iface -j MASQUERADE");

        // CLEAN DHCP log
        exec_fruitywifi("echo '' > /usr/share/fruitywifi/logs/dhcp.leases");

    } else if($action == "stop") {

        // REMOVE lines from NetworkManager
        exec_fruitywifi("sed -i '/unmanaged/d' /etc/NetworkManager/NetworkManager.conf");
        exec_fruitywifi("sed -i '/[keyfile]/d' /etc/NetworkMxanager/NetworkManager.conf");

        exec_fruitywifi("$bin_killall hostapd");

        exec_fruitywifi("/bin/rm /var/run/hostapd-phy0/$io_in_iface");

        exec_fruitywifi("$bin_killall dnsmasq");

        exec_fruitywifi("ip addr flush dev $io_in_iface");

        exec_fruitywifi("/sbin/ifconfig $io_in_iface down");

        exec_fruitywifi("/sbin/iptables -F");
        exec_fruitywifi("/sbin/iptables -t nat -F");
        exec_fruitywifi("/sbin/iptables -t mangle -F");
        exec_fruitywifi("/sbin/iptables -X");
        exec_fruitywifi("/sbin/iptables -t nat -X");
        exec_fruitywifi("/sbin/iptables -t mangle -X");

    }
}

//! Kick
if($service == "wireless" and $action == "kick" and isset($_GET['station'])) {
	$s = $_GET['station'];
	if(preg_match("/[0-9a-fA-F]{2}[0-9a-fA-F]{2}[0-9a-fA-F]{2}[0-9a-fA-F]{2}[0-9a-fA-F]{2}[0-9a-fA-F]{2}/", $s)) {
		$station = $s[0].$s[1].':'.$s[2].$s[3].':'.$s[4].$s[5].':'.$s[6].$s[7].':'.$s[8].$s[9].':'.$s[10].$s[11];
		// Wireless 1 -> /usr/sbin/hostapd_cli -P /var/run/hostapd-phy0
		if ($ap_mode==1) $hostapdcli = "/usr/sbin/hostapd_cli -p /var/run/hostapd-phy0";
		// Karma 4 ->
		elseif ($ap_mode==4) $hostapdcli = "/usr/share/fruitywifi/www/modules/karma/includes/hostapd_cli -p /var/run/hostapd-phy0";
		else die('invalid ap mode');
		exec_fruitywifi("$hostapdcli disassociate $station");
	} else {
		die('mac address invalid');
	}
}

//! Ban
if($service == "wireless" and $action == "ban" and isset($_GET['station'])) {
	$s = $_GET['station'];
	if(preg_match("/[0-9a-fA-F]{2}[0-9a-fA-F]{2}[0-9a-fA-F]{2}[0-9a-fA-F]{2}[0-9a-fA-F]{2}[0-9a-fA-F]{2}/", $s)) {
		$station = $s[0].$s[1].':'.$s[2].$s[3].':'.$s[4].$s[5].':'.$s[6].$s[7].':'.$s[8].$s[9].':'.$s[10].$s[11];
		// Wireless 1 -> /usr/sbin/hostapd_cli -P /var/run/hostapd-phy0
		//if ($ap_mode==1) $hostapdcli = "/usr/sbin/hostapd_cli -p /var/run/hostapd-phy0";
		// Karma 4 ->
		if ($ap_mode==4) $hostapdcli = "/usr/share/fruitywifi/www/modules/karma/includes/hostapd_cli -p /var/run/hostapd-phy0";
		else die('invalid ap mode');
		exec_fruitywifi("$hostapdcli karma_add_black_mac $station");
		sleep(0.5);
		exec_fruitywifi("$hostapdcli disassociate $station");
	} else {
		die('mac address invalid');
	}
}


header('Location: ../action.php');

?>