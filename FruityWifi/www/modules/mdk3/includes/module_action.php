<?
include_once dirname(__FILE__)."/../../../config/config.php";
include_once dirname(__FILE__)."/../_info_.php";

require_once WWWPATH."/includes/login_check.php";
require_once WWWPATH."/includes/filter_getpost.php";
include_once WWWPATH."/includes/functions.php";

include "options_config.php";

if(isset($_GET['service']) and $_GET['service'] == "mdk3" and isset($_GET['action'])) {

    //$exec = "/bin/sed -i 's/ss_mode.*/ss_mode = \\\"".$service."\\\";/g' options_config.php";
    //exec("/usr/share/FruityWifi/bin/danger \"" . $exec . "\"", $output);

    /*
    // START MONITOR MODE (mon0)
    $iface_mon0 = exec("/sbin/ifconfig |grep mon0");
    if ($iface_mon0 == "") {
        $exec = "/usr/bin/sudo /usr/sbin/airmon-ng start $io_action_extra";
        //exec("/usr/share/FruityWifi/bin/danger \"" . $exec . "\"", $output); //DEPRECATED
        //exec("/usr/share/FruityWifi/bin/danger \"" . $exec . "\"");
    }
    */

    if ($_GET['action'] == "start") {

        // START MONITOR MODE (mon0)
        start_monitor_mode($io_in_iface_extra);

        // COPY LOG
        if (file_exists($mod_logs) and 0 < filesize($mod_logs)) {
            exec_fruitywifi(BIN_CP." $mod_logs $mod_logs_history/".gmdate("Ymd-H-i-s").".log");
            exec_fruitywifi(BIN_ECHO." -n > $mod_logs");
        }

        // START MODULE

        $options = "";
        //if ($service == "mode_b") {
        if ($ss_mode == "mode_b") {

            $mode = "b";
            $tmp = array_keys($mode_b);
            for ($i=0; $i< count($tmp); $i++) {
                 if ($mode_b[$tmp[$i]][0] == "1") {
                    if ($tmp[$i] == "f" or $tmp[$i] == "v") {
                        $options .= " -" . $tmp[$i] . " $mod_path/includes/templates/" . @$mode_b[$tmp[$i]][2];
                    } else {
                        $options .= " -" . $tmp[$i] . " " . @$mode_b[$tmp[$i]][2];
                    }
                }
            }

        //} else if ($service == "mode_a") {
        } elseif ($ss_mode == "mode_a") {

            $mode = "a";
            $tmp = array_keys($mode_a);
            for ($i=0; $i< count($tmp); $i++) {
                 if ($mode_a[$tmp[$i]][0] == "1") {
                        $options .= " -" . $tmp[$i] . " " . @$mode_a[$tmp[$i]][2];
                }
            }

        //} else if ($service == "mode_d") {
        } elseif ($ss_mode == "mode_d") {

            $mode = "d";
            $tmp = array_keys($mode_d);
            for ($i=0; $i< count($tmp); $i++) {
                 if ($mode_d[$tmp[$i]][0] == "1") {
                    if ($tmp[$i] == "w" or $tmp[$i] == "b") {
                        $options .= " -" . $tmp[$i] . " $mod_path/includes/templates/" . @$mode_d[$tmp[$i]][2];
                    } else {
                        $options .= " -" . $tmp[$i] . " " . @$mode_d[$tmp[$i]][2];
                    }
                }
            }

        }

        exec_fruitywifi(BIN_MDK3." mon0 $mode $options >> $mod_logs &");

    } elseif($_GET['action'] == "stop") {
        // STOP MODULE
        exec_fruitywifi(BIN_KILLALL." mdk3");

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