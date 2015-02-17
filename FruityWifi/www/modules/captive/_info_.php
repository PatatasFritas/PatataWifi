<?
include_once dirname(__FILE__)."/../../config/config.php";

$mod_name="captive";
$mod_version="1.4x";
$mod_path="/usr/share/fruitywifi/www/modules/$mod_name";
$mod_logs=LOGPATH."/$mod_name.log";
$mod_logs_history=LOGPATH."/$mod_name/";
$mod_panel="show";
$mod_type="";
$mod_alias="Captive";
$mod_installed="0";

$mod_isup="/sbin/iptables -t mangle -L|grep -iEe 'internet.+anywhere'";

# EXEC
@define('BIN_CONNTRACK', "/usr/sbin/conntrack");


# FILE
$captive_dir = "/var/www/site";
$captive_site = "/var/www/site/captive";
$file_users = "/var/www/site/captive/admin/users";
?>
