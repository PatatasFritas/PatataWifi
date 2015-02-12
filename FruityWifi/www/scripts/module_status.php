<?
include_once dirname(__FILE__)."/../config/config.php";

require_once WWWPATH."/includes/login_check.php";
require_once WWWPATH."/includes/filter_getpost.php";
//include_once WWWPATH."/includes/functions.php";

$service = @$_POST['service'];
$service = str_replace("mod_", "", $service);

$ismoduleup = "";

if ($service == "s_wireless") {

    if ($ap_mode == "1") {
        $ismoduleup = exec("ps auxww | grep hostapd | grep -v -e grep");
    } else if ($ap_mode == "2") {
        $ismoduleup = exec("ps auxww | grep airbase | grep -v -e grep");
    } else if ($ap_mode == "3") {
        $ismoduleup = exec("ps auxww | grep hostapd | grep -v -e grep");
    } else if ($ap_mode == "4") {
        $ismoduleup = exec("ps auxww | grep hostapd | grep -v -e grep");
    }

} else if ($service == "s_phishing") {

    $ismoduleup = exec("grep 'FruityWifi-Phishing' /var/www/index.php");

} else {
	if(file_exists("../modules/".$service."/_info_.php")) {
	    include "../modules/".$service."/_info_.php";
	    if (isset($mod_isup))
	    	$ismoduleup = exec($mod_isup);
	}
}

if ($ismoduleup != "") {
    $output[0] = "true";
} else {
    $output[0] = "false";
}

echo json_encode($output);
?>