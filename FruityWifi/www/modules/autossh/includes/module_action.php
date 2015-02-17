<?
include_once dirname(__FILE__)."/../../../config/config.php";
include_once dirname(__FILE__)."/../_info_.php";

require_once WWWPATH."/includes/login_check.php";
require_once WWWPATH."/includes/filter_getpost.php";
include_once WWWPATH."/includes/functions.php";

include "options_config.php";

if(isset($_GET['service']) and $_GET['service'] == "autossh" and isset($_GET['action'])) {

    if ($_GET['action'] == "start") {

        // COPY LOG
        if (file_exists($mod_logs) and 0 < filesize($mod_logs)) {
            exec_fruitywifi(BIN_CP." $mod_logs $mod_logs_history/".gmdate("Ymd-H-i-s").".log");
            exec_fruitywifi(BIN_ECHO." -n > $mod_logs");
        }

        exec_fruitywifi(BIN_AUTOSSH." -M 0 -o 'ServerAliveInterval 60' -o 'ServerAliveCountMax 3' -N -R $autossh_listen:localhost:$autossh_port $autossh_user@$autossh_host -i id_rsa > /dev/null &");

    } elseif($_GET['action'] == "stop") {
        // STOP MODULE
        exec_fruitywifi(BIN_KILLALL." $mod_name");

        // COPY LOG
        if (file_exists($mod_logs) and 0 < filesize($mod_logs)) {
            exec_fruitywifi(BIN_CP." $mod_logs $mod_logs_history/".gmdate("Ymd-H-i-s").".log");
            exec_fruitywifi(BIN_ECHO." -n > $mod_logs");
        }
    }
}

if (isset($_POST['ssh_cert']) and $_POST['ssh_cert'] == "gen_certificate") {
    exec_fruitywifi(BIN_RM." id_rsa");
    exec_fruitywifi(BIN_RM." id_rsa.pub");
    exec_fruitywifi(BIN_SSH_KEYGEN." -t rsa -f id_rsa -C @FruityWifi");

    header('Location: ../index.php');
    exit;
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