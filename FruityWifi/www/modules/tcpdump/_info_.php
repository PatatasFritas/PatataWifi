<?
include_once dirname(__FILE__)."/../../config/config.php";

$mod_name="tcpdump";
$mod_version="1.1x";
$mod_path="/usr/share/fruitywifi/www/modules/$mod_name";
$mod_logs=LOGPATH."/$mod_name.log";
$mod_logs_history=LOGPATH."/$mod_name/";
$mod_panel="show";
$mod_type="";
$mod_alias="Tcpdump";
$mod_installed="0";

$mod_isup="ps auxww | grep $mod_name | grep -v -e grep";

@define('BIN_TCPDUMP', "/usr/sbin/tcpdump");
@define('BIN_ECHO', "/bin/echo");
@define('BIN_CP', "/bin/cp");
@define('BIN_KILLALL', "/usr/bin/killall");
@define('BIN_MKDIR', "/bin/mkdir");
@define('BIN_CHOWN', "/bin/chown");
@define('BIN_CHMOD', "/bin/chmod");

?>