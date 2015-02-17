<?
include_once dirname(__FILE__)."/../../../config/config.php";
include_once dirname(__FILE__)."/../_info_.php";

require_once WWWPATH."/includes/login_check.php";
require_once WWWPATH."/includes/filter_getpost.php";
include_once WWWPATH."/includes/functions.php";

include "options_config.php";

if(isset($_GET['service']) and $_GET['service'] == "tcpdump" and isset($_GET['action'])) {
    if ($_GET['action'] == "start") {

        // COPY LOG
        if (file_exists($mod_logs) and 0 < filesize($mod_logs)) {
            exec_fruitywifi(BIN_CP." $mod_logs $mod_logs_history/".gmdate("Ymd-H-i-s").".log");
            exec_fruitywifi(BIN_ECHO." -n > $mod_logs");
        }

        // ADD selected options
        $options = "";
        //$tmp = array_keys($mode_options);
        //for ($i=0; $i< count($tmp); $i++) {
        foreach ($mode_options as $key=>$option) {
             if ($option[0] == "1") {
				if ($key == "F") {
					$options .= " -F $mod_path/includes/templates/".$option[2];
				} else {
					$options .= " -" . $key . " " . $option[2];
				}

            }
        }

        exec_fruitywifi(BIN_TCPDUMP." -i $io_action $options '$expression' >> $mod_logs &");
        exec_fruitywifi(BIN_TCPDUMP." -i $io_action $options -s 0 -w dump/".gmdate("Ymd-H-i-s").".cap '$expression' >/dev/null &");

    } elseif($_GET['action'] == "stop") {
        // STOP tcpdump
        exec_fruitywifi(BIN_KILLALL." tcpdump");

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