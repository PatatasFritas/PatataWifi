<?
include_once dirname(__FILE__)."/../../config/config.php";

$mod_name="dnsspoof";
$mod_version="1.7x";
$mod_path="/usr/share/fruitywifi/www/modules/$mod_name";
$mod_logs=LOGPATH."/$mod_name.log";
$mod_logs_history=LOGPATH."/$mod_name/";
$mod_panel="show";
$mod_type="";
$mod_alias="DNSspoof";
$mod_installed="0";

$mod_isup="ps auxww | grep dnsspoof | grep -v -e grep";

@define('BIN_DNSSPOOF', "/usr/sbin/dnsspoof");

?>