<?
include_once dirname(__FILE__)."/../config/config.php";

require_once WWWPATH."/includes/login_check.php";
require_once WWWPATH."/includes/filter_getpost.php";
include_once WWWPATH."/includes/functions.php";

function macaddress($interface) {
	exec_log(BIN_IFCONFIG." -a $interface |grep 'HWaddr'", $output);
    if(preg_match("/[0-9a-fA-F]{2}:[0-9a-fA-F]{2}:[0-9a-fA-F]{2}:[0-9a-fA-F]{2}:[0-9a-fA-F]{2}:[0-9a-fA-F]{2}/", $output[0], $macs)) {
		$mac = $macs[0];
		return $mac;
    } else {
	    return NULL;
	    //die("Mac no encontrada.");
    }
}

function iptables_clean() {
    exec_fruitywifi(BIN_IPTABLES." -F");            // Delete all rules in 'filter'
    exec_fruitywifi(BIN_IPTABLES." -t nat -F");     // Delete all rules in 'nat'
    exec_fruitywifi(BIN_IPTABLES." -t mangle -F");  // Delete all rules in 'mangle'
    exec_fruitywifi(BIN_IPTABLES." -X");            // Delete a user-defined chain
    exec_fruitywifi(BIN_IPTABLES." -t nat -X");
    exec_fruitywifi(BIN_IPTABLES." -t mangle -X");
}

// COPY & CLEAN DHCP LOG
function dhcp_copycleanlog() {
    if (file_exists(LOGPATH."/dhcp.leases") and 0 < filesize(LOGPATH."/dhcp.leases")) {
        exec_fruitywifi(BIN_CP." ".LOGPATH."/dhcp.leases ".LOGPATH."/dhcp/".gmdate("Ymd-H-i-s").".log");
        exec_fruitywifi(BIN_ECHO." -n > ".LOGPATH."/dhcp.leases");
    }
}



#sed -i 's/interface=.*/interface=wlan0/g' /usr/share/fruitywifi/conf/dnsmasq.conf

