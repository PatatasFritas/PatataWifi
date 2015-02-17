<?
include_once dirname(__FILE__)."/../../config/config.php";

$mod_name="phishing";
$mod_version="1.1x";
$mod_path="/usr/share/fruitywifi/www/modules/$mod_name";
$mod_logs=LOGPATH."/$mod_name.log";
$mod_logs_history=LOGPATH."/$mod_name/";
$mod_panel="show";
$mod_type="";
$mod_alias="Phishing";
$mod_installed="1";

$mod_isup="grep 'FruityWifi-Phishing' /var/www/index.php";

@define('BIN_CONNTRACK', "/usr/sbin/conntrack");

# FILE
$file_users = "$mod_path/includes/www.site/data.txt";

?>