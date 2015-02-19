<?
include_once dirname(__FILE__)."/../../config/config.php";
include_once dirname(__FILE__)."/_info_.php";

require_once WWWPATH."/includes/login_check.php";
require_once WWWPATH."/includes/filter_getpost.php";
include_once WWWPATH."/includes/functions.php";

?>
<!DOCTYPE HTML>
<html lang="en">
<head>
<meta charset="utf-8" />
<title>FruityWifi</title>
<script src="<?=WEBPATH?>/js/jquery.js"></script>
<script src="<?=WEBPATH?>/js/jquery-ui.js"></script>
<link rel="stylesheet" href="<?=WEBPATH?>/css/jquery-ui.css" />
<link rel="stylesheet" href="<?=WEBPATH?>/css/style.css" />

<script>
$(function() {
    $( "#action" ).tabs();
    $( "#result" ).tabs();
});

</script>

</head>
<body>

<? include WWWPATH."/includes/menu.php"; ?>
<br/>
<?

$logfile = @$_GET['logfile'];
$action = @$_GET['action'];
$tempname = @$_GET['tempname'];

// DELETE LOG
if ($logfile != "" and $action=="delete") {
    if(file_exists($mod_logs_history.$logfile.".log")) {
        if(unlink($mod_logs_history.$logfile.".log")) {
            echo "Eliminado ".$mod_logs_history.$logfile.".log<br/>";
        } else {
            echo "Error al eliminar ".$mod_logs_history.$logfile.".log<br/>";
        }
    }
}

?>

<div class="rounded-top" align="left"> &nbsp; <?=$mod_alias?> </div>
<div class="rounded-bottom">

    &nbsp;&nbsp;&nbsp;version <?=$mod_version?><br>
    <?
    if (file_exists(BIN_SSLSPLIT) and fileperms(BIN_SSLSPLIT) & 0x0040 ) {
        echo "&nbsp;&nbsp;$mod_alias <font style='color:lime'>installed</font><br>";
    } else {
        //echo "&nbsp;&nbsp;&nbsp; ngrep <font style='color:red'>install</font><br>";
        echo "&nbsp;&nbsp;$mod_alias <a href='includes/module_action.php?install=install_sslstrip' style='color:red'>install</a><br>";
    }

    $issslstripup = exec($mod_isup);
    if ($issslstripup != "") {
        echo "&nbsp;&nbsp;$mod_alias <font color=\"lime\"><b>enabled</b></font>.&nbsp; | <a href=\"includes/module_action.php?service=sslsplit&action=stop&page=module\"><b>stop</b></a><br />";
    } else {
        echo "&nbsp;&nbsp;$mod_alias  <font color=\"red\"><b>disabled</b></font>. | <a href=\"includes/module_action.php?service=sslsplit&action=start&page=module\"><b>start</b></a><br />";
    }

    ?>

</div>

<br>


<div id="msg" style="font-size:largest;">
Loading, please wait...
</div>

<div id="body" style="display:none;">

<div id="result" class="module">
    <ul>
        <li><a href="#result-1">Output</a></li>
        <li><a href="#result-2">History</a></li>
		<li><a href="#result-3">About</a></li>
    </ul>
    <!-- OUTPUT -->
    <div id="result-1">
        <form id="formLogs-Refresh" name="formLogs-Refresh" method="GET" autocomplete="off" action="includes/save.php">
        <input type="submit" value="refresh">
        <input type="hidden" name="mod_service" value="mod_sslstrip_filter">
        <br><br>
        <?
            if ($logfile != "" and $action == "view") {
                $filename = $mod_logs_history.$logfile.".log";
            } else {
                $filename = $mod_logs;
            }

                $data = open_file($filename);

                $data_array = explode("\n", $data);
                //$data = implode("\n",array_reverse($data_array));
                //$data = array_reverse($data_array);
                $data = $data_array;

        ?>
        <textarea id="output" class="module-content" style="font-family: courier;"><?
            //htmlentities($data)

            for ($i=0; $i < count($data); $i++) {
                echo htmlentities($data[$i]) . "\n";
            }

        ?></textarea>
        <input type="hidden" name="type" value="logs">
        </form>
    </div>

    <!-- HISTORY -->

    <div id="result-2" class="history">

        <table border="0">
        <?
        $logs = glob($mod_logs_history.'*.log');
        for ($i = 0; $i < count($logs); $i++) {
            $filename = str_replace(".log","",str_replace($mod_logs_history,"",$logs[$i]));
            ?>
            <tr>
              <td><a href='?logfile=<?=$filename?>&action=delete&tab=1'><b>x</b></a></td>
              <td><?=$filename?></td>
              <td><?=filesize($logs[$i])?></td>
              <td><a href='?logfile=<?=$filename?>&action=view'><b>view</b></a></td>
            </tr>
            <?
        }
        ?>
        </table>

    </div>

	<!-- ABOUT -->

	<div id="result-3" class="history">
		<? include "includes/about.php"; ?>
	</div>

	<!-- END ABOUT -->


</div>


<?
if (isset($_GET['tab']) and is_numeric($_GET['tab'])) {
    echo "<script>";
    echo "$( '#result' ).tabs({ active: ".$_GET['tab']." });";
    echo "</script>";
}
?>

</div>

<script type="text/javascript">
$(document).ready(function() {
    $('#body').show();
    $('#msg').hide();
});
</script>

</body>
</html>