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

@$newdata = $_POST['newdata'];
@$logfile = $_GET['logfile'];
@$action = $_GET['action'];

// SAVE DNSSPOOF HOSTS
if ($newdata != "") {
    exec_fruitywifi(BIN_ECHO." '$newdata' > /usr/share/fruitywifi/conf/spoofhost.conf");
    exec_fruitywifi(BIN_DOS2UNIX." /usr/share/fruitywifi/conf/spoofhost.conf");
}

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
    if (file_exists(BIN_DNSSPOOF)) {
        echo "&nbsp;&nbsp;$mod_alias <font style='color:lime'>installed</font><br>";
    } else {
	echo "&nbsp;&nbsp;$mod_alias <a href='includes/module_action.php?install=install_$mod_name' style='color:red'>install</a><br>";
    }
    ?>

    <?
    $isdnsspoofup = exec($mod_isup);
    if ($isdnsspoofup != "") {
        echo "&nbsp;&nbsp;$mod_alias  <font color=\"lime\"><b>enabled</b></font>.&nbsp; | <a href='includes/module_action.php?service=dnsspoof&action=stop&page=module'><b>stop</b></a>";
    } else {
        echo "&nbsp;&nbsp;$mod_alias  <font color=\"red\"><b>disabled</b></font>. | <a href='includes/module_action.php?service=dnsspoof&action=start&page=module'><b>start</b></a>";
    }

    ?>

</div>

<br>

<div id="result" class="module">
    <ul>
        <li><a href="#result-1">Output</a></li>
        <li><a href="#result-2">History</a></li>
        <li><a href="#result-3">Hosts</a></li>
	<li><a href="#result-4">About</a></li>
    </ul>
    <div id="result-1" >
        <form id="formLogs" name="formLogs" method="POST" autocomplete="off">
        <?
            if ($action == "view" and $logfile != "" and file_exists($mod_logs_history.$logfile.".log")) {
                $filename = $mod_logs_history.$logfile.".log";
                echo("<b>$logfile.log</b> <a href=\"./\">close</a><br/>");
            } else {
                $filename = $mod_logs;
                echo '<input type="submit" value="refresh" /><br/><br/>';
            }

            $data = open_file($filename);

            $data_array = explode("\n", $data);
            $data = implode("\n",array_reverse($data_array));

        ?>
        <textarea id="output" class="module-content"><?=$data?></textarea>
        <input type="hidden" name="type" value="logs">
        </form>
    </div>
    <!-- HISTORY -->
    <div id="result-2" class="history">
        <br/><br/>
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

    <!-- HOSTS -->

	<div id="result-3" >
        <form id="formHosts" name="formHosts" method="POST" autocomplete="off" action="index.php?tab=2">
        <input type=submit value="save">
        <br/><br/>
        <?
            $filename = "/usr/share/fruitywifi/conf/spoofhost.conf";
            $data = open_file($filename);

        ?>
        <textarea id="hosts" name="newdata" class="module-content"><?=$data?></textarea>
        <input type="hidden" name="type" value="hosts">
        </form>
    </div>

	<!-- END HOSTS -->

	<!-- ABOUT -->

	<div id="result-4" class="history">
	    <? include "includes/about.php"; ?>
	</div>

	<!-- END ABOUT -->

</div>

<?
if (isset($_REQUEST['tab']) and is_numeric($_REQUEST['tab'])) {
    echo "<script>";
    echo "$( '#result' ).tabs({ active: ".($_REQUEST['tab'] - 1)." });";
    echo "</script>";
}
?>

</body>
</html>
