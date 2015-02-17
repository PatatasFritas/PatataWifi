<?
include_once dirname(__FILE__)."/../../config/config.php";

$mod_name="mdk3";
$mod_version="1.7x";
$mod_path="/usr/share/fruitywifi/www/modules/$mod_name";
$mod_logs=LOGPATH."/$mod_name.log";
$mod_logs_history=LOGPATH."/$mod_name/";
$mod_panel="show";
$mod_type="";
$mod_alias="mdk3";
$mod_installed="0";

$mod_isup="ps auxww | grep mdk3 | grep -v -e grep";

@define('BIN_MDK3', "/usr/bin/mdk3");

@define('BIN_ECHO', "/bin/echo");
@define('BIN_CP', "/bin/cp");
@define('BIN_KILLALL', "/usr/bin/killall");
@define('BIN_MKDIR', "/bin/mkdir");
@define('BIN_CHOWN', "/bin/chown");
@define('BIN_CHMOD', "/bin/chmod");

?>