if(isset($_GET['service']) and $_GET['service'] == "wireless" and isset($_GET['action'])) {

// +--------------------------------------------------------------+
// | 1: HOSTAPD                                                   |
// +--------------------------------------------------------------+
if($ap_mode == "1") {
    if ($_GET['action'] == "start") {

        exec_fruitywifi(BIN_KILLALL." hostapd");
        exec_fruitywifi(BIN_RM." /var/run/hostapd-phy0/$io_in_iface");
        exec_fruitywifi(BIN_KILLALL." dnsmasq");

        exec_fruitywifi(BIN_IFCONFIG." $io_in_iface up");
        exec_fruitywifi(BIN_IFCONFIG." $io_in_iface up $io_in_ip netmask 255.255.255.0");

        exec_fruitywifi(BIN_ECHO." 'nameserver $io_in_ip\nnameserver 8.8.8.8' > /etc/resolv.conf ");

        // COPY & CLEAN DHCP LOG
        dhcp_copycleanlog();

        exec_fruitywifi(BIN_DNSMASQ." -C /usr/share/fruitywifi/conf/dnsmasq.conf");

        if ($hostapd_secure == 1) {
            //REPLACE SSID
            exec_fruitywifi(BIN_SED." -i 's/^ssid=.*/ssid=".$hostapd_ssid."/g' /usr/share/fruitywifi/conf/hostapd-secure.conf");

            //REPLACE IFACE
            exec_fruitywifi(BIN_SED." -i 's/^interface=.*/interface=".$io_in_iface."/g' /usr/share/fruitywifi/conf/hostapd-secure.conf");

            //REPLACE WPA_PASSPHRASE
            exec_fruitywifi(BIN_SED." -i 's/wpa_passphrase=.*/wpa_passphrase=".$hostapd_wpa_passphrase."/g' /usr/share/fruitywifi/conf/hostapd-secure.conf");

            //EXTRACT MACADDRESS
	        $mac = macaddress($io_in_iface);
            //REPLACE MAC
            exec_fruitywifi(BIN_SED." -i 's/^bssid=.*/bssid=".$mac."/g' /usr/share/fruitywifi/conf/hostapd-secure.conf");

			//Run hostapd
            exec_fruitywifi("/usr/sbin/hostapd -P /var/run/hostapd-phy0 -B /usr/share/fruitywifi/conf/hostapd-secure.conf");
        } else {
            //REPLACE SSID
            exec_fruitywifi(BIN_SED." -i 's/^ssid=.*/ssid=".$hostapd_ssid."/g' /usr/share/fruitywifi/conf/hostapd.conf");

            //REPLACE IFACE
            exec_fruitywifi(BIN_SED." -i 's/^interface=.*/interface=".$io_in_iface."/g' /usr/share/fruitywifi/conf/hostapd.conf");

            //REPLACE WPA_PASSPHRASE
            //exec_fruitywifi(BIN_SED." -i 's/wpa_passphrase=.*/wpa_passphrase=".$hostapd_wpa_passphrase."/g' /usr/share/fruitywifi/conf/hostapd.conf");

            //EXTRACT MACADDRESS
	        $mac = macaddress($io_in_iface);

            //REPLACE BSSID
            exec_fruitywifi(BIN_SED." -i 's/^bssid=.*/bssid=".$mac."/g' /usr/share/fruitywifi/conf/hostapd.conf");

			//Run hostapd
            exec_fruitywifi("/usr/sbin/hostapd -P /var/run/hostapd-phy0 -B /usr/share/fruitywifi/conf/hostapd.conf");
        }

        iptables_clean();

        exec_fruitywifi(BIN_ECHO." 1 > /proc/sys/net/ipv4/ip_forward");
        exec_fruitywifi(BIN_IPTABLES." -t nat -A POSTROUTING -o $io_out_iface -j MASQUERADE");


    } elseif($_GET['action'] == "stop") {
        exec_fruitywifi(BIN_KILLALL." hostapd");
        exec_fruitywifi(BIN_RM." /var/run/hostapd-phy0/$io_in_iface");
        exec_fruitywifi(BIN_KILLALL." dnsmasq");

        exec_fruitywifi("ip addr flush dev $io_in_iface");

		exec_fruitywifi(BIN_IFCONFIG." $io_in_iface down");

        iptables_clean();

        // COPY & CLEAN DHCP LOG
        dhcp_copycleanlog();
    }
}

// +--------------------------------------------------------------+
// | 2: AIRCRACK airbase-ng                                       |
// +--------------------------------------------------------------+
if($ap_mode == "2") {
    if ($_GET['action'] == "start") {
        exec_fruitywifi("/usr/sbin/airmon-ng stop mon0");

        exec_fruitywifi(BIN_KILLALL." airbase-ng");

        exec_fruitywifi(BIN_KILLALL." dnsmasq");

        exec_fruitywifi(BIN_ECHO." 'nameserver $io_in_ip\nnameserver 8.8.8.8' > /etc/resolv.conf ");

        exec_fruitywifi("/usr/sbin/airmon-ng start $io_in_iface");

        //$exec = "/usr/sbin/airbase-ng -e $hostapd_ssid -c 2 mon0 > /dev/null &"; //-P (all)
        exec_fruitywifi("/usr/sbin/airbase-ng -e $hostapd_ssid -c 2 mon0 > /tmp/airbase.log &");

        //$exec = BIN_IFCONFIG." at0 up 10.0.0.1 netmask 255.255.255.0";

        sleep(1);

        exec_fruitywifi(BIN_IFCONFIG." at0 up");
        exec_fruitywifi(BIN_IFCONFIG." at0 up $io_in_ip netmask 255.255.255.0");

        // COPY & CLEAN DHCP LOG
        dhcp_copycleanlog();

        exec_fruitywifi(BIN_DNSMASQ." -C /usr/share/fruitywifi/conf/dnsmasq.conf");

        iptables_clean();

        exec_fruitywifi(BIN_ECHO." 1 > /proc/sys/net/ipv4/ip_forward");
        exec_fruitywifi("/sbin/iptables -t nat -A POSTROUTING -o $io_out_iface -j MASQUERADE");

    } elseif($_GET['action'] == "stop") {

        exec_fruitywifi(BIN_KILLALL." airbase-ng");

        exec_fruitywifi(BIN_KILLALL." dnsmasq");

        exec_fruitywifi("/usr/sbin/airmon-ng stop mon0");

        exec_fruitywifi("ip addr flush dev at0");

        exec_fruitywifi(BIN_IFCONFIG." at0 down");

        iptables_clean();

        // COPY & CLEAN DHCP LOG
        dhcp_copycleanlog();
    }
}

// +--------------------------------------------------------------+
// | 3: HOSTAPD MANA                                              |
// +--------------------------------------------------------------+
if($ap_mode == "3") {
    if ($_GET['action'] == "start") {

        //unmanaged-devices=mac:<realmac>;interface-name:wlan2
        if (file_exists("/etc/NetworkManager/NetworkManager.conf")) {
            $mac = macaddress($io_in_iface);
            $ispresent = exec_log("grep '^unmanaged-devices' /etc/NetworkManager/NetworkManager.conf");

            exec_fruitywifi(BIN_SED." -i '/unmanaged/d' /etc/NetworkManager/NetworkManager.conf");
            exec_fruitywifi(BIN_SED." -i '/\[keyfile\]/d' /etc/NetworkManager/NetworkManager.conf");

            if ($ispresent == "") {
                exec_fruitywifi(BIN_ECHO." '[keyfile]' >> /etc/NetworkManager/NetworkManager.conf");

                exec_fruitywifi(BIN_ECHO." 'unmanaged-devices=mac:".$mac.";interface-name:".$io_in_iface."' >> /etc/NetworkManager/NetworkManager.conf");
            }
        }

        exec_fruitywifi(BIN_KILLALL." hostapd");

        exec_fruitywifi(BIN_RM." /var/run/hostapd-phy0/$io_in_iface");

        exec_fruitywifi(BIN_KILLALL." dnsmasq");

        exec_fruitywifi(BIN_IFCONFIG." $io_in_iface up");
        exec_fruitywifi(BIN_IFCONFIG." $io_in_iface up $io_in_ip netmask 255.255.255.0");

        exec_fruitywifi(BIN_ECHO." 'nameserver $io_in_ip\nnameserver 8.8.8.8' > /etc/resolv.conf ");

        // COPY & CLEAN DHCP LOG
        dhcp_copycleanlog();

        exec_fruitywifi(BIN_DNSMASQ." -C /usr/share/fruitywifi/conf/dnsmasq.conf");

        if ($hostapd_secure == 1) {

            if (file_exists(BIN_HOSTAPDMANA)) {
                include "/usr/share/fruitywifi/www/modules/mana/_info_.php";

                //REPLACE SSID
                exec_fruitywifi(BIN_SED." -i 's/^ssid=.*/ssid=".$hostapd_ssid."/g' $mod_path/includes/conf/hostapd-secure.conf");

                //REPLACE IFACE
                exec_fruitywifi(BIN_SED." -i 's/^interface=.*/interface=".$io_in_iface."/g' $mod_path/includes/conf/hostapd-secure.conf");

                //REPLACE WPA_PASSPHRASE
                exec_fruitywifi(BIN_SED." -i 's/wpa_passphrase=.*/wpa_passphrase=".$hostapd_wpa_passphrase."/g' $mod_path/includes/conf/hostapd-secure.conf");

                //EXTRACT MACADDRESS
				$mac = macaddress($io_in_iface);

                //REPLACE MAC
                exec_fruitywifi(BIN_SED." -i 's/^bssid=.*/bssid=".$mac."/g' $mod_path/includes/conf/hostapd-secure.conf");

                exec_fruitywifi(BIN_HOSTAPDMANA." $mod_path/includes/conf/hostapd-secure.conf >> $mod_logs &");
            } else {
                exec_fruitywifi("/usr/sbin/hostapd -P /var/run/hostapd-phy0 -B /usr/share/fruitywifi/conf/hostapd-secure.conf");
            }

        } else {

            if (file_exists(BIN_HOSTAPDMANA)) {
                include "/usr/share/fruitywifi/www/modules/mana/_info_.php";

                //REPLACE SSID
                exec_fruitywifi(BIN_SED." -i 's/^ssid=.*/ssid=".$hostapd_ssid."/g' $mod_path/includes/conf/hostapd.conf");

                //REPLACE IFACE
                exec_fruitywifi(BIN_SED." -i 's/^interface=.*/interface=".$io_in_iface."/g' $mod_path/includes/conf/hostapd.conf");

                //EXTRACT MACADDRESS
				$mac = macaddress($io_in_iface);

                //REPLACE MAC
                exec_fruitywifi(BIN_SED." -i 's/^bssid=.*/bssid=".$mac."/g' $mod_path/includes/conf/hostapd.conf");

                exec_fruitywifi(BIN_HOSTAPDMANA." $mod_path/includes/conf/hostapd.conf >> $mod_logs &");
            } else {
                exec_fruitywifi("/usr/sbin/hostapd -P /var/run/hostapd-phy0 -B /usr/share/fruitywifi/conf/hostapd.conf");
            }

        }

        iptables_clean();

        exec_fruitywifi(BIN_ECHO." 1 > /proc/sys/net/ipv4/ip_forward");
        exec_fruitywifi("/sbin/iptables -t nat -A POSTROUTING -o $io_out_iface -j MASQUERADE");

    } elseif($_GET['action'] == "stop") {

        // REMOVE lines from NetworkManager
        if (file_exists("/etc/NetworkManager/NetworkManager.conf")) {
            exec_fruitywifi(BIN_SED." -i '/unmanaged/d' /etc/NetworkManager/NetworkManager.conf");
            exec_fruitywifi(BIN_SED." -i '/\[keyfile\]/d' /etc/NetworkManager/NetworkManager.conf");
        }

        exec_fruitywifi(BIN_KILLALL." hostapd");

        exec_fruitywifi(BIN_RM." /var/run/hostapd-phy0/$io_in_iface");

        exec_fruitywifi(BIN_KILLALL." dnsmasq");

        exec_fruitywifi("ip addr flush dev $io_in_iface");

        exec_fruitywifi(BIN_IFCONFIG." $io_in_iface down");

        iptables_clean();

        // COPY & CLEAN DHCP LOG
        dhcp_copycleanlog();

    }
}

// +--------------------------------------------------------------+
// | 4: HOSTAPD KARMA                                             |
// +--------------------------------------------------------------+
if($ap_mode == "4") {
    if ($_GET['action'] == "start") {

        //unmanaged-devices=mac:<realmac>;interface-name:wlan2

        if (file_exists("/etc/NetworkManager/NetworkManager.conf")) {
            $mac = macaddress($io_in_iface);
            $ispresent = exec("grep '^unmanaged-devices' /etc/NetworkManager/NetworkManager.conf");

            exec_fruitywifi(BIN_SED." -i '/unmanaged/d' /etc/NetworkManager/NetworkManager.conf");
            exec_fruitywifi(BIN_SED." -i '/[keyfile]/d' /etc/NetworkManager/NetworkManager.conf");

            if ($ispresent == "") {
                exec_fruitywifi(BIN_ECHO." '[keyfile]' >> /etc/NetworkManager/NetworkManager.conf");
                exec_fruitywifi(BIN_ECHO." 'unmanaged-devices=mac:".$mac.";interface-name:".$io_in_iface."' >> /etc/NetworkManager/NetworkManager.conf");
            }
        }

        exec_fruitywifi(BIN_KILLALL." hostapd");

        exec_fruitywifi(BIN_RM." /var/run/hostapd-phy0/$io_in_iface");

        exec_fruitywifi(BIN_KILLALL." dnsmasq");

        exec_fruitywifi(BIN_IFCONFIG." $io_in_iface up");
        exec_fruitywifi(BIN_IFCONFIG." $io_in_iface up $io_in_ip netmask 255.255.255.0");

        exec_fruitywifi(BIN_ECHO." 'nameserver $io_in_ip\nnameserver 8.8.8.8' > /etc/resolv.conf ");

        // COPY & CLEAN DHCP LOG
        dhcp_copycleanlog();

        exec_fruitywifi(BIN_DNSMASQ." -C /usr/share/fruitywifi/conf/dnsmasq.conf");

        if ($hostapd_secure == 1) {

            if (file_exists(BIN_HOSTAPDKARMA)) {
                include "/usr/share/fruitywifi/www/modules/karma/_info_.php";

                //REPLACE SSID
                exec_fruitywifi(BIN_SED." -i 's/^ssid=.*/ssid=".$hostapd_ssid."/g' $mod_path/includes/conf/hostapd-secure.conf");

                //REPLACE IFACE
                exec_fruitywifi(BIN_SED." -i 's/^interface=.*/interface=".$io_in_iface."/g' $mod_path/includes/conf/hostapd-secure.conf");

                //REPLACE WPA_PASSPHRASE
                exec_fruitywifi(BIN_SED." -i 's/wpa_passphrase=.*/wpa_passphrase=".$hostapd_wpa_passphrase."/g' $mod_path/includes/conf/hostapd-secure.conf");

                //EXTRACT MACADDRESS
                $mac = macaddress($io_in_iface);

                //REPLACE MAC
                exec_fruitywifi(BIN_SED." -i 's/^bssid=.*/bssid=".$mac."/g' $mod_path/includes/conf/hostapd-secure.conf");

                exec_fruitywifi(BIN_HOSTAPDKARMA." $mod_path/includes/conf/hostapd-secure.conf >> $mod_logs &");
            } else {
                exec_fruitywifi("/usr/sbin/hostapd -P /var/run/hostapd-phy0 -B /usr/share/fruitywifi/conf/hostapd-secure.conf");
            }

        } else {

            if (file_exists(BIN_HOSTAPDKARMA)) {
                include "/usr/share/fruitywifi/www/modules/karma/_info_.php";

                //REPLACE SSID
                exec_fruitywifi(BIN_SED." -i 's/^ssid=.*/ssid=".$hostapd_ssid."/g' $mod_path/includes/conf/hostapd.conf");

                //REPLACE IFACE
                exec_fruitywifi(BIN_SED." -i 's/^interface=.*/interface=".$io_in_iface."/g' $mod_path/includes/conf/hostapd.conf");

                //EXTRACT MACADDRESS
                $mac = macaddress($io_in_iface);

                //REPLACE MAC
                exec_fruitywifi(BIN_SED." -i 's/^bssid=.*/bssid=".$mac."/g' $mod_path/includes/conf/hostapd.conf");

                exec_fruitywifi(BIN_HOSTAPDKARMA." $mod_path/includes/conf/hostapd.conf >> $mod_logs &");
            } else {
                exec_fruitywifi("/usr/sbin/hostapd -P /var/run/hostapd-phy0 -B /usr/share/fruitywifi/conf/hostapd.conf");
            }

        }

        iptables_clean();

        exec_fruitywifi(BIN_ECHO." 1 > /proc/sys/net/ipv4/ip_forward");
        exec_fruitywifi("/sbin/iptables -t nat -A POSTROUTING -o $io_out_iface -j MASQUERADE");

    } elseif($_GET['action'] == "stop") {

        // REMOVE lines from NetworkManager
        if (file_exists("/etc/NetworkManager/NetworkManager.conf")) {
            exec_fruitywifi(BIN_SED." -i '/unmanaged/d' /etc/NetworkManager/NetworkManager.conf");
            exec_fruitywifi(BIN_SED." -i '/[keyfile]/d' /etc/NetworkManager/NetworkManager.conf");
        }

        exec_fruitywifi(BIN_KILLALL." hostapd");

        exec_fruitywifi(BIN_RM." /var/run/hostapd-phy0/$io_in_iface");

        exec_fruitywifi(BIN_KILLALL." dnsmasq");

        exec_fruitywifi("ip addr flush dev $io_in_iface");

        exec_fruitywifi(BIN_IFCONFIG." $io_in_iface down");

        iptables_clean();

        // COPY & CLEAN DHCP LOG
        dhcp_copycleanlog();

    }
}

// +--------------------------------------------------------------+
// | Kick                                                         |
// +--------------------------------------------------------------+
if($_GET['action'] == "kick" and isset($_GET['station'])) {
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

// +--------------------------------------------------------------+
// | Ban & Kick                                                   |
// +--------------------------------------------------------------+
if($_GET['action'] == "ban" and isset($_GET['station'])) {
	$s = $_GET['station'];
	if(preg_match("/[0-9a-fA-F]{2}[0-9a-fA-F]{2}[0-9a-fA-F]{2}[0-9a-fA-F]{2}[0-9a-fA-F]{2}[0-9a-fA-F]{2}/", $s)) {
		$station = $s[0].$s[1].':'.$s[2].$s[3].':'.$s[4].$s[5].':'.$s[6].$s[7].':'.$s[8].$s[9].':'.$s[10].$s[11];
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

} //if $_GET['service'] == "wireless"

header('Location: ../action.php');

?>