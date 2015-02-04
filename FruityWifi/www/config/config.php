<?
$version="v2.1.2";

define('FWPATH', "/usr/share/fruitywifi/"); // Path of fruitywifi
define('WWWPATH', "/usr/share/fruitywifi/www/");
define('LOGPATH', "/usr/share/fruitywifi/logs/");
$log_path = FWPATH."logs/";

define('WEBPATH', "/");


define('PASSWORDSALT', "FruityWifi");

$regex=1; // 1 (on) | 0 (off) >> web interface input validation.
$regex_extra=" _-.[]*"; // extra characters allowed (input validation).

// move this to wireless page
$hostapd_modes = array(1 => "Hostapd", 3 => "Hostapd-Mana", 4 => "Hostapd-Karma", 2 => "Airmon-ng");

// Config
$io_in_iface_extra="-";
$iface_supplicant="wlan0";
$supplicant_ssid="";
$supplicant_psk="";
$hostapd_ssid="FruityWifi";
$hostapd_secure="0";
$hostapd_wpa_passphrase="FruityWifi";
$url_rewrite_program="pasarela_xss.js";
$dnsmasq_domain="WIFI";
//------
$io_mode="1";
$io_in_iface="wlan0";
$io_in_set="1";
$io_in_ip="10.0.0.1";
$io_in_mask="255.255.255.0";
$io_in_gw="";

$io_out_iface="eth0";
$io_out_set="0";
$io_out_ip="192.168.0.1";
$io_out_mask="255.255.255.0";
$io_out_gw="192.168.0.1";

$ap_mode="1";
$io_action="wlan0";
//------

# EXEC
$bin_sudo = "/usr/bin/sudo";
$bin_ifconfig = "/sbin/ifconfig";
$bin_iwlist = "/sbin/iwlist";
$bin_sh = "/bin/sh";
$bin_echo = "/bin/echo";
$bin_grep = "/usr/bin/ngrep";
$bin_killall = "/usr/bin/killall";
$bin_cp = "/bin/cp";
$bin_chmod = "/bin/chmod";
$bin_sed = "/bin/sed";
$bin_rm = "/bin/rm";
$bin_route = "/sbin/route";
$bin_perl = "/usr/bin/perl";
$bin_nmcli = "/usr/share/fruitywifi/www/modules/nmcli/includes/NetworkManager/cli/src/nmcli";
$bin_ln = "/bin/ln";
$bin_awk = "/usr/bin/awk";
$bin_grep = "/bin/grep";
$bin_sed = "/bin/sed";
$bin_iptables = "/sbin/iptables";
$bin_cat = "/bin/cat";
$bin_dos2unix = "/usr/bin/dos2unix";
$bin_mv = "/bin/mv";
$bin_touch = "/bin/touch";
$bin_python = "/usr/bin/python";

?>