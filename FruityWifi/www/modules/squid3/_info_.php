<?
include_once dirname(__FILE__)."/../../config/config.php";

$mod_name="squid3";
$mod_version="1.3x";
$mod_path="/usr/share/fruitywifi/www/modules/$mod_name";
$mod_logs=LOGPATH."/$mod_name.log";
$mod_logs_history=LOGPATH."/$mod_name/";
$mod_panel="show";
$mod_type="";
$mod_alias="Squid3";
$mod_installed="0";

$mod_isup="ps auxww | grep squid3 | grep -v -e 'grep squid3'";

$url_rewrite_program="pasarela_xss.js";

@define('BIN_SQUID3', "/usr/sbin/squid3");

?>