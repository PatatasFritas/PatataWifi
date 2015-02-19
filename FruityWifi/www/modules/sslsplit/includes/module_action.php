<?
include_once dirname(__FILE__)."/../../../config/config.php";
include_once dirname(__FILE__)."/../_info_.php";

require_once WWWPATH."/includes/login_check.php";
require_once WWWPATH."/includes/filter_getpost.php";
include_once WWWPATH."/includes/functions.php";

if(isset($_GET['service']) and $_GET['service'] == "sslsplit" and isset($_GET['action'])) {
    if ($_GET['action'] == "start") {

        // COPY LOG
        if (file_exists($mod_logs) and 0 < filesize($mod_logs)) {
            exec_fruitywifi(BIN_CP." $mod_logs $mod_logs_history/sslsplit-".gmdate("Ymd-H-i-s").".log");
            exec_fruitywifi(BIN_ECHO." -n > $mod_logs");
        }

        //delete iptables rules
        exec_fruitywifi(BIN_IPTABLES." -t nat -D PREROUTING -p tcp --destination-port 80 -j REDIRECT --to-port 10080");
		exec_fruitywifi(BIN_IPTABLES." -t nat -D PREROUTING -p tcp --destination-port 443 -j REDIRECT --to-port 10443");

        #iptables -t nat -A PREROUTING -p tcp --dport 80 -j REDIRECT --to-ports 10080
        #iptables -t nat -A PREROUTING -p tcp --dport 443 -j REDIRECT --to-ports 10443
        // add redirect iptables rules
		exec_fruitywifi(BIN_IPTABLES." -t nat -A PREROUTING -p tcp --destination-port 80 -j REDIRECT --to-port 10080");
		exec_fruitywifi(BIN_IPTABLES." -t nat -A PREROUTING -p tcp --destination-port 443 -j REDIRECT --to-port 10443");

        #./sslsplit
        exec_fruitywifi(BIN_SSLSPLIT."  -D -l connections.log -j /usr/share/fruitywifi/www/modules/sslsplit/includes/jail/ -S logdir/   -k ca.key   -c ca.crt    ssl 10.0.0.1 10443    tcp 10.0.0.1 10080 > $mod_logs &");

    } elseif($_GET['action'] == "stop") {
		exec_fruitywifi(BIN_IPTABLES." -t nat -D PREROUTING -p tcp --destination-port 80 -j REDIRECT --to-port 10080");
		exec_fruitywifi(BIN_IPTABLES." -t nat -D PREROUTING -p tcp --destination-port 443 -j REDIRECT --to-port 10443");
		exec_fruitywifi(BIN_KILLALL." sslsplit");

		// COPY LOG
        if (file_exists($mod_logs) and 0 < filesize($mod_logs)) {
            exec_fruitywifi(BIN_CP." $mod_logs $mod_logs_history/sslsplit-".gmdate("Ymd-H-i-s").".log");
            exec_fruitywifi(BIN_ECHO." -n > $mod_logs");
        }

    }
}



if (isset($_GET['install']) and $_GET['install'] == "install_sslsplit") {

    if(!is_dir($mod_logs_history)) {
        exec_fruitywifi(BIN_MKDIR." -p $mod_logs_history");
        exec_fruitywifi(BIN_CHOWN." fruitywifi:fruitywifi $mod_logs_history");
    }

    exec_fruitywifi(BIN_CHMOD." 755 install.sh");
    exec_fruitywifi("./install.sh > ".LOGPATH."/install.txt &");

    header('Location: '.WEBPATH.'/modules/install.php?module=sslsplit');
    exit;
}

if (isset($_GET['page']) and $_GET['page'] == "status") {
    header('Location: '.WEBPATH.'/action.php');
} else {
    header('Location: '.WEBPATH.'/modules/action.php?page=sslsplit');
}

?>