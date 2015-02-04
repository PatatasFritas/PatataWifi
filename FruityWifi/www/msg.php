<link href="css/style.css" rel="stylesheet" type="text/css">
<? include "menu.php" ?>

<br><br>
<div class="box-msg" align="center">
<b>
<?
$msg = $_GET["msg"];

if ($msg == 1) {
    echo "ERROR: Check your input...";
} else {
    echo "General error...";
}
?>
</b>
</div>