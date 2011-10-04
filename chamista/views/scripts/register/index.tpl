<div id="register">
<img class="bild1" src="http://static.ikariem.org/img/bild1.jpg" width="173" height="85" />
<img class="bild2" src="http://static.ikariem.org/img/bild2.jpg" width="173" height="85" />
<h1>Welcome to the world of Ikariem</h1>
{if isset($messages)}
	<div>
	{foreach from=$messages item=message}
		<div class="warning">{$message}</div>
	{/foreach}
	</div>
{else}
	<p class="desc">In order to play along, you only have to enter a nickname, a password and an E-mail address. You also have to check the box in order to accept the terms and conditions.</p>
{/if}
<form name="registerForm" action="#" onsubmit="changeAction('register');" method="post">
<p class="desc">


<table cellpadding="3" cellspacing="0" id="logindata">
	<tr>
		<td><label for="welt" class="labelwelt">World</label></td>
		<td><select id="universe" class="uni" size="1" onchange="getServerNotice(this.value, 'infotext');">
						{foreach from=$servers item=server key=url}
                        <option value="{$url}">{$server}</option>
                        {/foreach}
		</select></td>
	</tr>
	<tr>
		<td><label for="usr_nick" class="labellogin">Your Nickname</label></td>
		<td><input type='text' name='usr_nick' class="startinput" size='30'	onfocus="showInfo('201');" value="{if isset($usr_nick)}{$usr_nick}{/if}" /></td>
	</tr>
	<tr>
		<td><label for="usr_password" class="labellogin">Password</label></td>

		<td><input type='password' name='usr_password' class="startinput" size='30' onfocus="showInfo('205');" /></td>
	</tr>
	<tr>
		<td><label for="login" class="labellogin">E-mail</label></td>
		<td><input type='text' name='usr_email' class="startinput" size='30' onfocus="showInfo('202');" value="{if isset($usr_email)}{$usr_email}{/if}" /></td>
	</tr>
	<tr>
		<td></td>
		<td><input type="checkbox" name="agb" onfocus="javascript:showInfo('204');" value="1" />I accept the <a style="color: rgb(223, 88, 67);" target="_blank" href="#">T&C</a> and the <a style="color: rgb(223, 88, 67);" target="_blank" href="#">Privacy Policy</a>.</td>
	</tr>
</table>
</p>
<div id="infotext"></div>
<br />
<input type="submit" class="button" value="Register Now!" /></form>
</div>