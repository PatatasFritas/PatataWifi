<?
include_once dirname(__FILE__)."/../../config/config.php";
include_once dirname(__FILE__)."/_info_.php";

require_once WWWPATH."/includes/login_check.php";
require_once WWWPATH."/includes/filter_getpost.php";
include_once WWWPATH."/includes/functions.php";

include "includes/options_config.php";

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
$service = @$_POST['service'];

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

// SET MODE
if (isset($_POST['change_mode']) and $_POST['change_mode'] == "1") {
    $ss_mode = $service;
    exec_fruitywifi("/bin/sed -i 's/ss_mode.*/ss_mode = \\\"".$ss_mode."\\\";/g' includes/options_config.php");
}

?>

<div class="rounded-top" align="left"> &nbsp; <b>mdk3</b> </div>
<div class="rounded-bottom">
  <form name="ss_mode" style="margin=0px" action="index.php" method="POST">

    &nbsp;&nbsp;version <?=$mod_version?><br>
    <?
    if (file_exists(BIN_MDK3) and fileperms(BIN_MDK3) & 0x0040) {
        echo "&nbsp;&nbsp;&nbsp;&nbsp; mdk3 <font style='color:lime'>installed</font><br>";
    } else {
        echo "&nbsp;&nbsp;&nbsp;&nbsp; mdk3 <a href='includes/module_action.php?install=install_mdk3' style='color:red'>install</a><br>";
    }
    ?>

    <?
    $ismdk3up = exec($mod_isup);
    if ($ismdk3up != "") {
        echo "&nbsp;&nbsp;&nbsp;&nbsp; mdk3  <font color=\"lime\"><b>enabled</b></font>.&nbsp; | <a href=\"includes/module_action.php?service=mdk3&action=stop&page=module\"><b>stop</b></a>";
        //echo "&nbsp;&nbsp;&nbsp;&nbsp; mdk3  <font color=\"lime\"><b>enabled</b></font>.&nbsp; | <a href='#' onclick='document.ss_mode.submit();'><b>stop</b></a>&nbsp;";
        //echo "<input type='hidden' name='service' value=''>";
        echo "<input type='hidden' name='action' value='stop'>";
        echo "<input type='hidden' name='page' value='module'>";
    } else {
        echo "&nbsp;&nbsp;&nbsp;&nbsp; mdk3  <font color=\"red\"><b>disabled</b></font>. | <a href=\"includes/module_action.php?service=mdk3&action=start&page=module\"><b>start</b></a>";
        //echo "&nbsp;&nbsp;&nbsp;&nbsp; mdk3  <font color=\"red\"><b>disabled</b></font>. | <a href='#' onclick='document.ss_mode.submit();'><b>start</b></a>";
        //echo "<input type='hidden' name='service' value=''>";
        echo "<input type='hidden' name='action' value='start'>";
        echo "<input type='hidden' name='page' value='module'>";
    }
    ?>

    <select name="service" class="module" onchange='this.form.submit()'>
        <option value="mode_b" <? if ($ss_mode == "mode_b") echo "selected"?> >[B] Beacon Flood Mode</option>
        <option value="mode_a" <? if ($ss_mode == "mode_a") echo "selected"?> >[A] Authentication DoS Mode</option>
        <option value="mode_d" <? if ($ss_mode == "mode_d") echo "selected"?> >[D] Deauthentication Mode</option>
    </select>

    <input type="hidden" name="change_mode" value="1">
  </form>
</div>

<br>

