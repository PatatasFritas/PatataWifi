<?
include_once dirname(__FILE__)."/../../config/config.php";

$mod_name="sslsplit";
$mod_version="1.0";
$mod_path="/usr/share/fruitywifi/www/modules/$mod_name";
$mod_logs=LOGPATH."/$mod_name.log";
$mod_logs_history=LOGPATH."/$mod_name/";
$mod_panel="show";
$mod_type="";
$mod_alias="SSL-Split";
$mod_installed="0";

$mod_isup="ps auxww | grep sslsplit | grep -v -e grep";

@define('BIN_SSLSPLIT', "$mod_path/includes/sslsplit");

?>