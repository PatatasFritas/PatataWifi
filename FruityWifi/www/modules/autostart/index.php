<?
include_once dirname(__FILE__)."/../../config/config.php";
include_once dirname(__FILE__)."/_info_.php";

require_once WWWPATH."/includes/login_check.php";
require_once WWWPATH."/includes/filter_getpost.php";
include_once WWWPATH."/includes/functions.php";

include_once "includes/options_config.php";

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


$logfile = @$_GET["logfile"];
$action = @$_GET["action"];

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

<div class="rounded-top" align="left"> &nbsp; <b><?=$mod_alias?></b> </div>
<div class="rounded-bottom">

    &nbsp;&nbsp;version <?=$mod_version?><br>
    <?
    $isinstalled = exec("dpkg-query -s php5-curl|grep -iEe '^status.+installed'");
    if ($isinstalled != "") {
        echo "$mod_alias <font style='color:lime'>installed</font><br>";
    } else {
        echo "$mod_alias <a href='includes/module_action.php?install=install_autostart' style='color:red'>install</a><br>";
    }
    ?>

    <?
    $ismoduleup = exec($mod_isup);
    if ($ismoduleup != "") {
        echo "$mod_alias  <font color=\"lime\"><b>enabled</b></font>.&nbsp; | <a href=\"includes/module_action.php?service=responder&action=stop&page=module\"><b>stop</b></a>";
    } else {
        echo "$mod_alias  <font color=\"red\"><b>disabled</b></font>. | <a href=\"includes/module_action.php?service=responder&action=start&page=module\"><b>start</b></a>";
    }
    ?>

</div>
<br/>

<div id="msg" style="font-size:largest;">
Loading, please wait...
</div>

<div id="body" style="display:none;">


    <div id="result" class="module">
        <ul>
            <li><a href="#result-1">Output</a></li>
            <li><a href="#result-2">Options</a></li>
            <li><a href="#result-3">History</a></li>
        </ul>

        <!-- OUTPUT -->

        <div id="result-1">
            <form id="formLogs-Refresh" name="formLogs-Refresh" method="POST" autocomplete="off" action="index.php">
            <input type="submit" value="refresh">
            <br><br>
            <?
                if ($logfile != "" and $action == "view") {
                    $filename = $mod_logs_history.$logfile.".log";
                } else {
                    $filename = $mod_logs;
                }

                $data = open_file($filename);

                // REVERSE
                //$data_array = explode("\n", $data);
                //$data = implode("\n",array_reverse($data_array));

            ?>
            <textarea id="output" class="module-content" style="font-family: courier;"><?=htmlspecialchars($data)?></textarea>
            <input type="hidden" name="type" value="logs">
            </form>

        </div>

        <!-- OPTIONS -->

        <div id="result-2" class="module-options">
            <form id="formInject" name="formInject" method="POST" autocomplete="off" action="includes/save.php">
            <input type="submit" value="save">
            <br><br>

            <div class="module-options" s-tyle="background-color:#000; border:1px dashed;">
            <table>

	        <?
		        foreach ($opt_responder as $opt=>$option) {
			?>
				<!-- // OPTION <?=$opt?>:<?=$opt_responder[$opt][2]?> -->
		        <tr>
                    <td><input type="checkbox" name="options[]" value="<?=$opt?>" <? if ($opt_responder[$opt][0] == "1") echo "checked" ?> ></td>
                    <td><?=$opt_responder[$opt][2]?></td>
                    <td></td>
                </tr>
		    <?
		         }
		    ?>

            </table>
            </div>

            <input type="hidden" name="type" value="opt_responder">
            </form>
            <br>
            <?
                $filename = "$mod_path/includes/mode_d.txt";

                $data = open_file($filename);

            ?>

        </div>

        <!-- HISTORY -->

        <div id="result-3">

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