<div id="result" class="module">
    <ul>
        <li><a href="#result-1">Output</a></li>
        <li><a href="#result-2">B Mode</a></li>
        <li><a href="#result-3">A Mode</a></li>
        <li><a href="#result-4">D Mode</a></li>
        <li><a href="#result-5">Lists</a></li>

        <li><a href="#result-7">History</a></li>
    </ul>
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

            $data_array = explode("\n", $data);
            $data = implode("\n",array_reverse($data_array));

        ?>
        <textarea id="output" class="module-content" style="font-family: courier;"><?=htmlspecialchars($data)?></textarea>
        <input type="hidden" name="type" value="logs">
        </form>



    </div>

    <!-- START Beacon Flood Mode -->

    <div id="result-2" class="module-options">
        <form id="formInject" name="formInject" method="POST" autocomplete="off" action="includes/save.php">
        <input type="submit" value="save">
        <br><br>

        <div class="module-options" style="b-ackground-color:#000; b-order:1px dashed;">
        <table>
            <tr>
                <td><input type="checkbox" name="options[]" value="f" <? if ($mode_b['f'][0] == "1") echo "checked" ?> ></td>
                <td>-f</td>
                <td>
                    <select name="opt_f">
                        <option value="0">-</option>
                        <?
                        $template_path = "$mod_path/includes/templates/";
                        $templates = glob($template_path.'*');
                        //print_r($templates);

                        for ($i = 0; $i < count($templates); $i++) {
                            $filename = str_replace($template_path,"",$templates[$i]);
                            if ($filename == $mode_b['f'][2]) echo "<option selected>"; else echo "<option>";
                            echo "$filename";
                            echo "</option>";
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td><input type="checkbox" name="options[]" value="v" <? if ($mode_b['v'][0] == "1") echo "checked" ?> ></td>
                <td>-v</td>
                <td>
                    <select name="opt_v">
                        <option value="0">-</option>
                        <?
                        $template_path = "$mod_path/includes/templates/";
                        $templates = glob($template_path.'*');
                        //print_r($templates);

                        for ($i = 0; $i < count($templates); $i++) {
                            $filename = str_replace($template_path,"",$templates[$i]);
                            if ($filename == $mode_b['v'][2]) echo "<option selected>"; else echo "<option>";
                            echo "$filename";
                            echo "</option>";
                        }
                        ?>
                    </select>

                </td>
            </tr>
            <tr>
                <td><input type="checkbox" name="options[]" value="n" <? if ($mode_b['n'][0] == "1") echo "checked" ?> ></td>
                <td style="padding-right:10px">-n</td>
                <td width="200px"><input class="ui-widget" type="text" name="opt_n" value="<?=$mode_b['n'][2]?>" style="width:150px"></td>
            </tr>
            <tr>
                <td><input type="checkbox" name="options[]" value="c" <? if ($mode_b['c'][0] == "1") echo "checked" ?> ></td>
                <td>-c</td>
                <td>
                    <select name="opt_c">
                        <?
                        for ($i=1; $i<=14; $i++) {
                            if ($i == $mode_b['c'][2]) echo "<option selected>"; else echo "<option>";
                            echo $i;
                            echo "</option>";
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td><input type="checkbox" name="options[]" value="s" <? if ($mode_b['s'][0] == "1") echo "checked" ?> ></td>
                <td>-s</td>
                <td><input class="ui-widget" type="text" name="opt_s" value="<?=$mode_b['s'][2]?>" style="width:42px"></td>
            </tr>
            <tr>
                <td><input type="checkbox" name="options[]" value="d" <? if ($mode_b['d'][0] == "1") echo "checked" ?> ></td>
                <td>-d</td>
                <td></td>
            </tr>
            <tr>
                <td><input type="checkbox" name="options[]" value="w" <? if ($mode_b['w'][0] == "1") echo "checked" ?> ></td>
                <td>-w</td>
                <td></td>
            </tr>
            <tr>
                <td><input type="checkbox" name="options[]" value="g" <? if ($mode_b['g'][0] == "1") echo "checked" ?> ></td>
                <td>-g</td>
                <td></td>
            </tr>
            <tr>
                <td><input type="checkbox" name="options[]" value="t" <? if ($mode_b['t'][0] == "1") echo "checked" ?> ></td>
                <td>-t</td>
                <td></td>
            </tr>
            <tr>
                <td><input type="checkbox" name="options[]" value="a" <? if ($mode_b['a'][0] == "1") echo "checked" ?> ></td>
                <td>-a</td>
                <td></td>
            </tr>
            <tr>
                <td><input type="checkbox" name="options[]" value="m" <? if ($mode_b['m'][0] == "1") echo "checked" ?> ></td>
                <td>-m</td>
                <td></td>
            </tr>
            <tr>
                <td><input type="checkbox" name="options[]" value="h" <? if ($mode_b['h'][0] == "1") echo "checked" ?> ></td>
                <td>-h</td>
                <td></td>
            </tr>
        </table>
        </div>

        <input type="hidden" name="type" value="mode_b">
        </form>
        <br>
        <?
            $filename = "$mod_path/includes/mode_b.txt";

            $data = open_file($filename);

            //echo str_replace("\n","<br>",htmlspecialchars($data));

            include "includes/mode_b.htm";

        ?>

    </div>

    <!-- START Authentication DoS mode -->

    <div id="result-3" class="module-options">
        <form id="formInject" name="formInject" method="POST" autocomplete="off" action="includes/save.php">
        <input type="submit" value="save">
        <br><br>

        <div class="module-options" style="b-ackground-color:#000; b-order:1px dashed;">
        <table>
            <!-- // OPTION a -->
            <tr>
                <? $opt = "a"; ?>
                <td><input type="checkbox" name="options[]" value="<?=$opt?>" <? if ($mode_a[$opt][0] == "1") echo "checked" ?> ></td>
                <td style="padding-right:10px">-<?=$opt?></td>
                <td width="200px"><input class="ui-widget" type="text" name="opt_<?=$opt?>" value="<?=$mode_a[$opt][2]?>" style="width:150px"></td>
            </tr>
            <!-- // OPTION i -->
            <tr>
                <? $opt = "i"; ?>
                <td><input type="checkbox" name="options[]" value="<?=$opt?>" <? if ($mode_a[$opt][0] == "1") echo "checked" ?> ></td>
                <td style="padding-right:10px">-<?=$opt?></td>
                <td width="200px"><input class="ui-widget" type="text" name="opt_<?=$opt?>" value="<?=$mode_a[$opt][2]?>" style="width:150px"></td>
            </tr>
            <!-- // OPTION c -->
            <tr>
                <? $opt = "c"; ?>
                <td><input type="checkbox" name="options[]" value="<?=$opt?>" <? if ($mode_a[$opt][0] == "1") echo "checked" ?> ></td>
                <td style="padding-right:10px">-<?=$opt?></td>
                <td width="200px"><input class="ui-widget" type="text" name="opt_<?=$opt?>" value="<?=$mode_a[$opt][2]?>" style="width:150px"></td>
            </tr>
            <!-- // OPTION s -->
            <tr>
                <? $opt = "s"; ?>
                <td><input type="checkbox" name="options[]" value="<?=$opt?>" <? if ($mode_a[$opt][0] == "1") echo "checked" ?> ></td>
                <td style="padding-right:10px">-<?=$opt?></td>
                <td width="200px"><input class="ui-widget" type="text" name="opt_<?=$opt?>" value="<?=$mode_a[$opt][2]?>" style="width:42px"></td>
            </tr>
            <!-- // OPTION m -->
            <tr>
                <td><input type="checkbox" name="options[]" value="m" <? if ($mode_a['m'][0] == "1") echo "checked" ?> ></td>
                <td>-m</td>
                <td></td>
            </tr>
        </table>
        </div>

        <input type="hidden" name="type" value="mode_a">
        </form>
        <br>
        <?
            $filename = "$mod_path/includes/mode_a.txt";

            $data = open_file($filename);

            //echo str_replace("\n","<br>",htmlspecialchars($data));

            include "includes/mode_a.htm";
        ?>

    </div>

    <!-- START Deauthentication / Disassociation Amok Mode -->

    <div id="result-4" class="module-options">
        <form id="formInject" name="formInject" method="POST" autocomplete="off" action="includes/save.php">
        <input type="submit" value="save">
        <br><br>

        <div class="module-options" style="b-ackground-color:#000; b-order:1px dashed;">
        <table>
            <!-- // OPTION w -->
            <tr>
                <? $opt = "w"; ?>
                <td><input type="checkbox" name="options[]" value="<?=$opt?>" <? if ($mode_d[$opt][0] == "1") echo "checked" ?> ></td>
                <td>-<?=$opt?></td>
                <td>
                    <select name="opt_<?=$opt?>">
                        <option value="0">-</option>
                        <?
                        $template_path = "$mod_path/includes/templates/";
                        $templates = glob($template_path.'*');

                        for ($i = 0; $i < count($templates); $i++) {
                            $filename = str_replace($template_path,"",$templates[$i]);
                            if ($filename == $mode_d[$opt][2]) echo "<option selected>"; else echo "<option>";
                            echo "$filename";
                            echo "</option>";
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <!-- // OPTION b -->
            <tr>
                <? $opt = "b"; ?>
                <td><input type="checkbox" name="options[]" value="<?=$opt?>" <? if ($mode_d[$opt][0] == "1") echo "checked" ?> ></td>
                <td>-<?=$opt?></td>
                <td>
                    <select name="opt_<?=$opt?>">
                        <option value="0">-</option>
                        <?
                        $template_path = "$mod_path/includes/templates/";
                        $templates = glob($template_path.'*');
                        //print_r($templates);

                        for ($i = 0; $i < count($templates); $i++) {
                            $filename = str_replace($template_path,"",$templates[$i]);
                            if ($filename == $mode_d[$opt][2]) echo "<option selected>"; else echo "<option>";
                            echo "$filename";
                            echo "</option>";
                        }
                        ?>
                    </select>

                </td>
            </tr>
            <!-- // OPTION d -->
            <tr>
                <? $opt = "d"; ?>
                <td><input type="checkbox" name="options[]" value="<?=$opt?>" <? if ($mode_d[$opt][0] == "1") echo "checked" ?> ></td>
                <td style="padding-right:10px">-<?=$opt?></td>
                <td width="200px"><input class="ui-widget" type="text" name="opt_<?=$opt?>" value="<?=$mode_d[$opt][2]?>" style="width:150px"></td>
            </tr>
            <!-- // OPTION c -->
            <tr>
                <? $opt = "c"; ?>
                <td><input type="checkbox" name="options[]" value="<?=$opt?>" <? if ($mode_d[$opt][0] == "1") echo "checked" ?> ></td>
                <td style="padding-right:10px">-<?=$opt?></td>
                <td width="200px"><input class="ui-widget" type="text" name="opt_<?=$opt?>" value="<?=$mode_d[$opt][2]?>" style="width:150px"></td>
            </tr>
            <!-- // OPTION s -->
            <tr>
                <? $opt = "s"; ?>
                <td><input type="checkbox" name="options[]" value="<?=$opt?>" <? if ($mode_d[$opt][0] == "1") echo "checked" ?> ></td>
                <td style="padding-right:10px">-<?=$opt?></td>
                <td width="200px"><input class="ui-widget" type="text" name="opt_<?=$opt?>" value="<?=$mode_d[$opt][2]?>" style="width:42px"></td>
            </tr>
        </table>
        </div>

        <input type="hidden" name="type" value="mode_d">
        </form>
        <br>
        <?
            $filename = "$mod_path/includes/mode_d.txt";

            $data = open_file($filename);

            //echo str_replace("\n","<br>",htmlspecialchars($data));

            include "includes/mode_d.htm";

        ?>

    </div>

    <!-- START LISTS -->

    <div id="result-5" >
        <form id="formTemplates" name="formTemplates" method="POST" autocomplete="off" action="includes/save.php">
        <input type="submit" value="save">

        <br><br>
        <?
        	if ($tempname != "") {
            	$filename = "$mod_path/includes/templates/".$tempname;

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
        <form id="formTempname" name="formTempname" method="GET" autocomplete="off" action="">
    		<input type="hidden" name="tab" value="4" />
    		<select name="tempname" onchange='this.form.submit()'>
        	<option value="0">-</option>
        	<?
        	$template_path = "$mod_path/includes/templates/";
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
    	</form>
        </td>
        <tr>
        <td class="general" style="padding-right:10px">
        	Add/Rename
        </td>
        <td>
        <form id="formTempname" name="formTempname" method="POST" autocomplete="off" action="includes/save.php">
        	<select name="new_rename">
        	<option value="0">- add template -</option>
        	<?
        	$template_path = "$mod_path/includes/templates/";
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
        	$template_path = "$mod_path/includes/templates/";
        	$templates = glob($template_path.'*');

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

    <!-- END LISTS -->

	<!-- HISTORY -->

    <div id="result-7" class="history">
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

</div>

<div id="loading" class="ui-widget" style="width:100%;background-color:#000; padding-top:4px; padding-bottom:4px;color:#FFF">
    Loading...
</div>

<script>
$('#formLogs').submit(function(event) {
    event.preventDefault();
    $.ajax({
        type: 'POST',
        url: 'includes/ajax.php',
        data: $(this).serialize(),
        dataType: 'json',
        success: function (data) {
            $('#output').html('');
            $.each(data, function (index, value) {
                $("#output").append( value ).append("\n");
            });
            $('#loading').hide();
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
        echo "$( '#result' ).tabs({ active: ".($_REQUEST['tab'])." });";
        echo "</script>";
    }
    ?>

</body>
</html>