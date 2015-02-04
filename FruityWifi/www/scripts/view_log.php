<link href="../style.css" rel="stylesheet" type="text/css">
<pre>
<?
include "../login_check.php";
include "../config/config.php";
include "../functions.php";

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

$module = $_GET['module'];
$file = $_GET['file'];

if ($module == "sslstrip") {
    //echo $file.".log";
    $data = load_file("../logs/sslstrip/".$file.".log");
    echo $data;
}

?>
</pre>