<?
include_once dirname(__FILE__)."/../config/config.php";

require_once WWWPATH."includes/login_check.php";
//require_once WWWPATH."includes/filter_getpost.php";
//include_once WWWPATH."includes/functions.php";

$filename = LOGPATH."/dhcp.leases";

if ( 0 < filesize( $filename ) ) {
	$fh = fopen($filename, "r"); //or die("Could not open file.");
	$data = fread($fh, filesize($filename)); //or die("Could not read file.");

	fclose($fh);
	$data = explode("\n",$data);

	for ($i=0; $i < count($data); $i++) {
		if(substr_count($data[$i], " ")>=3) {
			$tmp = explode(" ", $data[$i]);
			$output[] = $tmp[2] . " " . $tmp[1] . " " . $tmp[3];
			//echo $tmp[2] . " " . $tmp[3] . " " . $tmp[4] . "<br>";
		}
	}
}
echo json_encode($output);
?>
