<?
include_once dirname(__FILE__)."/../../config/config.php";

$mod_name="whatsapp";
$mod_version="1.3x";
$mod_path="/usr/share/fruitywifi/www/modules/$mod_name";
$mod_logs=LOGPATH."/$mod_name.log";
$mod_logs_history=LOGPATH."/$mod_name/";
$mod_panel="show";
$mod_type="";
$mod_alias="WhatsApp";
$mod_installed="0";

$mod_isup="ps auxww | grep whatsapp_discover | grep -v -e 'grep'";

@define('BIN_ECHO', "/bin/echo");
@define('BIN_CP', "/bin/cp");
@define('BIN_ROUTE', "/sbin/route");
@define('BIN_KILLALL', "/usr/bin/killall");
@define('BIN_MKDIR', "/bin/mkdir");
@define('BIN_CHOWN', "/bin/chown");
@define('BIN_CHMOD', "/bin/chmod");
?>