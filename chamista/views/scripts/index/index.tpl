<img class="bild1" src="http://static.ikariem.org/img/bild1.jpg" width="173" height="85" />
<img class="bild2" src="http://static.ikariem.org/img/bild2.jpg" width="173" height="85" />			
<h1>Live the ancient world!</h1>

<p class="desc">The sound of the sea, a white sandy beach and sun! 
On a small island somewhere in the Mediterranean, an ancient civilization arises. 
Under your leadership an era of wealth and discovery begins. 
Welcome to Ikariem.</p>
<div class="joinbutton"><a href="/register" title="Come on! Click me!">Play now for free!</a></div>
{if isset($messages)}
<div class="warning">{$messages[0]}</div>
{/if}
<form id="loginForm" name="loginForm" action="#" onsubmit="changeAction('login');" method="post">
	<div id="formz">
		<table cellpadding="0" cellspacing="0" id="logindata">
			<tr>
				<td><label for="welt" class="labelwelt">World</label></td>
				<td><label for="login" class="labellogin">Nickname</label></td>
				<td><label for="pwd" class="labelpwd">Password</label></td>
				<td></td>
			</tr>
			<tr>
				<td>
					<select id="universe" class="uni" size="1">
						{foreach from=$servers item=server key=url}
						<option value="{$url}">{$server}</option>
						{/foreach}
					</select>
				</td>
				<td><input id="login" name="usr_nick" type="text" class="login" /></td>
				<td><input id="pwd"  name="usr_password" type="password" class="pass" /></td>
				<td><input type="submit" class="button" value="Login" /></td>
		   </tr>
		   <tr>
				<td colspan="3" class="forgotpwd"><a href="lostpwd.php" title="Here you can request a new password">Forgot your password?</a></td>
				<td style="font-size:10px; text-align:left; padding:4px 0px 0px 16px;">
					Logging in constitutes acceptance of the <a style="color:rgb(223, 88, 67);" target="_blank" href="#">T&C</a>.
				</td>
		   </tr>

	   </table>
	</div>
</form>