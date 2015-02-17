<?
include_once dirname(__FILE__)."/../../config/config.php";

$mod_name="urlsnarf";
$mod_version="1.6x";
$mod_path="/usr/share/fruitywifi/www/modules/$mod_name";
$mod_logs=LOGPATH."/$mod_name.log";
$mod_logs_history=LOGPATH."/$mod_name/";
$mod_panel="show";
$mod_type="";
$mod_alias="URLSnarf";
$mod_installed="0";

$mod_isup="ps auxww | grep urlsnarf | grep -v -e grep";

@define('BIN_URLSNARF', "/usr/sbin/urlsnarf");
@define('BIN_ECHO', "/bin/echo");
@define('BIN_CP', "/bin/cp");
@define('BIN_KILLALL', "/usr/bin/killall");
@define('BIN_MKDIR', "/bin/mkdir");
@define('BIN_CHOWN', "/bin/chown");
@define('BIN_CHMOD', "/bin/chmod");
?>