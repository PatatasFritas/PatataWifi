<?
include_once dirname(__FILE__)."/../../config/config.php";

$mod_name="karma";
$mod_version="1.4x";
$mod_path="/usr/share/fruitywifi/www/modules/$mod_name";
$mod_logs="$log_path/$mod_name.log";
$mod_logs_history="$mod_path/includes/logs/";
$mod_logs_panel="disabled";
$mod_panel="show";
$mod_type="service";
$mod_alias="Karma";
$mod_installed="0";

$mod_isup=BIN_SUDO." $mod_path/includes/hostapd_cli -p /var/run/hostapd-phy0 karma_get_state | grep 'ENABLE'";

//Hide karma
if($ap_mode!=4 and $_SERVER['PHP_SELF']!="/page_modules.php" and $mod_installed!=0) $mod_panel="";
//Hide start when wireless is down
if($ap_mode==4 and isset($iswlanup) and $iswlanup==false) $mod_isup="";


@define('BIN_HOSTAPDKARMA', "/usr/share/fruitywifi/www/modules/karma/includes/hostapd");
@define('BIN_HOSTAPDKARMA_CLI', "/usr/share/fruitywifi/www/modules/karma/includes/hostapd_cli");

?>