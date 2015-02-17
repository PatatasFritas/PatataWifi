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

<script>
function OnChangeType (obj){
    var scan;
    var target;

    target = document.getElementById("target").value;

    if (obj.value == 0) {
        scan = "nmap " + target;
    } else if (obj.value == 1) {
        scan = "nmap -T4 -A -v " + target;
    } else if (obj.value == 2) {
        scan = "nmap -sn " + target;
    } else if (obj.value == 3) {
        scan = "nmap -T4 -F " + target;
    } else if (obj.value == 4) {
        scan = "nmap -sn --traceroute " + target;
    }

    form1.command.value = scan;

}

function OnChangeTarget (obj) {
    var target;
    var scan_type;
    //alert(document.getElementById("scan_type").value);

    target = document.getElementById("target").value;
    scan_type = document.getElementById("scan_type").value;

    //alert(obj.value);

    if (scan_type == 1) {
        //alert(1);
        scan = "nmap -F " + target;
        //document.getElementById("command").value = scan;
        //document.getElementById("command").value = scan;
        form1.command.value = scan;
    }

    if (scan_type == 0) {
        scan = "nmap " + target;
    } else if (scan_type == 1) {
        scan = "nmap -T4 -A -v " + target;
    } else if (scan_type == 2) {
        scan = "nmap -sn " + target;
    } else if (scan_type == 3) {
        scan = "nmap -T4 -F " + target;
    } else if (scan_type == 4) {
        scan = "nmap -sn --traceroute " + target;
    }

    form1.command.value = scan;


}
</script>

</head>
<body>

<? include WWWPATH."/includes/menu.php"; ?>
<br/>
<?

$logfile = @$_GET['logfile'];
$action = @$_GET['action'];

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

<div class="rounded-top" align="left"> &nbsp; <b>Nmap</b> </div>
<div class="rounded-bottom">
    &nbsp;&nbsp;version <?=$mod_version?><br/>
    <?
    if (file_exists(BIN_NMAP) and fileperms(BIN_NMAP) & 0x0040 ) {
        echo "&nbsp;&nbsp;$mod_alias <font style='color:lime'>installed</font><br>";
    } else {
        //echo "&nbsp;&nbsp;&nbsp; ngrep <font style='color:red'>install</font><br>";
        echo "&nbsp;&nbsp;$mod_alias <a href='includes/module_action.php?install=install_nmap' style='color:red'>install</a><br>";
    }
    ?>
</div>

<br>

<form id="form1" method="post" autocomplete="off">
    <div id="action" class="module">
        <ul>
            <li><a href="#action-1">General</a></li>
        </ul>
        <div id="action-1">
            Target: <input class="ui-widget" type="text" name="target" id="target" style="width:200px" onchange="OnChangeTarget(1)" onkeypress="OnChangeTarget(1)">
            <select id="scan_type" onchange="OnChangeType(this)" disabled="disabled">
		<option value=0>Default</option>
		<option value=1>Intense scan</option>
		<option value=2>Ping scan</option>
		<option value=3>Quick scan</option>
		<option value=4>Quick traceroute</option>
        </select>
        </div>
    </div>

    <div id="command" class="ui-widget module" style="width:100%;padding-top:4px; padding-bottom:4px;">
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp
	<input class="ui-widget" type="text" name="command" id="command" value="" style="width:200px">
	<input type="submit" name="submit" id="submit" value="Scan" class="ui-widget">
    </div>
</form>

<br>

<div id="result" class="module" >
    <ul>
        <li><a href="#result-1">Output</a></li>
        <li><a href="#result-2">History</a></li>
    </ul>
    <div id="result-1">
        <?
            if ($action == "view" and $logfile != "" and file_exists($mod_logs_history.$logfile.".log")) {
                $filename = $mod_logs_history.$logfile.".log";
                echo("<b>$logfile.log</b> <a href=\"?tab=2\">close</a><br/>");

                $data = open_file($filename);

                $data_array = explode("\n", $data);
                //$data = implode("\n",array_reverse($data_array));
                $data = implode("\n",$data_array);

                ?><textarea id="output" class="module-content"><?=$data?></textarea><?
            } else {
                ?><textarea id="output" class="module-content" style="display:none;"></textarea><?
            }



        ?>
    </div>

    <!-- HISTORY -->

    <div id="result-2">
        <br/><br/>
            <table border="0">
            <?
            $logs = glob($mod_logs_history.'*.log');
            for ($i = 0; $i < count($logs); $i++) {
                $filename = str_replace(".log","",str_replace($mod_logs_history,"",$logs[$i]));
                ?>
                <tr>
                  <td><a href='?logfile=<?=$filename?>&action=delete&tab=2'><b>x</b></a></td>
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

<div id="loading" class="ui-widget" style="width:100%;background-color:#000; padding-top:4px; padding-bottom:4px;color:#FFF">
    Loading...
</div>

<script>
$('#form1').submit(function(event) {
    event.preventDefault();
    $.ajax({
        type: 'POST',
        url: 'includes/ajax.php',
        data: $(this).serialize(),
        dataType: 'json',
        success: function (data) {
            //console.log(data);
            $('#output').html('');
            $.each(data, function (index, value) {
                $("#output").append( value ).append("\n");
            });
            $('#loading').hide();
            $('#output').show();
        }
    });
    $('#output').html('');
    $('#loading').show()
});
$('#loading').hide();

</script>
    <?
    if (isset($_REQUEST['tab']) and is_numeric($_REQUEST['tab'])) {
        echo "<script>";
        echo "$( '#result' ).tabs({ active: ".($_REQUEST['tab'] - 1)." });";
        echo "</script>";
    }
    ?>

</body>
</html>
