<!--------------------------------------------------------------------------------------
    ////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////// dynamic side-boxes //////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////
    -------------------------------------------------------------------------------------->
<div id="backTo" class="dynamic">
    <h3 class="header">Back</h3>
    <div class="content">
        <a href="/militaryAdvisor/combatReports" title="Back to the advisor">
            <img src="http://static.ikariem.org/img/img/action_back.gif" width="160" height="100" />
            <span class="textLabel">&lt;&lt; Back to the advisor</span>
        </a>
    </div>
    <div class="footer"></div>
</div>

<div class="dynamic" id="viewMilitaryImperium">
    <h3 class="header">Military Overview</h3>
    <div class="content">
        <img src="http://static.ikariem.org/img/premium/sideAd_premiumMilitaryAdvisor.jpg" width="203" height="85" />
        <p>When your right flank suddenly moves in from the left, the time has come for a little more structure. This overview is your own private little hill next to the battlefield.</p>
        <div class="centerButton">
            <a href="?view=premiumDetails" class="button">Have a look now!</a>
        </div>
    </div>
    <div class="footer"></div>
</div>
<!--------------------------------------------------------------------------------------
    ////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////// main view /////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////
    -------------------------------------------------------------------------------------->
<div id="mainview">
	<div class="buildingDescription">
		<h1>Military</h1>
		<p></p>
	</div>
	<div class="yui-navset">
		<ul class="yui-nav">
			<li><a href="/militaryAdvisor" title="Troop movements"><em>Troop movements ({$reportView.movements})</em></a></li>
			<li class="selected"><a href="/militaryAdvisor/combatReports" title="Combat reports"><em>Combat reports (1)</em></a></li>
			<li><a href="/militaryAdvisor/combatReportsArchive" title="Archive"><em>Archive</em></a></li>
		</ul>
	</div>
	<div id="troopsReport">
		<div class="contentBox01h">
			<h3 class="header">{$reportView.title} <span class="date">({$reportView.date|date_format:"%d.%m.%Y %H:%M:%S"})</span></h3>
			<div class="content">
				<div class="attacker ">
					<label>Attacker:</label>
					<span><a href="/diplomacyAdvisor/sendIKMessage?receiverId={$reportView.attacker.id}">{$reportView.attacker.name}</a> from <b><a href="/island?cityId={$reportView.attacker.townId}">{$reportView.attacker.townName}</a></b></span>
				</div>
				<div class="defender textgreen">
					<label>Defender:</label>
					<span>{if $reportView.defender.id > 0}<a href="/diplomacyAdvisor/sendIKMessage?receiverId={$reportView.defender.id}">{/if}{$reportView.defender.name}{if $reportView.defender.id > 0}</a>{/if} from <b>{if $reportView.defender.id > 0}<a href="/island?cityId={$reportView.defender.townId}">{/if}{$reportView.defender.townName}{if $reportView.defender.id > 0}</a>{/if}</b></span>
				</div>
				<h5>Outcome of the Battle</h5>
				<table class="overview">
					{foreach from=$reportView.outcome.typesOfArmy item=row name=rowNo}
					<thead>
						<tr>
							<th class="col1 textred">Attacker</th>
							{foreach from=$row item=army name=army}
							<th><div class="army s{$army}"></div></th>
							{if $smarty.foreach.army.last}
							<th colspan="{math equation="7-x" x=$smarty.foreach.army.total}"></th>
							{/if}
							{/foreach}
						</tr>
					</thead>
					<tr class="textred">
						<td class="firstCol">Enemies</td>
						{assign var=attacker value=$reportView.outcome.attacker}
						{foreach from=$attacker[$smarty.foreach.rowNo.index] item=attack name=army}
						<td class="numbers">{$attack}</td>
						{if $smarty.foreach.army.last}
						<td colspan="{math equation="7-x" x=$smarty.foreach.army.total}"></td>
						{/if}
						{/foreach}
					</tr>
					<tr class="textgreen">
						<td colspan="8" class="col1 nobg textgreen">Defender</td>
					</tr>
					<tr class="textgreen">
						<td class="firstCol">Allied Troops</td>
						{assign var=attacker value=$reportView.outcome.defender}
						{foreach from=$attacker[$smarty.foreach.rowNo.index] item=attack name=army}
						<td class="numbers">{$attack}</td>
						{if $smarty.foreach.army.last}
						<td colspan="{math equation="7-x" x=$smarty.foreach.army.total}"></td>
						{/if}
						{/foreach}
					</tr>
					{/foreach}
				</table>
			</div>
			{if $reportView.winner != 0}
			<div class="result">
				<div class="winners">Winners: <br />{if $reportView.winner == 1}{$reportView.defender.name}{else}{$reportView.attacker.name}{/if}</div>
				<div class="losers">Losers: <br />{if $reportView.winner == 1}{$reportView.attacker.name}{else}{$reportView.defender.name}{/if}</div>
				{if $reportView.winner == 2}
				<div>The town {if $reportView.defender.id > 0}<a href="/island?cityId={$reportView.defender.townId}">{/if}{$reportView.defender.townName}{if $reportView.defender.id > 0}</a>{/if} has been pillaged by <a href="/diplomacyAdvisor/sendIKMessage?receiverId={$reportView.attacker.id}">{$reportView.attacker.name}</a>.
				{if count($reportView.loots) > 0}
				The following resources have been stolen:
				<ul class="resources">
					{foreach from=$reportView.loots item=loot}
					<li class="{$loot.css}"><span class="textLabel">{$loot.name}: </span>{$loot.value|number_format}</li>
					{/foreach}
				</ul>
				{/if}</div>{/if}
				<div>{if $reportView.winner == 1}{$reportView.attacker.name}{else}{$reportView.defender.name}{/if}'s army has been completely destroyed.</div>
			</div>
			{/if}
			<p class="link">
				<a href="/militaryAdvisor/detailedReportView?combatRound=1&detailedCombatId={$reportView.id}" class="button">Detailed battle report</a>
				{if $reportView.winner != 0}<a href="?action=Premium&function=archiveReport&actionRequest=fc38d9a7e93d3050f5a6c6492010d12c&type=1&id=153667" class="button">Save in archive&nbsp;&nbsp;(<span class="costAmbrosia" style="padding-top: 5px; padding-bottom: 5px; font-weight: bold; padding-right: 22px; background-image: url(/img/premium/ambrosia_icon.gif); background-repeat: no-repeat; background-position: 100% 50%;">1</span>)</a>{/if}
			</p>
			<div class="footer"></div>
		</div>
	</div>
</div>
