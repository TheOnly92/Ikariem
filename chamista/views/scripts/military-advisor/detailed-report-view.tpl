<!--[if IE 6]-->
<style>{literal}
<!--
/*
#back {
    padding: 10px 10px 10px 0px;
    position: absolute;
    width: 200px;
    right: 55px;
    top: 470px; 
    text-align:right;
    
    display:block;
}


#rounds {
    padding: 0;
    color: #612d04;
    position: absolute;
    width: 400px;
    right: 55px;
    top: 43px;
    text-align:right;
    vertical-align: center;
}*/

#events {
    background-image: url('http://static.ikariem.org/img/combatreport/resultbg.gif');
    background-repeat: repeat-x;    
    background-color: #f9e3aa;
    width: 905px;
    border: 2px solid #fedc9c;
    padding: 0;
    margin: 20px 0;
}
#events div.norm {
    padding: 10px;
}
#events div.alt {
    background-color: #fedc9c;
    
}
#events span {
    width: 720px;
    display: block;
    padding: 0 0 0 150px;
    margin: 0;
    
}
#conExtraDiv2 { 
    position:absolute;
    z-index:3000;
    bottom:62px;    right:29px;
    width:9px; height:12px;
    background-image: url(http://static.ikariem.org/img/layout/corner_bottomright.gif);
    background-position: bottom right;
    background-repeat: no-repeat;   
}
#conExtraDiv3 { 
    position:absolute;
    z-index:3000;
    top:147px;  right:29px;
    width:5px; height:10px;
    background-image: url(http://static.ikariem.org/img/layout/corner_topright.gif);
    background-position: top right;
    background-repeat: no-repeat;
}

-->
{/literal}</style>

<!--[endif]-->

<!--[if IE 6]>
<style>{literal}

#militaryAdvisorDetailedReportView #rounds{
    top:42px;
}

#rounds ul {
    margin-right: 22px;
    margin-top: 0px;
}

{/literal}</style>
<![endif]-->


<!--------------------------------------------------------------------------------------
    ////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////// dynamic side-boxes //////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////
    -------------------------------------------------------------------------------------->
<!--------------------------------------------------------------------------------------
    ////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////// main view /////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////
    -------------------------------------------------------------------------------------->
    
