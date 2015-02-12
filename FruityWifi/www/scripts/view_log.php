<link href="../style.css" rel="stylesheet" type="text/css">
<pre>
<?
include_once dirname(__FILE__)."/../../../config/config.php";
include_once dirname(__FILE__)."/../_info_.php";

require_once WWWPATH."/includes/login_check.php";
require_once WWWPATH."/includes/filter_getpost.php";
include_once WWWPATH."/includes/functions.php";

// Checking POST & GET variables...
if ($regex == 1) {
    regex_standard($_GET['module'], "../msg.php", $regex_extra);
    regex_standard($_GET['file'], "../msg.php", $regex_extra);
}

function load_file ($filename) {
    $fh = fopen($filename, "r") or die("Could not open file.");
    $data = fread($fh, filesize($filename)) or die("Could not read file.");
    fclose($fh);
    return $data;
}

if (isset($_GET['module']) and $_GET['module'] == "sslstrip" and isset($_GET['file'])) {
    //echo $file.".log";
    $file = $_GET['file']
    $data = load_file("../logs/sslstrip/".$file.".log");
    echo $data;
}

?>
</pre>