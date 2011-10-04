<!---------------------------------------------------------------------------------------
////////////////////////////////////////////////////////////////////////////////////
///////////////////////////// dynamic side-boxes. //////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////// --> 
 
<div class="dynamic" id="reportInboxLeft"> 
    <h3 class="header">Invite friends</h3> 
    <div class="content"> 
        <img width="167" height="110" src="http://static.ikariem.org/img/options/invitefriends.jpg" /> 
        <p>You want to invite friends to join Ikariam? You want them to have a town near yours? Generate a link that you can pass on to your friends here.</p> 
        <div class="centerButton"> 
            <a href="?view=optionsInviteFriends" class="button">Invite friends</a> 
        </div> 
    </div> 
    <div class="footer"></div> 
</div> 
    
<!---------------------------------------------------------------------------------------
////////////////////////////////////////////////////////////////////////////////////
///////////////// the main view. take care that it stretches. //////////////////////
//////////////////////////////////////////////////////////////////////////////////// --> 
 
<div id="mainview"> 
 
    <div class="buildingDescription"> 
        <h1>Settings</h1> 
        <p>Here you can change your player`s name, your password and your e-mail address. Remember that you can only change your e-mail address once a week and that you can only use numbers, letters and spaces for your password.</p> 
    </div><!-- ende .buildingDescription --> 
 
    
    {if isset($messages)}
    <div class="contentBox01h">
    	<h3 class="header"><span class="textLabel">Error message(s)</span></h3>
    	<div class="content">
    		<ul class="errors">
    		{foreach from=$messages item=message}
    			<li>{$message}</li>
    		{/foreach}
    		</ul>
    	</div>
    </div>
    {/if}
 
    <div class="contentBox01h"> 
        <h3 class="header"><span class="textLabel">Settings</span></h3> 
        <div class="content"> 
            <form  action="/options/changeAvatarValues" method="POST"> 
                <div id="options_userData"> 
                    <table cellpadding="0" cellspacing="0"> 
                        <tr> 
                            <th>Change player`s name</th> 
                            <td>{$usr_nick}</td> 
                        </tr> 
 
                    </table> 
                </div> 
 
                <div id="options_changePass"> 
                    <h3>Change password</h3> 
                    <table cellpadding="0" cellspacing="0"> 
                        <tr> 
                            <th>Old password</th> 
                            <td><input type="password" class="textfield" name="oldPassword" size="20"/></td> 
                        </tr> 
                        <tr> 
                            <th>New password</th> 
                            <td><input type="password" class="textfield" name="newPassword" size="20"/></td> 
                        </tr> 
                        <tr> 
                            <th>Confirm new password</th> 
                            <td><input type="password" class="textfield" name="newPasswordConfirm" size="20"/></td> 
                        </tr> 
                    </table> 
                </div> 
 
                <div> 
                    <h3>Other</h3> 
                    <table cellpadding="0" cellspacing="0"> 
                        <tr> 
                            <th>Display details in town selection</th> 
                            <td> 
                                {html_options name=citySelectOptions options=$citySelectOptions selected=$usr_citySelectOptions} 
                            </td> 
                        </tr> 
                                            </table> 
                </div> 
 
                <div id="options_debug"> 
                    <h3>Debugdata</h3> 
                    <table cellpadding="0" cellspacing="0"> 
                        <tr> 
                            <th>Player-ID:</th> 
                            <td> {$usr_id}</td> 
                        </tr> 
                        <tr> 
                            <th>Current City-ID: </th> 
                            <td>{$town_id}</td> 
                        </tr> 
                    </table> 
                </div> 
 
 
                <div class="centerButton"> 
                    <input type="submit" class="button" value="Save settings!"> 
                </div> 
            </form> 
        </div> 
        <div class="footer"></div> 
    </div> 
 
 
 
    <div class="contentBox01h"> 
        <h3 class="header"><span class="textLabel">Change e-mail-address</span></h3> 
        <div class="content"> 
            <form  action="index.php" method="POST"> 
                <input type=hidden name=action value="Options"> 
                <input type=hidden name=function value="changeEmail"> 
                <input type="hidden" name="actionRequest" value="74dee402933f30c49a649d0af19acbf3" /> 
                <table cellpadding="0" cellspacing="0"> 
 
                    <tr> 
                        <th>Change e-mail-address</th> 
                        <td> 
                                                        <input class="textfield" type="text" name="email" size="30" value="{$usr_email}" /> 
                                                
                        </td> 
                    </tr> 
 
                    <tr> 
                        <th>Confirm the change of your e-mail address with your password</th> 
                        <td><input type="password" class="textfield" name="emailPassword" size="20"/></td> 
                    </tr> 
                </table> 
                <div class="centerButton"> 
                    <input type="submit" class="button" value="Change e-mail-address"> 
                </div> 
            </form> 
        </div> 
        <div class="footer"></div> 
    </div> 
 
            
    <div class="contentBox01h" id="vacationMode"> 
        <h3 class="header"><span class="textLabel">Activate vacation mode</span></h3> 
        <div class="content"> 
            <p>You can activate vacation mode here. What this means is that your game account will not be deleted if you are inactive for too long and you cities will not be attacked during that time Your workers and scientists will also be on holiday and will not be working. So that vacation mode is not taken advantage of, your holiday has to last for a minimum of 48 hours. You will not be able to play Ikariam during this time. After those two days, your holiday will automatically come to an end the next time you log on.</p> 
            <p class="warningFont">Caution! Fleets and armies that are outside your cities will be dispersed and will return to their home towns if you activate vacation mode! Goods on board will all be lost!</p> 
            <div class="centerButton"> 
                <a class="button" href="/options/umodConfirm">Activate vacation mode</a> 
            </div> 
        </div> 
        <div class="footer"></div> 
    </div> 
 
 
    <div class="contentBox01h" id="deletionMode"> 
        <h3 class="header"><span class="textLabel">Delete Player</span></h3> 
        <div class="content"> 
            <p>If you no longer want to play can delete your account here. It will be removed from the game after seven days.</p> 
            <br /> 
            <div class="centerButton"> 
                <a class="button" href="?view=options_deletion_confirm">Delete player irretrievably now!</a> 
            </div> 
            <br /> 
        </div> 
        <div class="footer"></div> 
    </div> 
 
 
 
</div> 