<div id="mainview">
    <div class="buildingDescription">
        <h1>{$militaryAdvisor.reportTitle}</h1>
    </div>
    <div id="attacker">
        Attacker:<br>
            {$militaryAdvisor.attackerNick}</div>
        <div id="rounds">
            <ul>
            	{if $militaryAdvisor.totalRounds != $militaryAdvisor.round}
            	{if $militaryAdvisor.totalRounds - $militaryAdvisor.round > 1}
            	<li class="arrow">
            		<a href="/militaryAdvisor/detailedReportView?combatRound={$militaryAdvisor.totalRounds}&detailedCombatId={$militaryAdvisor.combatId}">
            			<img src='http://static.ikariem.org/img/combatreport/arrow_last.gif' />
            		</a>
            	</li>
            	{/if}
                <li class='arrow'>
                    <a href='/militaryAdvisor/detailedReportView?combatRound={$militaryAdvisor.round+1}&detailedCombatId={$militaryAdvisor.combatId}'>
                        <img src='http://static.ikariem.org/img/combatreport/arrow_fore.gif'/>
                    </a>
                </li>
                {/if}
                {if $militaryAdvisor.round > 1}
                <li class="arrow">
                	<a href="/militaryAdvisor/detailedReportView?combatRound={$militaryAdvisor.round-1}&detailedCombatId={$militaryAdvisor.combatId}">
                		<img src='http://static.ikariem.org/img/combatreport/arrow_back.gif' />
                	</a>
                </li>
                {if $militaryAdvisor.round - 1 > 1}
                <li class="arrow">
                	<a href="/militaryAdvisor/detailedReportView?combatRound=1&detailedCombatId={$militaryAdvisor.combatId}">
                		<img src='http://static.ikariem.org/img/combatreport/arrow_first.gif' />
                	</a>
                </li>
                {/if}
                {/if}
                <li class="roundTime">({$militaryAdvisor.time|date_format:"%d.%m.%y %T"})</li>
                <li class="roundNo">Round {$militaryAdvisor.round} / {$militaryAdvisor.totalRounds}</li>
            </ul>
        </div>
        <div id="battlefield" class="battlefield_barb_A_D">
            <div id="resAttacker">
                <div class="nav">
                    <ul>
                        <li><a href="javascript:;" id="attBack">&lt;&lt;</a></li>
                        <li class="res">Reserve</li>
                        <li><a href="javascript:;" id="attFore">&gt;&gt;</a></li>
                    </ul>
                </div>
                <div class="units "></div>
            </div>
            <div id="fieldAttacker">
                <ul class="special">
                    <li>
                        <div class="empty"></div>
                    </li>
                    <li>
                        <div class="empty"></div>
                    </li>
                </ul>
                <ul class="airfighter">
                    <li>
                        <div class="empty"></div>
                    </li>
                </ul>
                <ul class="air">
                    <li>
                        <div class="empty"></div>
                    </li>
                </ul>
                <ul class="flankLeft">
                    <li style="width:37px"></li>
                    <li style="width:37px"></li>
                </ul>
                <ul class="flankRight">
                    <li style="width:37px"></li>
                    <li style="width:37px"></li>
                </ul>
                <ul class="main">
                	{foreach from=$militaryAdvisor.fieldAttacker.main item=main}
                	{if $main === false}
                	<li style="width:37px"></li>
                	{elseif $main == 0}
                	<li>
                		<div class="empty"></div>
                	</li>
                	{else}
                	<li>
                		<div id="slot{$main.id}" class="s{$main.class}">
                			<div class="hitpoints"></div>
                			<div class="loss" style="height:{$main.loss}px"></div>
                		</div>
                		<div class="number">{$main.number}</div>
                	</li>
                	{/if}
                	{/foreach}
                </ul>
                <ul class="longRange">
                	{foreach from=$militaryAdvisor.fieldAttacker.longRange item=main}
                	{if $main === false}
                	<li style="width:37px"></li>
                	{elseif $main == 0}
                	<li>
                		<div class="empty"></div>
                	</li>
                	{else}
                	<li>
                		<div id="slot{$main.id}" class="s{$main.class}">
                			<div class="hitpoints"></div>
                			<div class="loss" style="height:{$main.loss}px"></div>
                		</div>
                		<div class="number">{$main.number}</div>
                	</li>
                	{/if}
                	{/foreach}
                </ul>
                <ul class="artillery">
                    <li style="width:37px"></li>
                    <li>
                        <!-- <div id="slot11_24_0" class="s307">
                            <div class="hitpoints"></div>
                            <div class="loss" style="height:0px"></div>
                        </div>
                        <div class="number">6 (-0)</div> --><div class="empty"></div></li>
                    <li style="width:37px"></li>
                </ul>
            </div>
            <div id="resDefender">
                <div class="nav">
                    <ul>
                        <li><a href="javascript:;" id="defBack">&lt;&lt;</a></li>
                        <li class="res">Reserve</li>
                        <li><a href="javascript:;" id="defFore">&gt;&gt;</a></li>
                    </ul>
                </div>
                <div class="units ">
                    <ul id="defenderPage1">
                       <!--  <li>
                            <div class="s316d"></div>74</li> -->
                    </ul>
                </div>
            </div>
            <div id="fieldDefender">
                <ul class="artillery">
                    <li style="width:37px"></li>
                    <li>
                        <div class="empty"></div>
                    </li>
                    <li style="width:37px"></li>
                </ul>
                <ul class="longRange">
                    <li style="width:37px"></li>
                    <li style="width:37px"></li>
                    <li>
                        <div class="empty"></div>
                    </li>
                    <li>
                        <div class="empty"></div>
                    </li>
                    <li>
                        <div class="empty"></div>
                    </li>
                    <li style="width:37px"></li>
                    <li style="width:37px"></li>
                </ul>
                <ul class="main">
                    {foreach from=$militaryAdvisor.fieldDefender.main item=main}
                	{if $main === false}
                	<li style="width:37px"></li>
                	{elseif $main == 0}
                	<li>
                		<div class="empty"></div>
                	</li>
                	{else}
                	<li>
                		<div id="slot{$main.id}" class="s{$main.class}">
                			<div class="hitpoints"></div>
                			<div class="loss" style="height:{$main.loss}px"></div>
                		</div>
                		<div class="number">{$main.number}</div>
                	</li>
                	{/if}
                	{/foreach}
                </ul>
                <ul class="flankRight">
                    <li style="width:37px"></li>
                    <li style="width:37px"></li>
                </ul>
                <ul class="flankLeft">
                    <li style="width:37px"></li>
                    <li style="width:37px"></li>
                </ul>
                <ul class="air">
                    <li>
                        <div class="empty"></div>
                    </li>
                </ul>
                <ul class="airfighter">
                    <li>
                        <div class="empty"></div>
                    </li>
                </ul>
                <ul class="special">
                    <li>
                        <div class="empty"></div>
                    </li>
                    <li>
                        <div class="empty"></div>
                    </li>
                </ul>
            </div>
        </div>
        <div id="defender">Defender:<br>{$militaryAdvisor.defenderNick}</div>
            <div id="back"><a href="/militaryAdvisor/reportView?combatId={$militaryAdvisor.combatId}" class="button">Back</a></div>
            <div id="morale">
                <table class="table01">
                    <tr>
                        <th class="atter">Attacker</th>
                        <th>Units</th>
                        <th colspan="2">Morale</th>
                    </tr>
                    <tr>
                        <td class="avatarName">Undirrast</td>
                        <td class="militarySize">114 (-0)</td>
                        <td class="militaryMorale" id="slotmorale3512">
                            <div class="progressBar">
                                <div class="bar" style="background-color:#E25353;width:100%;"></div>
                                <div class="bar" style="margin-top:-10px;width:90%;"></div>
                            </div>
                        </td>
                        <td class="morale">90 %
                        </td>
                    </tr>
                </table>
                <table class="table01">
                    <tr>
                        <th class="deffer">Defender</th>
                        <th>Units</th>
                        <th colspan="2">Morale</th>
                    </tr>
                    <tr>
                        <td class="avatarName">Barbarians</td>
                        <td class="militarySize">74 (-0)</td>
                        <td class="militaryMorale" id="slotmorale-1">
                            <div class="progressBar">
                                <div class="bar" style="background-color:#E25353;width:100%;"></div>
                                <div class="bar" style="margin-top:-10px;width:100%;"></div>
                            </div>
                        </td>
                        <td class="morale">100 %
                        </td>
                    </tr>
                </table>
            </div>
            {if count($militaryAdvisor.events) > 0}
            <div id="events">
                <h2>Current events</h2>
                {foreach from=$militaryAdvisor.events item=event name=event}
                <div class="norm{if $smarty.foreach.event.index % 2 != 0} alt{/if}">
                	<label>{$militaryAdvisor.time|date_format:"%d.%m.%y %T"}</label>
                	<span>
                		{if $event.type == 1}
                		{$event.person} has joined the battle on the defender's side with the following units:
                		<ul class="unitlist">
                		{foreach from=$event.units item=unit}
                			<li class="s{$unit.class}d">{$unit.amount}</li>
                		{/foreach}
                			<div style="clear:both"></div>
                		</ul>
                		{elseif $event.type == 2}
                		{$event.person} is joining the battle on the attacker's side with the following units:
                		<ul class="unitlist">
                		{foreach from=$event.units item=unit}
                			<li class="s{$unit.class}d">{$unit.amount}</li>
                		{/foreach}
                			<div style="clear:both"></div>
                		</ul>
                		{/if}
                	</span>
                </div>
                {/foreach}
            </div>
            {/if}

    <script language="javascript">{literal}
    <!--
    
    Event.onDOMReady(function() {
        var node = document.getElementById('militaryAdvisorDetailedReportView');    
        infoBox = document.createElement('div');
        infoBox.setAttribute('id','infoBox');
        node.appendChild(infoBox);
    });
    
    function registerMouseOver(idName, html, isMoraleDisplay, isLTR) {
        var slotId = "slot" + idName;
        var marginX = 36;
        var marginY = 0;
        if (isMoraleDisplay == true) {
            if (isLTR == true) {
                marginX = -340;
                marginY = 28;
            } else {
                marginX = -83;
                marginY = 28;
            }
        }
        Dom.get(slotId).onmouseover = function() {
            infoBox.innerHTML = html;
            infoBox.style.display = "block";
            var xy = YAHOO.util.Dom.getXY(slotId);
            xy[0] += marginX;
            xy[1] += marginY;
              // alert(infoBox.clientHeight);
            if(xy[1] + infoBox.clientHeight > Dom.getViewportHeight() + Dom.getDocumentScrollTop()) {
             
                xy[1] = Math.max(180, xy[1] - infoBox.clientHeight*(1.5), Dom.getDocumentScrollTop());
                infoBox.zIndex = 1000000;
            }
            YAHOO.util.Dom.setXY(infoBox, xy);
        };
        
        Dom.get(slotId).onmouseout = function() {
            infoBox.style.display = "none";
        };
    }
    
    var maxAttackerPages = 1;
