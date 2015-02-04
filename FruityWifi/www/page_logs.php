<?
include_once "config/config.php";

require_once WWWPATH."includes/login_check.php";
require_once WWWPATH."includes/filter_getpost.php";
include_once WWWPATH."includes/functions.php";

include_once WWWPATH."includes/menu.php";

function showLog($filename, $path) {

	$fh = fopen($path, "r"); // or die("Could not open file.");
	if(filesize($path)) {
		$data = fread($fh, filesize($path)); // or die("Could not read file.");
		fclose($fh);
		$data_array = explode("\n", $data);
		$data = implode("\n",array_reverse($data_array));

		$data = htmlspecialchars($data);

	echo "
		<br/>
		<div class='rounded-top' align='left'> &nbsp; <b>$filename</b> </div>
		<textarea name='newdata' rows='10' cols='100' class='module-content' style='font-family: courier; overflow: auto; height:200px;'>$data</textarea>
		<br/>
	";
	}
}

$logs = glob(LOGPATH.'*');

for ($i = 0; $i < count($logs); $i++) {
	$filename = str_replace(LOGPATH,"",$logs[$i]);
	//echo "$filename<br>";
	if ($filename != "install.txt") showLog($filename, $logs[$i]);
}
?>
