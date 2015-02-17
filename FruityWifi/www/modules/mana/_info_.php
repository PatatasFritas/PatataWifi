<?
include_once dirname(__FILE__)."/../../config/config.php";

$mod_name="mana";
$mod_version="1.1x";
$mod_path="/usr/share/fruitywifi/www/modules/$mod_name";
$mod_logs=LOGPATH."/$mod_name.log";
$mod_logs_history=LOGPATH."/$mod_name/";
$mod_panel="show";
$mod_type="service";
$mod_alias="Mana";
$mod_installed="0";

$mod_isup="/usr/share/fruitywifi/www/modules/$mod_name/includes/hostapd_cli -p /var/run/hostapd karma_get_state | tail -1 | grep 'KARMA EN'";

//Hide mana
if($ap_mode!=3 and $_SERVER['PHP_SELF']!="/page_modules.php" and $mod_installed!=0) $mod_panel="";

@define('BIN_HOSTAPDMANA', "/usr/share/fruitywifi/www/modules/mana/includes/hostapd");
@define('BIN_HOSTAPDMANA_CLI', "/usr/share/fruitywifi/www/modules/mana/includes/hostapd_cli");

@define('BIN_ECHO', "/bin/echo");
@define('BIN_CP', "/bin/cp");
@define('BIN_ROUTE', "/sbin/route");
@define('BIN_KILLALL', "/usr/bin/killall");
@define('BIN_MKDIR', "/bin/mkdir");
@define('BIN_CHOWN', "/bin/chown");
@define('BIN_CHMOD', "/bin/chmod");

?>