var maxDefenderPages = 1;
{/literal}
{foreach from=$militaryAdvisor.mouseOver item=tips key=slot}
registerMouseOver("{$slot}", "<table><tr><td><h2><span>{$tips.name}</span></h2></td></tr><tr><td><p>Hit points: {$tips.hp}</p></td></tr></table>");
{/foreach}
{literal}
/*registerMouseOver("11_21_1", "<table><tr><td><h2><span>Hoplite</span></h2></td></tr><tr><td><p>Hit points: 99%</p></td></tr></table>");
registerMouseOver("11_21_0", "<table><tr><td><h2><span>Hoplite</span></h2></td></tr><tr><td><p>Hit points: 99%</p></td></tr></table>");
registerMouseOver("11_23_1", "<table><tr><td><h2><span>Archer</span></h2></td></tr><tr><td><p>Munition: 100%</p></td></tr><tr><td><p>Hit points: 100%</p></td></tr></table>");
registerMouseOver("11_23_0", "<table><tr><td><h2><span>Archer</span></h2></td></tr><tr><td><p>Munition: 100%</p></td></tr><tr><td><p>Hit points: 100%</p></td></tr></table>");
registerMouseOver("11_24_0", "<table><tr><td><h2><span>Ram</span></h2></td></tr><tr><td><p>Hit points: 100%</p></td></tr></table>");
registerMouseOver("12_21_1", "<table><tr><td><h2><span>Wall</span></h2></td></tr><tr><td><p>Wall level: 2</p></td></tr><tr><td><p>Hit points: 0%</p></td></tr><tr><td><p>Losses: 1</p></td></tr></table>");
registerMouseOver("12_21_0", "<table><tr><td><h2><span>Wall</span></h2></td></tr><tr><td><p>Wall level: 2</p></td></tr><tr><td><p>Hit points: 0%</p></td></tr><tr><td><p>Losses: 1</p></td></tr></table>");
registerMouseOver("12_21_2", "<table><tr><td><h2><span>Wall</span></h2></td></tr><tr><td><p>Wall level: 2</p></td></tr><tr><td><p>Hit points: 52%</p></td></tr></table>");
registerMouseOver("morale3512", "<table><tr><td><h2><span>Morale changes</span></h2></td></tr><tr><td style='padding:5px;'>Both sides are exhausted. Their morale has been reduced by 10%.</td></tr></table>",true,true);*/
    
    var currentAttackerPage = 1;
    var currentDefenderPage = 1;
    
    Dom.get('attBack').onclick = function() {
        if (currentAttackerPage > 1) {
            Dom.get('attackerPage' + currentAttackerPage).style.display = 'none';
            currentAttackerPage--;
            Dom.get('attackerPage' + currentAttackerPage).style.display = 'block';
        }
    }
    
    Dom.get('attFore').onclick = function() {
        if (currentAttackerPage < maxAttackerPages) {
            Dom.get('attackerPage' + currentAttackerPage).style.display = 'none';
            currentAttackerPage++;
            Dom.get('attackerPage' + currentAttackerPage).style.display = 'block';
        }
    }
    
    Dom.get('defBack').onclick = function() {
        if (currentDefenderPage > 1) {
            Dom.get('defenderPage' + currentDefenderPage).style.display = 'none';
            currentDefenderPage--;
            Dom.get('defenderPage' + currentDefenderPage).style.display = 'block';
        }
    }
    
    Dom.get('defFore').onclick = function() {
        if (currentDefenderPage < maxDefenderPages) {
            Dom.get('defenderPage' + currentDefenderPage).style.display = 'none';
            currentDefenderPage++;
            Dom.get('defenderPage' + currentDefenderPage).style.display = 'block';
        }
    }
    
    if (maxAttackerPages < 2) {
        Dom.get('attBack').style.visibility = 'hidden';
        Dom.get('attFore').style.visibility = 'hidden';
    }
    
    if (maxDefenderPages < 2) {
        Dom.get('defBack').style.visibility = 'hidden';
        Dom.get('defFore').style.visibility = 'hidden';
    }
    
    -->
{/literal}</script>
    
</div>