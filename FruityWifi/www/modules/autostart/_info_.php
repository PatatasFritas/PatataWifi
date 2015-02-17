<?
include_once dirname(__FILE__)."/../../config/config.php";

$mod_name="autostart";
$mod_version="1.2x";
$mod_path="/usr/share/fruitywifi/www/modules/$mod_name";
$mod_logs="$log_path/$mod_name.log";
$mod_logs_history="$mod_path/includes/logs/";
$mod_logs_panel="disabled";
$mod_panel="show";
$mod_type="";
$mod_alias="Autostart";
$mod_installed="0";

$mod_isup="grep 'FruityWifi-autostart.php' /etc/rc.local";

@define('BIN_ECHO', "/bin/echo");
@define('BIN_CP', "/bin/cp");
@define('BIN_KILLALL', "/usr/bin/killall");
@define('BIN_MKDIR', "/bin/mkdir");
@define('BIN_CHOWN', "/bin/chown");
@define('BIN_CHMOD', "/bin/chmod");
?>