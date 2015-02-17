<?
include_once dirname(__FILE__)."/../../config/config.php";

$mod_name="autossh";
$mod_version="1.1x";
$mod_path="/usr/share/fruitywifi/www/modules/$mod_name";
$mod_logs="$log_path/$mod_name.log";
$mod_logs_history="$mod_path/includes/logs/";
$mod_logs_panel="disabled";
$mod_panel="show";
$mod_type="";
$mod_alias="AutoSSH";
$mod_installed="0";

$mod_isup="ps auxww | grep $mod_name | grep -v -e 'grep $mod_name'";

@define('BIN_AUTOSSH', "/usr/bin/autossh");
@define('BIN_SSH_KEYGEN', "/usr/bin/ssh-keygen");

?>