<div id="highscoreswitch" class="dynamic">
    <h3 class="header">Alliance Highscore</h3>
    <div class="content">
        <img width="203" height="85"src="http://static.ikariem.org/img/layout/sieger_2.jpg" />
        <p>Highscore of all alliances</p>
        <div class="centerButton">
            <a href="?view=allyHighscore" class="button">Alliance Highscore</a>

        </div>
    </div>
    <div class="footer"></div>
</div>
<div class="dynamic">
    <h3 class="header">Pillory</h3>
    <div class="content">
        <p>All banned players are listed on the pillory</p>
        <div class="centerButton">

            <a href="pillory.php" target="blank" class="button">Pillory</a>
        </div>
    </div>
    <div class="footer"></div>
</div>

<div id="mainview">
    <h1>Highscore</h1>
    <div class="contentBox01h">
        <h3 class="header"><span class="textLabel">Highscore</span></h3>
        <div class="content">
            <table class="table01">
            <tr>
                <td width="33%"><img src="http://static.ikariem.org/img/layout/sieger_2.jpg" /></td>
                <td width="33%" align="center">
                    <form action="/highscore" method="POST">
                        <div align="center" style="padding: 10px 0;">
                            <select name="highscoreType">
                                <option value="score"{if $highscore.highscoreType == 'score'} selected="selected"{/if}>Total score</option>
                                <option value="building_score_main"{if $highscore.highscoreType == 'building_score_main'} selected="selected"{/if}>Master builders</option>
                                <option value="building_score_secondary"{if $highscore.highscoreType == 'building_score_secondary'} selected="selected"{/if}>Building levels</option>
                                <option value="research_score_main"{if $highscore.highscoreType == 'research_score_main'} selected="selected"{/if}>Scientists</option>
                                <option value="research_score_secondary"{if $highscore.highscoreType == 'research_score_secondary'} selected="selected"{/if}>Levels of research</option>
                                <option value="army_score_main"{if $highscore.highscoreType == 'army_score_main'} selected="selected"{/if}>Military</option>
                                <option value="trader_score_secondary"{if $highscore.highscoreType == 'trader_score_secondary'} selected="selected"{/if}>Gold stock</option>
                                <option value="offense"{if $highscore.highscoreType == 'offense'} selected="selected"{/if}>Offensive points</option>
                                <option value="defense"{if $highscore.highscoreType == 'defense'} selected="selected"{/if}>Defence points</option>
                                <option value="trade"{if $highscore.highscoreType == 'trade'} selected="selected"{/if}>Trade high score</option>
                                <option value="resources"{if $highscore.highscoreType == 'resources'} selected="selected"{/if}>Resources</option>
                                <option value="donations"{if $highscore.highscoreType == 'donations'} selected="selected"{/if}>Donations</option>
                            </select>
                            <select name="offset" id='offset'>
                                <option value="-1"{if $highscore.offset == -1} selected="selected"{/if}>Own position</option>
                                {section name=offset start=0 loop=$highscore.offsetEnd}
                                <option value="{$smarty.section.offset.index}">{$smarty.section.offset.index+1} - {math equation="(x+1)*100" x=$smarty.section.offset.index}</option>
                                {/section}
                            </select>
                            <input class="button" name="sbm" type="submit" value="Submit" />
                        </div>

	           </td>
	           	           <td width="33%" align="center">
	               Player name
	                   <input type="text" name="searchUser" value="{$highscore.searchUser}" /> 
                       <input type="hidden" name="view" value="highscore" /> 
                   </form>
	           </td>
            </tr>
           </table>
           <p style="font-size: 14px; padding: 0; margin: 0 0 15px 10px;">{if !$highscore.searchUser}Place <b>{$highscore.current+1}-{math equation="(x+1)*100" x=$highscore.current}</b>{else}Search results for '<b>{$highscore.searchUser}</b>'{/if} of the {include file="highscore/highscore-types.tpl"} highscore list!</p>
           <table class="table01">
             <tr>
                <th width="15%">Position</th>
                <th width="30%">Player name</th>
                <th width="15%">Alliance</th>
                <th width="20%">Points</th>
                <th width="20%">Actions</th>
            </tr>
			{foreach from=$highscore.scores item=score name=highscore}
			<tr class="{if $score.rank == 1}first {elseif $score.rank == 2}second {elseif $score.rank == 3}third {/if}{if $highscore.usrId == $score.usrId}own {/if}{if $smarty.foreach.highscore.index % 2 == 0}alt{/if}">
				<td class="place">{$score.rank}</td>
				<td class="name">{$score.name}</td>
				<td class="allytag">{$score.alliance}</td>
				<td class="score">{$score.points|number_format}</td>
				<td class="action"><a title="Write message" href="/diplomacyAdvisor/sendIKMessage?receiverId={$score.usrId}"><img src="http://static.ikariem.org/img/interface/icon_message_write.gif" alt="Write message" /></a></td>
			</tr>
			{/foreach}
           </table>
        </div>
        
        <div class="contentBox01h">
        	<div class="content">
        		<p></p>
        	</div>
        </div>
        <div class="footer"></div>
    </div>
</div>