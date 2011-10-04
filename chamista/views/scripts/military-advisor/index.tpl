<!--------------------------------------------------------------------------------------
    ////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////// dynamic side-boxes //////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////
    -------------------------------------------------------------------------------------->
<div class="dynamic" id="viewMilitaryImperium">
	<h3 class="header">Military Overview</h3>
	<div class="content">
		<img src="http://static.ikariem.org/img/premium/sideAd_premiumMilitaryAdvisor.jpg" width="203" height="85" />
		<p>When your right flank suddenly moves in from the left, the time has come for a little more structure. This overview is your own personal command centre!</p>
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
			<li class="selected"><a href="/militaryAdvisor" title="Troop movements"><em>Troop movements ({$militaryAdvisor.movements})</em></a></li>
			<li><a href="/militaryAdvisor/combatReports" title="Combat reports"><em>Combat reports ({$militaryAdvisor.combatReports})</em></a></li>
			<li><a href="/militaryAdvisor/combatReportsArchive" title="Archive"><em>Archive</em></a></li>
		</ul>
	</div>
	
	<div id="combatsInProgress" class="contentBox">
		<h3 class="header">Current events</h3>
		<div class="content">
			<ul>
			{foreach from=$militaryAdvisor.events item=event name=event}
				<li><!-- per location -->
					<div class="locationheader" style="z-index: 200">
						<h4>{if $event.id > 0}<a href="/island?cityId={$event.id}">{/if}{$event.name}{if $event.id > 0}</a>{/if} [{$event.coords.x}:{$event.coords.y}]</h4>
					</div>
					
					<div class="events" style="padding-bottom: 7px; z-index: 201">
						<div id="combat{$smarty.foreach.event.index}events">
							<table width="100%" cellpadding="0" cellspacing="0" class="locationEvents">
								<tr style="font-weight: bold; background-color: #f6e4ba; background-image: url('http://static.ikariem.org/img/interface/x674_tablehead_shadow.gif'); background-repeat: repeat-x;">
									<td style="background-repeat: repeat-x; width: 35px; padding:0px"></td>
									<td style="background-repeat: repeat-x; width: 50px">Arrival time</td>
									<td style="background-repeat: repeat-x; width: 150px;">Units</td>
									<td style="background-repeat: repeat-x;">Origin</td>
									<td colspan="3" style="background-repeat: repeat-x; width: 60px; text-align: center;">Mission</td>
									<td style="background-repeat: repeat-x; width: 42px;">Action</td>
								</tr>
								<!-- repeat... -->
								{foreach from=$event.battles item=battle name=battle}
								<tr class="{if $smarty.foreach.battle.index % 2 != 0}alt {/if}own own">
									<td><img src="http://static.ikariem.org/img/advisors/military/bang_soldier.gif" height="27" /></td>
									<td style="color: #666;" id="fleetRow{$battle.id}" title="Time of arrival"></td>
									<td title="Number of" style="cursor: pointer" onMouseOut="this.firstChild.nextSibling.style.display = 'none'" onMouseOver="this.firstChild.nextSibling.style.display = 'block'">
										{$battle.ships} Ships{if $battle.units > 0} / {$battle.units|number_format} Units{/if}
										<div class="tooltip2" style="z-index:2000">
											<h5>Fleet / Units / Cargo</h5>
											<div class="unitBox" title="Cargo Ship">
												<div class="icon"><img src="http://static.ikariem.org/img/characters/fleet/40x40/ship_transport_r_40x40.gif" /></div>
												<div class="count">{$battle.ships|number_format}</div>
											</div>
											{foreach from=$battle.armies item=army}
											<div class="unitBox" title="{$army.name}">
												<div class="icon"><img src="http://static.ikariem.org/img/characters/military/x40_y40/y40_{$army.css}_faceright.gif" /></div>
												<div class="count">{$army.total|number_format}</div>
											</div>
											{/foreach}
										</div>
									</td>
									<td title="Origin"><a href="/island?cityId={$battle.origin.id}">{$battle.origin.name}</a> ({$battle.origin.owner})</td>
									<td style='width: 12px; padding-left: 0px; padding-right: 0px'></td>
									<td style="text-align: center; width: 35px" title="{$battle.missionName}"><img src="http://static.ikariem.org/img/interface/mission_plunder.gif" /></td>
									<td style="width: 12px; padding-left: 0px; padding-right: 0px;"></td>
									<td title="Action" style="text-align: center;"><a href="/transport/abortFleetOperation?eventId={$battle.id}"><img title="Withdraw!" src="http://static.ikariem.org/img/interface/btn_flee.gif" /></a></td>
								</tr>
								{/foreach}
								<!-- end repeat... -->
							</table>
						</div>
						<div id="combat{$smarty.foreach.event.index}pulldown" class="eventbar" title="Current events">
							<div class="status">1 Fleet(s) / 0 underway</div>
							<div class="nextEventETA" id="nextEventETA{$smarty.foreach.event.index}">{$event.nextEventETA.title}</div>
						</div>
					</div>
				</li>
			{/foreach}
			</ul>
		</div>
		<div class="footer"></div>
	</div>
	<!--contentBox-->
	
	<div id="fleetMovements" class="contentBox">
		<h3 class="header"><span class="textLabel">Fleet / Troop Movements</span></h3>
		<div class="content">
			<table width="100%" cellpadding="0" cellspacing="0"
				class="locationEvents">
				<tr style="font-weight: bold; background-color: #faeac6; background-repeat: repeat-x;">
					<td style="background-repeat: repeat-x; width: 35px; padding: 0"></td>
					<td style="width: 50px;"></td>
					<td style="width: 150px;">Units</td>
					<td>Origin</td>
					<td colspan="3" style="width: 80px; text-align: center;">Mission</td>
					<td>Target</td>
					<td style="width: 42px">Action</td>
				</tr>
				<!-- repeat... -->
				{foreach from=$militaryAdvisor.transports item=transport name=transport}
				<tr class="{if $smarty.foreach.transport.index % 2 == 0}alt {/if}{$transport.class1} {$transport.class2}">
					<td><img src="http://static.ikariem.org/img/resources/icon_time.gif" /></td>
					<td id="fleetRow{$transport.id}" title="Time of arrival">{if count($transport.eta) > 0}{$transport.eta.title}{/if}</td>
					<td title="Number of" style="cursor: pointer" onMouseOut="this.firstChild.nextSibling.style.display = 'none'" onMouseOver="this.firstChild.nextSibling.style.display = 'block'">
						{$transport.ships} Ships{if $transport.units > 0} / {$transport.units|number_format} Units{/if}
						<div class="tooltip2" style="z-index:2000">
							<h5>Fleet / Units / Cargo</h5>
							<div class="unitBox" title="Cargo Ship">
								<div class="icon"><img src="http://static.ikariem.org/img/characters/fleet/40x40/ship_transport_r_40x40.gif" /></div>
								<div class="count">{$transport.ships|number_format}</div>
							</div>
							{foreach from=$transport.armies item=army}
							<div class="unitBox" title="{$army.name}">
								<div class="icon"><img src="http://static.ikariem.org/img/characters/military/x40_y40/y40_{$army.css}_faceright.gif" /></div>
								<div class="count">{$army.total|number_format}</div>
							</div>
							{/foreach}
							{foreach from=$transport.goods item=good}
							<div class="unitBox" title="{$good.name}">
								<div class="iconSmall"><img src="http://static.ikariem.org/img/resources/icon_{$good.css}.gif" /></div>
								<div class="count">{$good.total|number_format}</div>
							</div>
							{/foreach}
						</div>
					</td>
					<td title="Origin"><a href="/island?cityId={$transport.origin.id}">{$transport.origin.name}</a> ({$transport.origin.owner})</td>
					<td style='width: 12px; padding-left: 0px; padding-right: 0px'>{if $transport.return}<img style="padding-bottom: 5px;" src="http://static.ikariem.org/img/interface/arrow_left_green.gif" />{/if}</td>
					<td style="text-align: center; width: 35px" title="{$transport.missionName}"><img src="{$transport.img}" /></td>
					<td style="width: 12px; padding-left: 0px; padding-right: 0px;">{if $transport.going}<img style="padding-bottom: 5px;" src="http://static.ikariem.org/img/interface/arrow_right_green.gif" />{/if}</td>
					<td title="Target">{if $transport.target.id > 0}<a href="/island?cityId={$transport.target.id}">{/if}{$transport.target.name}{if $transport.target.id > 0}</a> ({$transport.target.owner}){/if}</td>
					<td title="Action" style="text-align: center;"><a href="/transport/abortFleetOperation?eventId={$transport.id}"><img title="Withdraw!" src="http://static.ikariem.org/img/interface/btn_abort.gif" /></a></td>
				</tr>
				{/foreach}
				<!-- end repeat... -->
			</table>
		</div>
		<div class="footer"></div>
	</div>
	<!--contentBox-->
</div>
<script type="text/javascript">
Event.onDOMReady(function() {literal}{{/literal}
{foreach from=$militaryAdvisor.events item=event name=event}
new Pulldown('combat{$smarty.foreach.event.index}events', 'combat{$smarty.foreach.event.index}pulldown', 0);
getCountdown({literal}{{/literal}enddate: {$event.nextEventETA.enddate}, currentdate: {$event.nextEventETA.currentdate}, el: "nextEventETA{$smarty.foreach.event.index}"{literal}}{/literal})
{/foreach}
{foreach from=$militaryAdvisor.transports item=transport}
{if count($transport.eta) > 0}
getCountdown({literal}{{/literal}enddate: {$transport.eta.enddate}, currentdate: {$transport.eta.currentdate}, el: "fleetRow{$transport.id}"{literal}}{/literal});
{/if}
{/foreach}
{literal}}{/literal})
</script>