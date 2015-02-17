<?
include_once dirname(__FILE__)."/../../../config/config.php";
include_once dirname(__FILE__)."/../_info_.php";

require_once WWWPATH."/includes/login_check.php";
require_once WWWPATH."/includes/filter_getpost.php";
include_once WWWPATH."/includes/functions.php";

include "options_config.php";

if(isset($_GET['service']) and $_GET['service'] == "ngrep" and isset($_GET['action'])) {
    if ($_GET['action'] == "start") {
        // COPY LOG
        if (file_exists($mod_logs) and 0 < filesize($mod_logs)) {
            exec_fruitywifi(BIN_CP." $mod_logs $mod_logs_history/".gmdate("Ymd-H-i-s").".log");
            exec_fruitywifi(BIN_ECHO." -n > $mod_logs");
        }

        $options = "";
        // ADD selected options
        $tmp = array_keys($mode_ngrep);
        for ($i=0; $i< count($tmp); $i++) {
             if ($mode_ngrep[$tmp[$i]][0] == "1") {
                $options .= " -" . $tmp[$i] . " " . $mode_ngrep[$tmp[$i]][2];
            }
        }

        //$exec = "/usr/bin/ngrep -q -d wlan0 -W byline -t $mode $options >> $mod_logs &";
        //$exec = "/usr/bin/ngrep -q -d wlan0 -W byline -t 'Cookie' 'tcp and port 80' >> $mod_logs &";

        $filename = "$mod_path/includes/templates/".$ss_mode;
        $data = open_file($filename);

        exec_fruitywifi(BIN_NGREP." -q -d $io_action -W byline $options -t $data >> $mod_logs &");

    } else if($_GET['action'] == "stop") {
        // STOP MODULE
        exec_fruitywifi(BIN_KILLALL." ngrep");

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