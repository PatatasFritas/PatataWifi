<?
include_once dirname(__FILE__)."/../config/config.php";
//Set no caching
header("Expires: Mon, 1 Jan 1900 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>
<!DOCTYPE html>
<link href="<?=WEBPATH?>/css/style.css" rel="stylesheet" type="text/css">
<link rel="icon" type="image/x-icon" href="<?=WEBPATH?>/img/favicon.ico"/>

<div class="menu-bc" s-tyle="background-color: #111/*#576971*/;">

<table width="560px">
    <tr>
        <td width="160px" nowrap>
            <div class="m-enu">
                <img src="<?=WEBPATH?>/img/logo.png" width=32 /><img style="padding-left:2px; padding-top:0px;" src="<?=WEBPATH?>/img/logo-fw.png" />
            </div>
        </td>
        <td nowrap>
            <div class="menu" style="padding-left:4px; padding-bottom:0px;" >
                <a href="<?=WEBPATH?>/page_status.php" class="menu">status</a> |
                <a href="<?=WEBPATH?>/page_status_wsdl.php" class="menu">wsdl</a> |
                <a href="<?=WEBPATH?>/page_config.php">config</a> |
                <a href="<?=WEBPATH?>/page_modules.php">modules</a> |
                <a href="<?=WEBPATH?>/page_logs.php">logs</a> |
                <a href="<?=WEBPATH?>/logout.php">logout</a> | <?=$version?>
            </div>
        </td>
    </tr>
</table>
</div>
