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
    if (file_exists(BIN_SSLSTRIP) and fileperms(BIN_SSLSTRIP) & 0x0040 ) {
        echo "&nbsp;&nbsp;$mod_alias <font style='color:lime'>installed</font><br>";
    } else {
        //echo "&nbsp;&nbsp;&nbsp; ngrep <font style='color:red'>install</font><br>";
        echo "&nbsp;&nbsp;$mod_alias <a href='includes/module_action.php?install=install_sslstrip' style='color:red'>install</a><br>";
    }

    $issslstripup = exec($mod_isup);
    if ($issslstripup != "") {
        echo "&nbsp;&nbsp;$mod_alias <font color=\"lime\"><b>enabled</b></font>.&nbsp; | <a href=\"includes/module_action.php?service=sslstrip&action=stop&page=module\"><b>stop</b></a><br />";
    } else {
        echo "&nbsp;&nbsp;$mod_alias  <font color=\"red\"><b>disabled</b></font>. | <a href=\"includes/module_action.php?service=sslstrip&action=start&page=module\"><b>start</b></a><br />";
    }

    if ($mod_sslstrip_inject == 1) {
        echo "&nbsp&nbsp;&nbsp;&nbsp;Inject  <font color=\"lime\"><b>enabled</b></font>.&nbsp; | <a href=\"includes/save.php?mod_service=mod_sslstrip_inject&mod_action=0\"><b>stop</b></a><br />";
    } else {
        echo "&nbsp&nbsp;&nbsp;&nbsp;Inject  <font color=\"red\"><b>disabled</b></font>. | <a href=\"includes/save.php?mod_service=mod_sslstrip_inject&mod_action=1\"><b>start</b></a><br />";
    }

    if ($mod_sslstrip_tamperer == 1) {
        echo "&nbsp;&nbsp;Tamperer  <font color=\"lime\"><b>enabled</b></font>.&nbsp; | <a href=\"includes/save.php?mod_service=mod_sslstrip_tamperer&mod_action=0\"><b>stop</b></a><br />";
    } else {
        echo "&nbsp;&nbsp;Tamperer  <font color=\"red\"><b>disabled</b></font>. | <a href=\"includes/save.php?mod_service=mod_sslstrip_tamperer&mod_action=1\"><b>start</b></a><br />";
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
        <li><a href="#result-3">Inject</a></li>
        <li><a href="#result-4">Tamperer</a></li>
        <li><a href="#result-5">Templates</a></li>
        <li><a href="#result-6">Filters</a></li>
		<li><a href="#result-7">About</a></li>
    </ul>
    <!-- OUTPUT -->
    <div id="result-1">
        <form id="formLogs-Refresh" name="formLogs-Refresh" method="GET" autocomplete="off" action="includes/save.php">
        <input type="submit" value="refresh">
        <input type="hidden" name="mod_service" value="mod_sslstrip_filter">
        <select style="module" name="mod_action" onchange='this.form.submit()'>
            <option value="" <? if ($mod_sslstrip_filter == "") echo 'selected'; ?> >-</option>
            <option value="LogEx.py" <? if ($mod_sslstrip_filter == "LogEx.py") echo 'selected'; ?>>LogEx.py</option>
            <option value="ParseLog.py" <? if ($mod_sslstrip_filter == "ParseLog.py") echo 'selected'; ?>>ParseLog.py</option>
        </select>
        <br><br>
        <?
            if ($logfile != "" and $action == "view") {
                $filename = $mod_logs_history.$logfile.".log";
            } else {
                $filename = $mod_logs;
            }

            if ($mod_sslstrip_filter == "LogEx.py") {
                $output = exec_fruitywifi(BIN_PYTHON." $mod_path/includes/filters/LogEx.py $filename");

                //$data = implode("\n",$output);
                $data = $output;
            } else if ($mod_sslstrip_filter == "ParseLog.py") {
                $output = exec_fruitywifi(BIN_PYTHON." $mod_path/includes/filters/ParseLog.py $filename $mod_path/includes/filters");

                //$data = implode("\n",$output);
                $data = $output;
            } else {

                $data = open_file($filename);

                $data_array = explode("\n", $data);
                //$data = implode("\n",array_reverse($data_array));
                //$data = array_reverse($data_array);
                $data = $data_array;
            }

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
    <div id="result-3" >
        <form id="formInject" name="formInject" method="POST" autocomplete="off" action="includes/save.php">
        <input type="submit" value="save">
        <br><br>
        <?
            $filename = "$mod_path/includes/inject.txt";
            $data = open_file($filename);
        ?>
        <textarea id="inject" name="newdata" class="module-content" style="font-family: courier;"><?=htmlspecialchars($data)?></textarea>
        <input type="hidden" name="type" value="inject">
        </form>
    </div>
    <div id="result-4" >
        <form id="formTamperer" name="formTamperer" method="POST" autocomplete="off" action="includes/save.php">
        <input type="submit" value="save">
        <br><br>
        <?
            $filename = "$mod_path/includes/app_cache_poison/config.ini";
            $data = open_file($filename);
        ?>
        <textarea id="inject" name="newdata" class="module-content" style="font-family: courier;"><?=htmlspecialchars($data)?></textarea>
        <input type="hidden" name="type" value="tamperer">
        </form>
    </div>
    <div id="result-5" >
        <form id="formTemplates" name="formTemplates" method="POST" autocomplete="off" action="includes/save.php">
        <input type="submit" value="save">

        <br><br>
        <?
        	if ($tempname != "") {
            	$filename = "$mod_path/includes/app_cache_poison/templates/".$tempname;
                $data = open_file($filename);
			} else {
				$data = "";
			}



        ?>
        <textarea id="inject" name="newdata" class="module-content" style="font-family: courier;"><?=htmlspecialchars($data)?></textarea>
        <input type="hidden" name="type" value="templates">
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="tempname" value="<?=$tempname?>">
        </form>

    <br>

    <table border=0 cellspacing=0 cellpadding=0>
    	<tr>
    	<td class="general">
    		Template
    	</td>
    	<td>
        <form id="formTempname" name="formTempname" method="POST" autocomplete="off" action="includes/save.php">
    		<select name="tempname" onchange='this.form.submit()'>
        	<option value="0">-</option>
        	<?
        	$template_path = "$mod_path/includes/app_cache_poison/templates/";
        	$templates = glob($template_path.'*');
        	//print_r($templates);

        	for ($i = 0; $i < count($templates); $i++) {
            	$filename = str_replace($template_path,"",$templates[$i]);
            	if ($filename == $tempname) echo "<option selected>"; else echo "<option>";
            	echo "$filename";
            	echo "</option>";
        	}
        	?>
        	</select>
        	<input type="hidden" name="type" value="templates">
        	<input type="hidden" name="action" value="select">
    	</form>
        </td>
        <tr>
        <td class="general">
        	Add/Rename
        </td>
        <td>
        <form id="formTempname" name="formTempname" method="POST" autocomplete="off" action="includes/save.php">
        	<select name="new_rename">
        	<option value="0">- add template -</option>
        	<?
        	$template_path = "$mod_path/includes/app_cache_poison/templates/";
        	$templates = glob($template_path.'*');
        	//print_r($templates);

        	for ($i = 0; $i < count($templates); $i++) {
            	$filename = str_replace($template_path,"",$templates[$i]);
            	echo "<option>";
            	//if ($filename == $tempname) echo "<option selected>"; else echo "<option>";
            	echo "$filename";
            	echo "</option>";
        	}
        	?>

        	</select>
        	<input class="ui-widget" type="text" name="new_rename_file" value="" style="width:150px">
        	<input type="submit" value="add/rename">

        	<input type="hidden" name="type" value="templates">
        	<input type="hidden" name="action" value="add_rename">

        </form>
        </td>
        </tr>

        <tr><td><br></td></tr>

        <tr>
        <td>

        </td>
        <td>
        <form id="formTempDelete" name="formTempDelete" method="POST" autocomplete="off" action="includes/save.php">
        	<select name="new_rename">
        	<option value="0">-</option>
        	<?
        	$template_path = "$mod_path/includes/app_cache_poison/templates/";
        	$templates = glob($template_path.'*');
        	//print_r($templates);

        	for ($i = 0; $i < count($templates); $i++) {
            	//$filename = $templates[$i];
            	$filename = str_replace($template_path,"",$templates[$i]);
            	echo "<option>";
            	echo "$filename";
            	echo "</option>";
        	}
        	?>

        	</select>

        	<input type="submit" value="delete">

        	<input type="hidden" name="type" value="templates">
        	<input type="hidden" name="action" value="delete">

        </form>
        </td>
        </tr>
    </table>
    </div>

    <!-- START FILTERS -->

    <div id="result-6" >
        <form id="formFilters" name="formFilters" method="POST" autocomplete="off" action="includes/save.php">
        <input type="submit" value="save"> [ParseLog.py]

        <br><br>
        <?
        	if ($tempname != "") {
            	$filename = "$mod_path/includes/filters/resources/$tempname";

                $data = open_file($filename);

			} else {
				$data = "";
			}



        ?>
        <textarea id="inject" name="newdata" class="module-content" style="font-family: courier;"><?=htmlspecialchars($data)?></textarea>
        <input type="hidden" name="type" value="filters">
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="tempname" value="<?=$tempname?>">
        </form>

    <br>

    <table border=0 cellspacing=0 cellpadding=0>
    	<tr>
    	<td class="general" style="padding-right:10px">
    		Setup
    	</td>
    	<td>
        <form id="formFilters" name="formFilters" method="POST" autocomplete="off" action="includes/save.php">
    		<select name="tempname" onchange='this.form.submit()'>
        	<option value="0">-</option>
        	<?
        	$template_path = "$mod_path/includes/filters/resources/";
        	$templates = glob($template_path.'*');
        	//print_r($templates);

        	for ($i = 0; $i < count($templates); $i++) {
            	$filename = str_replace($template_path,"",$templates[$i]);
            	if ($filename == $tempname) echo "<option selected>"; else echo "<option>";
            	echo "$filename";
            	echo "</option>";
        	}
        	?>
        	</select>
        	<input type="hidden" name="type" value="filters">
        	<input type="hidden" name="action" value="select">
    	</form>
        </td>

    </table>
    </div>

    <!-- END FILTERS -->

	<!-- ABOUT -->

	<div id="result-7" class="history">
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