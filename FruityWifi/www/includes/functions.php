<?
require_once dirname(__FILE__) . '/../config/config.php';

# [Verifica characteres -> [a-z0-9-_. ] ]
function regex_standard($var, $url, $regex_extra) {

    $regex_extra = implode("\\", str_split($regex_extra));
    $regex = "/(?i)(^[a-z0-9$regex_extra]{1,40}$)|(^$)/";

    if (preg_match($regex, $var) == 0) {
        //header("Location: ".$referer."?error=1");
        echo "<pre>$regex\n$var</pre>"; //DEBUG
        echo "<br/><br/>FAIL!!<br/><br/>"; //DEBUG
        //echo "<script>window.location = '$url?msg=1';</script>";
        //echo "<script>window.location = '$url?msg=1&debug=$var&regex=$regex&extra=$regex_extra';</script>";
        exit;
    }

}

function exec_fruitywifi($exec) {
    $bin_exec = "/usr/bin/sudo";
    exec("$bin_exec sh -c \"$exec\"", $output);
    //exec("$bin_exec sh -c \"$exec\" 2>&1", $output); //DEBUG SHOW ERRORS (da problemas cuando se usa para ejecutar un servicio)
    //LOG
    $rs = fopen(LOGPATH."/exec.log", 'a');
    fwrite($rs, date("Y-m-d H:i:s")." - "."$bin_exec sh -c \"$exec\"\n");

    if(is_array($output) and array_key_exists(0, $output)) {
        fwrite($rs, "\t".$output[0]."\n\n");
    } elseif (is_string($output)) {
        fwrite($rs, "\t".$output."\n\n");
    }

    fclose($rs);

    return $output;
}


function exec_log( $command , array &$output = NULL , int &$return_var = NULL ) {

    $output2 = exec($command, $output, $return_var);
    //LOG
    $rs = fopen(LOGPATH."/exec.log", 'a');
    fwrite($rs, date("Y-m-d H:i:s")." - ".$command."\n");
    if(is_array($output) and array_key_exists(0, $output)) {
        fwrite($rs, "\t".$output[0]."\n\n");
    } elseif (is_string($output)) {
        fwrite($rs, "\t".$output."\n\n");
    }
    fclose($rs);

    return $output2;
}


function module_deb($mod_name) {

    $module="fruitywifi-module-$mod_name";

    $exec = "apt-cache policy $module";
    exec($exec, $output);

    //print_r($output);

    if(empty($output)) {
	   //echo "none...";
	   return 0;
    } else {

	$installed = explode(" ", trim($output[1]));
	$candidate = explode(" ", trim($output[2]));

	if( $installed[1] == $candidate[1] ) {
	    //echo "installed...";
	    return 1;
	} else if( $installed[1] == "(none)" ) {
	    //echo "install...";
	    return 2;
	} else {
	    //echo "upgrade...";
	    return 3;
	}

    }
}

function start_monitor_mode($iface) {

    // START MONITOR MODE (mon0)
    $iface_mon0 = exec("/sbin/ifconfig |grep mon0");
    if ($iface_mon0 == "") {
	   exec_fruitywifi("/usr/sbin/airmon-ng start $iface");
    }

}

function stop_monitor_mode($iface) {

    // START MONITOR MODE (mon0)
    $iface_mon0 = exec("/sbin/ifconfig |grep mon0");
    if ($iface_mon0 != "") {
	   exec_fruitywifi("/usr/sbin/airmon-ng stop mon0");
    }

}

function open_file($filename) {

    if ( file_exists($filename) ) {
        if ( 0 < filesize( $filename ) ) {
            $fh = fopen($filename, "r");//  or die("Could not open file: $filename\n");
            $data = fread($fh, filesize($filename)); // or die("Could not read file.");
            fclose($fh);
            return $data;
        }
    }

}

function start_iface($iface, $ip, $gw) {

    // START MONITOR MODE (mon0)
    //$iface_mon0 = exec("/sbin/ifconfig |grep mon0");
    //if ($iface_mon0 == "") {
	exec_fruitywifi("/sbin/ifconfig $iface $ip");
	//}

	if (trim($gw) != "") {
	    exec_fruitywifi("/sbin/route add default gw $gw");
	}

}

function stop_iface($iface, $ip, $gw) {

    // START MONITOR MODE (mon0)
    //$iface_mon0 = exec("/sbin/ifconfig |grep mon0");
    //if ($iface_mon0 != "") {
	exec_fruitywifi("/sbin/ifconfig $iface 0.0.0.0");
    //}

}

?>