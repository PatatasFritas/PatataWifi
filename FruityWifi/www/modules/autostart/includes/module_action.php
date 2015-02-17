<?
include_once dirname(__FILE__)."/../../../config/config.php";
include_once dirname(__FILE__)."/../_info_.php";

require_once WWWPATH."/includes/login_check.php";
require_once WWWPATH."/includes/filter_getpost.php";
include_once WWWPATH."/includes/functions.php";

include_once "options_config.php";

if(isset($_GET['service']) and $_GET['service'] != "" and isset($_GET['action'])) {

    if ($_GET['action'] == "start") {

        $srv_https = $_SERVER['HTTPS'];
        $srv_port = $_SERVER['SERVER_PORT'];
        $srv_dir = dirname(__FILE__);
        $srv_dir = str_replace("/","\\/",$srv_dir);
        $srv_php_self = $_SERVER['PHP_SELF'];
        $web_path = substr($srv_php_self, 0, strpos($srv_php_self, "/modules/"));
        $web_path = str_replace("/","\\/",$web_path);
        $logs = str_replace("/","\\/",$mod_logs);

        exec_fruitywifi(BIN_SED." -i 's/^\\\$srv_port =.*/\\\$srv_port = \\\"$srv_port\\\";/g' FruityWifi-autostart.php");

        exec_fruitywifi(BIN_SED." -i 's/^\\\$srv_https =.*/\\\$srv_https = \\\"$srv_https\\\";/g' FruityWifi-autostart.php");

        exec_fruitywifi(BIN_SED." -i 's/^\\\$srv_dir =.*/\\\$srv_dir = \\\"$srv_dir\\\";/g' FruityWifi-autostart.php");

        exec_fruitywifi(BIN_SED." -i 's/^\\\$web_path =.*/\\\$web_path = \\\"$web_path\\\";/g' FruityWifi-autostart.php");

        exec_fruitywifi(BIN_SED." -i 's/^\\\$logs =.*/\\\$logs = \\\"$logs\\\";/g' FruityWifi-autostart.php");

        // INCLUDE rc.local
        $isautostart = exec("grep 'FruityWifi-autostart.php' /etc/rc.local");
        if ($isautostart  == "") {

			// Check if 'exit 0' exists in rc.local
			$isexit = exec("grep '^exit 0' /etc/rc.local");
			if ($isexit  == "") {
                exec_fruitywifi("echo 'exit 0' >> /etc/rc.local");
			}

			// Insert Autostart in rc.local
            exec_fruitywifi("sed -i '/FruityWifi-autostart.php/d' /etc/rc.local");

            exec_fruitywifi("sed -i 's/^exit 0/php $srv_dir\/FruityWifi-autostart.php\\nexit 0/g' /etc/rc.local");

        }

         // COPY LOG
        if (file_exists($mod_logs) and 0 < filesize($mod_logs)) {
            exec_fruitywifi(BIN_CP." $mod_logs $mod_logs_history/".gmdate("Ymd-H-i-s").".log");
            exec_fruitywifi(BIN_ECHO." -n > $mod_logs");
        }


    } elseif($_GET['action'] == "stop") {

        // REMOVE from rc.local
        exec_fruitywifi("sed -i '/FruityWifi-autostart.php/d' /etc/rc.local");

         // COPY LOG
        if (file_exists($mod_logs) and 0 < filesize($mod_logs)) {
            exec_fruitywifi(BIN_CP." $mod_logs $mod_logs_history/".gmdate("Ymd-H-i-s").".log");
            exec_fruitywifi(BIN_ECHO." -n > $mod_logs");
        }

    }

}

if (isset($_GET['install']) and $_GET['install'] == "install_$mod_name") {

    if(!is_dir($mod_logs_history)) {
        exec_fruitywifi(BIN_MKDIR." -p $mod_logs_history");
        exec_fruitywifi(BIN_CHOWN." fruitywifi:fruitywifi $mod_logs_history");
    }

    exec_fruitywifi(BIN_CHMOD." 755 install.sh");
    exec_fruitywifi("./install.sh > ".LOGPATH."/install.txt &");

    header('Location: '.WEBPATH.'/modules/install.php?module='.$mod_name);
    exit;
}

if (isset($_GET['page']) and $_GET['page'] == "status") {
    header('Location: '.WEBPATH.'/action.php');
} else {
    header('Location: '.WEBPATH.'/modules/action.php?page='.$mod_name);
}

?>