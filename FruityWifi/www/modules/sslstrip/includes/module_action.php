<?
include_once dirname(__FILE__)."/../../../config/config.php";
include_once dirname(__FILE__)."/../_info_.php";

require_once WWWPATH."/includes/login_check.php";
require_once WWWPATH."/includes/filter_getpost.php";
include_once WWWPATH."/includes/functions.php";

if(isset($_GET['service']) and $_GET['service'] == "sslstrip" and isset($_GET['action'])) {
    if ($_GET['action'] == "start") {

        // COPY LOG
        if (file_exists($mod_logs) and 0 < filesize($mod_logs)) {
            exec_fruitywifi(BIN_CP." $mod_logs $mod_logs_history/sslstrip-".gmdate("Ymd-H-i-s").".log");
            exec_fruitywifi(BIN_ECHO." -n > $mod_logs");
        }

		exec_fruitywifi(BIN_IPTABLES." -t nat -A PREROUTING -p tcp --destination-port 80 -j REDIRECT --to-port 10000");
        //$exec = "/usr/bin/sslstrip -a -s -l 10000 -w ../logs/sslstrip.log > /dev/null 2 &";

$options = "";
if($mod_sslstrip_inject == "1") {
    $options .= " -i $mod_path/includes/inject.txt ";
}
if($mod_sslstrip_tamperer == "1") {
    $options .= " -t $mod_path/includes/app_cache_poison/config.ini ";
}



        if ($mod_sslstrip_inject == "1" and $mod_sslstrip_tamperer == "0") {
            exec_fruitywifi(BIN_SSLSTRIP." -a -s -l 10000 -w $mod_logs -i $mod_path/includes/inject.txt > /dev/null 2 &");
        } else if ($mod_sslstrip_inject == "0" and $mod_sslstrip_tamperer == "1") {
            exec_fruitywifi(BIN_SSLSTRIP." -a -s -l 10000 -w $mod_logs -t $mod_path/includes/app_cache_poison/config.ini > /dev/null 2 &");
        } else if ($mod_sslstrip_inject == "1" and $mod_sslstrip_tamperer == "1") {
            exec_fruitywifi(BIN_SSLSTRIP." -a -s -l 10000 -w $mod_logs -t $mod_path/includes/app_cache_poison/config.ini -i $mod_path/includes/inject.txt > /dev/null 2 &");
        } else {
            exec_fruitywifi(BIN_SSLSTRIP." -a -s -l 10000 -w $mod_logs > /dev/null 2 &");
        }
        //$exec = "/usr/bin/sslstrip-tamper -a -s -l 10000 -w ../logs/sslstrip.log -t /usr/share/FruityWifi/www/modules/sslstrip/includes/app_cache_poison/config.ini > /dev/null 2 &";

        //$exec = "/usr/bin/sslstrip -a -s -l 10000 -w ../logs/sslstrip/sslstrip-".gmdate("Ymd-H-i-s").".log > /dev/null 2 &";

    } elseif($_GET['action'] == "stop") {
		exec_fruitywifi(BIN_IPTABLES." -t nat -D PREROUTING -p tcp --destination-port 80 -j REDIRECT --to-port 10000");
		exec_fruitywifi(BIN_KILLALL." sslstrip");

		// COPY LOG
        if (file_exists($mod_logs) and 0 < filesize($mod_logs)) {
            exec_fruitywifi(BIN_CP." $mod_logs $mod_logs_history/sslstrip-".gmdate("Ymd-H-i-s").".log");
            exec_fruitywifi(BIN_ECHO." -n > $mod_logs");
        }

    }
}



if (isset($_GET['install']) and $_GET['install'] == "install_sslstrip") {

    if(!is_dir($mod_logs_history)) {
        exec_fruitywifi(BIN_MKDIR." -p $mod_logs_history");
        exec_fruitywifi(BIN_CHOWN." fruitywifi:fruitywifi $mod_logs_history");
    }

    exec_fruitywifi(BIN_CHMOD." 755 install.sh");
    exec_fruitywifi("./install.sh > ".LOGPATH."/install.txt &");

    header('Location: '.WEBPATH.'/modules/install.php?module=sslstrip');
    exit;
}

if (isset($_GET['page']) and $_GET['page'] == "status") {
    header('Location: '.WEBPATH.'/action.php');
} else {
    header('Location: '.WEBPATH.'/modules/action.php?page=sslstrip');
}

?>