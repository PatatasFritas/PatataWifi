<?
include_once dirname(__FILE__)."/../../config/config.php";

$mod_name="nmap";
$mod_version="1.4x";
$mod_path="/usr/share/fruitywifi/www/modules/$mod_name";
$mod_logs="$log_path/$mod_name.log";
$mod_logs_history="$mod_path/includes/logs/";
$mod_panel="show";
$mod_type="";
$mod_alias="Nmap";
$mod_installed="0";

$mod_isup="";

@define('BIN_NMAP', "/usr/bin/nmap");

//$mod_logs=LOGPATH."/$mod_name.log";
//$mod_logs_history=LOGPATH."/$mod_name/";

?>