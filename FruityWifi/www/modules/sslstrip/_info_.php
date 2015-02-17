<?
include_once dirname(__FILE__)."/../../config/config.php";

$mod_name="sslstrip";
$mod_version="1.7x";
$mod_path="/usr/share/fruitywifi/www/modules/$mod_name";
$mod_logs=LOGPATH."/$mod_name.log";
$mod_logs_history=LOGPATH."/$mod_name/";
$mod_panel="show";
$mod_type="";
$mod_alias="SSLstrip";
$mod_installed="0";

$mod_isup="ps auxww | grep sslstrip | grep -v -e grep";

@define('BIN_SSLSTRIP', "$mod_path/includes/sslstrip");

@define('BIN_PYTHON', "/usr/bin/python");
@define('BIN_IPTABLES', "/sbin/iptables");
@define('BIN_ECHO', "/bin/echo");
@define('BIN_CP', "/bin/cp");
@define('BIN_KILLALL', "/usr/bin/killall");
@define('BIN_MKDIR', "/bin/mkdir");
@define('BIN_CHOWN', "/bin/chown");
@define('BIN_CHMOD', "/bin/chmod");
@define('BIN_PYTHON', "/usr/bin/python");
@define('BIN_DOS2UNIX', "/usr/bin/dos2unix");
@define('BIN_SED', "/bin/sed");

#CONFIG
$mod_sslstrip_inject=0;
$mod_sslstrip_tamperer=0;
$mod_sslstrip_filter="LogEx.py";

?>