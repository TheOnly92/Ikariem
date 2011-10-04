<!--------------------------------------------------------------------------------------
    ////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////// dynamic side-boxes //////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////
    -------------------------------------------------------------------------------------->

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
	</div>
	<div class="yui-navset">
		<ul class="yui-nav">
			<li><a href="/militaryAdvisor" title="Troop movements"><em>Troop movements ({$militaryAdvisor.movements})</em></a></li>
			<li class="selected"><a href="/militaryAdvisor/combatReports" title="Combat reports"><em>Combat reports ({$militaryAdvisor.combatReports})</em></a></li>
			<li><a href="/militaryAdvisor/combatReportsArchive" title="Archive"><em>Archive</em></a></li>
		</ul>
	</div>
	<div id="troopsOverview">
		<div class="contentBox01h">
		<h3 class="header"><span class="textLabel">Combat Reports</span></h3>
		<div class="content">
			<form method="post" id="finishedReports" action="/index.php?action=MilitaryAction&function=modifyCombatReport&actionRequest=fc38d9a7e93d3050f5a6c6492010d12c">
			<input type='hidden' name='start' value='0'>
			<table cellpadding="0" cellspacing="0" class="operations">
				{foreach from=$militaryAdvisor.reports item=report}
				<tr>
					<td class="empty"></td>
					<td><input type="checkbox" name="combatId[{$report.id}]" value="1" /></td>
					<td class="date">{$report.date|date_format:"%d.%m.%Y %H:%M:%S"}</td>
					<td class="subject{if $report.won} won{elseif $report.lost} lost{/if}{if $report.new} new{/if}">
						<a href="/militaryAdvisor/reportView?combatId={$report.id}" title="{$report.title}">{$report.title}</a>
					</td>
					<td class="empty"></td>
				</tr>
				{/foreach}
				<!-- <tr>
				<td class="empty"></td>
				<td><input type="checkbox" name="combatId[153667]" value="1" /></td>
				<td class="date">
				05.08.2010 21:12:39
				</td>
				<td class="subject lost">
				<a href="/index.php?view=militaryAdvisorReportView&amp;combatId=153667" title="Battle for Mizar">Battle for Mizar</a>
				</td>
				<td class="empty"></td>
				</tr>
				<tr>
				<td class="empty"></td>
				<td><input type="checkbox" name="combatId[153641]" value="1" /></td>
				<td class="date">
				05.08.2010 20:30:23
				</td>
				<td class="subject lost new">
				<a href="/index.php?view=militaryAdvisorReportView&amp;combatId=153641" title="Sea battle near Mizar">Sea battle near Mizar</a>
				</td>
				<td class="empty"></td>
				</tr>
				<tr>
				<td class="empty"></td>
				<td><input type="checkbox" name="combatId[152797]" value="1" /></td>
				<td class="date">05.08.2010 14:43:35</td>
				<td class="subject won">
				<a href="/index.php?view=militaryAdvisorReportView&amp;combatId=152797" title="Battle for Polis">Battle for Polis</a>
				</td>
				<td class="empty"></td>
				</tr> -->
			</table>
			</form>
			<script type="text/javascript">{literal}
			function markAll(command) {
				var allInputs = document.getElementById('finishedReports').getElementsByTagName('input');
				for (var i=0; i<allInputs.length; i++) {
					if (allInputs[i].getAttribute('type') == "checkbox") {
						if (command == 'checked') {
							allInputs[i].checked = true;
						}
						if (command == 'reverse') {
							if (allInputs[i].checked == true) {
								allInputs[i].checked = false;
							} else {
								allInputs[i].checked = true;
							}
						}
					}
				}
			}
			{/literal}</script>
			</div>
			<div class="footer"></div>
		</div>
	</div>
</div>
