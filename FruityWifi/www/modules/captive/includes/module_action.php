<?
include_once dirname(__FILE__)."/../../../config/config.php";
include_once dirname(__FILE__)."/../_info_.php";

require_once WWWPATH."/includes/login_check.php";
require_once WWWPATH."/includes/filter_getpost.php";
include_once WWWPATH."/includes/functions.php";



if(isset($_GET['service']) and $_GET['service'] == "captive" and isset($_GET['action'])) {

    if ($_GET['action'] == "start") {
        // START MODULE

        // COPY LOG
        if (file_exists($mod_logs) and 0 < filesize($mod_logs)) {
            exec_fruitywifi(BIN_CP." $mod_logs $mod_logs_history/".gmdate("Ymd-H-i-s").".log");
            exec_fruitywifi(BIN_ECHO." -n > $mod_logs");
        }

        # Masquerade any incoming packet on the firewall
		exec_fruitywifi(BIN_IPTABLES." -A POSTROUTING -t nat -o $io_out_iface -j MASQUERADE");

        # Create a new chain named 'internet' in mangle table with this command
		exec_fruitywifi(BIN_IPTABLES." -t mangle -N internet");

        # Send all HTTP traffic from WIFI to the newly created chain for further processing
        exec_fruitywifi(BIN_IPTABLES." -t mangle -A PREROUTING -i $io_action -p tcp -m tcp --dport 80 -j internet");
		exec_fruitywifi(BIN_IPTABLES." -t mangle -A PREROUTING -i $io_action -p tcp -m tcp --dport 443 -j internet");

        # Mark all traffic from internet chain with 99
		exec_fruitywifi(BIN_IPTABLES." -t mangle -A internet -j MARK --set-mark 99");

        # Redirect all marked traffic to the portal
		exec_fruitywifi(BIN_IPTABLES." -t nat -A PREROUTING -i $io_action -p tcp -m mark --mark 99 -m tcp --dport 80 -j DNAT --to-destination $io_in_ip");
		exec_fruitywifi(BIN_IPTABLES." -t nat -A PREROUTING -i $io_action -p tcp -m mark --mark 99 -m tcp --dport 443 -j DNAT --to-destination $io_in_ip");

        # FORWARD
		exec_fruitywifi("echo '1' > /proc/sys/net/ipv4/ip_forward");

        // INCLUDE INDEX
		if (!file_exists("/var/www/index.php")) {
            exec_fruitywifi(BIN_ECHO." '.' >> /var/www/index.php");
        }

        $isphishingup = exec("grep 'FruityWifi-Phishing' /var/www/index.php");
        if ($isphishingup  != "") {
		    exec_fruitywifi("sed -i '/FruityWifi-Phishing/d' /var/www/index.php");
			exec_fruitywifi("sed -i 1i'<? include \\\"site\/index.php\\\"; \/\* FruityWifi-Phishing \*\/ ?>' /var/www/index.php");
			exec_fruitywifi("sed -i 1i'<? header(\\\"Location: site\/captive\/index.php\\\"); exit; \/\* FruityWifi-Captive \*\/ ?>' /var/www/index.php");

        } else {
	        if(file_exists("/var/www/index.php") and 0 < filesize("/var/www/index.php")) {
		    	exec_fruitywifi("sed -i 1i'<? header(\\\"Location: site\/captive\/index.php\\\"); exit; \/\* FruityWifi-Captive \*\/ ?>' /var/www/index.php");
	        } else {
		    	exec_fruitywifi("echo '<? header(\\\"Location: site/captive/index.php\\\"); exit; /* FruityWifi-Captive */ ?>' > /var/www/index.php");
	        }

        }


    } else if($_GET['action'] == "stop") {
        // STOP MODULE

        // REMOVE INCLUDE
		exec_fruitywifi("sed -i '/FruityWifi-Captive/d' /var/www/index.php");

        # Send all HTTP traffic from WIFI to the newly created chain for further processing
		exec_fruitywifi(BIN_IPTABLES." -t mangle -D PREROUTING -i $io_action -p tcp -m tcp --dport 80 -j internet");
		exec_fruitywifi(BIN_IPTABLES." -t mangle -D PREROUTING -i $io_action -p tcp -m tcp --dport 443 -j internet");

        # Mark all traffic from internet chain with 99
        exec_fruitywifi(BIN_IPTABLES." -t mangle -D internet -j MARK --set-mark 99");

        # Redirect all marked traffic to the portal
        exec_fruitywifi(BIN_IPTABLES." -t nat -D PREROUTING -i $io_action -p tcp -m mark --mark 99 -m tcp --dport 80 -j DNAT --to-destination $io_in_ip");
        exec_fruitywifi(BIN_IPTABLES." -t nat -D PREROUTING -i $io_action -p tcp -m mark --mark 99 -m tcp --dport 443 -j DNAT --to-destination $io_in_ip");

        // DELETE ALLOWED MAC RULES

		$output = exec_fruitywifi(BIN_IPTABLES." -t mangle -L --line-numbers | grep RETURN | ".BIN_AWK." '{print $1}'");

        for ($i=0; $i < count($output); $i++) {
			$output = exec_fruitywifi(BIN_IPTABLES." -t mangle -D internet 1");
        }

        // CLEAN USERS FILE
        exec_fruitywifi("echo '-' > $file_users");

        // COPY LOG
        if (file_exists($mod_logs) and 0 < filesize($mod_logs)) {
            exec_fruitywifi(BIN_CP." $mod_logs $mod_logs_history/".gmdate("Ymd-H-i-s").".log");
            exec_fruitywifi(BIN_ECHO." -n > $mod_logs");
        }

    }

}

if (isset($_GET['service']) and $_GET['service'] == "install_portal") {
	exec_fruitywifi("mkdir -p -v $captive_dir");
    exec_fruitywifi("/bin/ln -s $mod_path/www.captive $captive_site");
}

//$filename = "/var/www/site/captive/admin/users";
$filename = $file_users;

if (isset($_GET['service']) and $_GET['service'] == "users" and isset($_GET['mac']) and $_GET['mac'] != "") {
    $mac = strtoupper($_GET['mac']);
    $mac = trim($mac,'*');

    if ($_GET['action'] == "delete") {
		exec_fruitywifi(BIN_SED." -i '/$mac/d' $filename");
		exec_fruitywifi(BIN_IPTABLES." -D internet -t mangle -m mac --mac-source $mac -j RETURN");

		// ADD TO LOGS
		exec_fruitywifi(BIN_ECHO." 'DELETE: $mac|".date("Y-m-d h:i:s")."' >> $mod_logs ");
    }

    header('Location: ../index.php?tab=1');
    exit;
}


if (isset($_GET['install']) and $_GET['install'] == "install_captive") {

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