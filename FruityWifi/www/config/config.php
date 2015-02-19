<?
$version="v2.1.2x";

define('FWPATH', "/usr/share/fruitywifi/"); // Path of fruitywifi
define('WWWPATH', "/usr/share/fruitywifi/www/");
define('LOGPATH', "/usr/share/fruitywifi/logs");
$log_path = LOGPATH;

define('WEBPATH', "");
define('PASSWORDSALT', "FruityWifi");

$regex=1; // 1 (on) | 0 (off) >> web interface input validation.
$regex_extra=" _-.[]*,:"; // extra characters allowed (input validation).

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
//@define('BIN_', "/");
@define('BIN_SUDO', "/usr/bin/sudo");
@define('BIN_ECHO', "/bin/echo");
@define('BIN_CP', "/bin/cp");
@define('BIN_KILLALL', "/usr/bin/killall");
@define('BIN_MKDIR', "/bin/mkdir");
@define('BIN_CHOWN', "/bin/chown");
@define('BIN_CHMOD', "/bin/chmod");
@define('BIN_IPTABLES', "/sbin/iptables");
@define('BIN_PYTHON', "/usr/bin/python");
@define('BIN_DOS2UNIX', "/usr/bin/dos2unix");
@define('BIN_TOUCH', "/bin/touch");
@define('BIN_SED', "/bin/sed");
@define('BIN_RM', "/bin/rm");
@define('BIN_MV', "/bin/mv");
@define('BIN_SED', "/bin/sed");
@define('BIN_GREP', "/bin/grep");
@define('BIN_ROUTE', "/sbin/route");
@define('BIN_IFCONFIG', "/sbin/ifconfig");
@define('BIN_IWLIST', "/sbin/iwlist");
@define('BIN_AWK', "/usr/bin/awk");

@define('BIN_CAT', "/bin/cat");


@define('BIN_DNSMASQ', "/usr/sbin/dnsmasq");

@define('BIN_HOSTAPDMANA', "/usr/share/fruitywifi/www/modules/mana/includes/hostapd");
@define('BIN_HOSTAPDMANA_CLI', "/usr/share/fruitywifi/www/modules/mana/includes/hostapd_cli");
@define('BIN_HOSTAPDKARMA', "/usr/share/fruitywifi/www/modules/karma/includes/hostapd");
@define('BIN_HOSTAPDKARMA_CLI', "/usr/share/fruitywifi/www/modules/karma/includes/hostapd_cli");

?>