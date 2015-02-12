<?
include_once dirname(__FILE__)."/config/config.php";
//Redirect logged users
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: '.WEBPATH.'/page_status.php');
    exit;
}
?>
<link href="<?=WEBPATH?>/css/style.css" rel="stylesheet" type="text/css">
<link rel="icon" type="image/x-icon" href="<?=WEBPATH?>/img/favicon.ico"/>

<div class="menu-bc" s-tyle="background-color: #576971;">
	<table>
		<tr>
			<td>
				<div class="m-enu">
				<img src="<?=WEBPATH?>/img/logo.png" width=32><img style="padding-left:2px; padding-top:0px;" src="<?=WEBPATH?>/img/logo-fw.png">
				</div>
			</td>
			<td>

			</td>
		</tr>
	</table>
</div>

<i-mg src="<?=WEBPATH?>/img/logo.png">

<br><br>

<div align="center">

<div class="rounded-top" align="center"> Login </div>
<div class="rounded-bottom" align="center">

    <form action="login.php" method="post" autocomplete="off">
        <?
        /*
        &nbsp;&nbsp;&nbsp;&nbsp;user: <input name="user" class="input"><br>
        &nbsp;&nbsp;&nbsp;&nbsp;pass: <input name="pass" type="password" class="input"><br>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" value="login" class="input"><br>
        */
        ?>
        <br>
        <table class="general">
            <tr>
                <td>
                    user:
                </td>
                <td>
                    <input name="user" class="input" <? if (isset($_GET['error']) and $_GET['error'] == 1) echo "value='Who are you?...'"?>><br>
                </td>
            <tr>
                <td>
                    pass:
                </td>
                <td>
                    <input name="pass" type="password" class="input"><br>
                </td>
            </tr>
                <td></td>
                <td>
                    <input type="submit" value="login" class="input"><br>
                </td>
            </tr>
        </table>

    </form>

</div>

</div>