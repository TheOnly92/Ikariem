<div id="backTo" class="dynamic">
	<h3 class="header">Troops in town</h3>
	<div class="content">
		<a href="/city/index/id/{$town_id}" title="Back to the town">
			<img src="http://static.ikariem.org/img/img/action_back.gif" width="160" height="100" />
			<span class="textLabel">&lt;&lt; Back to the town</span>
		</a>
	</div>
	<div class="footer"></div>

</div>
<div class="dynamic" id="reportInboxLeft">
	<h3 class="header">Dismiss unit(s)</h3>
	<div class="content">
		<img src="http://static.ikariem.org/img/layout/{if $type == 'army'}militay-dismissed.jpg{else}flotte_entlassen.jpg{/if}" />
		<div class="centerButton">
			<a href="/barracks/armyGarrisonEdit?id={$town_id}&position={$position}" class="button">Dismiss unit(s)</a>
		</div>
	</div>
	
	<div class="footer"></div>
</div>
<div class="dynamic" id="">
	<h3 class="header">Barracks</h3>
	<div class="content">
		<img src="http://static.ikariem.org/img/buildings/y100/barracks.gif" />
		<div class="centerButton">
			<a href="/barracks?id={$town_id}&position={$position}" class="button">To the barracks</a>
		</div>
	
	</div>
	<div class="footer"></div>
</div>

<div id="mainview">
	<div class="buildingDescription">
		<h1>Troops in town</h1>
		<p>Inspect troops that are stationed in the town</p>
	</div>
	
	<div class="militaryAdvisorTabs">
		<table cellpadding="0" cellspacing="0" id="tabz">
		<tr>
			<td{if $type == 'army'} class="selected"{/if}><a title="Units" href="/city/military/type/army/id/{$town_id}"><em>Units</em></a></td>
			<td{if $type == 'fleet'} class="selected"{/if}><a title="Ships" href="/city/military/type/fleet/id/{$town_id}"><em>Ships</em></a></td>
		</tr>
		
		</table>
	</div>
	<div class="yui-navset yui-navset-top" id="demo">
		<div class="yui-content">
			<div id="tab1" style="display: block;">
				<!--------------------------------------------------------------------------------------
					////////////////////////////////////////////////////////////////////////////////////
					///////////////////////////////////   Troops   /////////////////////////////////////
					//////////////////////////////////////////////////////////////////////////////////// -->
				<div class="contentBox01h">
					<h3 class="header"><span class="textLabel">Garrison</span></h3>
					
					<div class="content">
						<table cellpadding="0" cellspacing="0">
							<tr>
								{if $type == 'army'}
								{section name=troops1 loop=$troops max=7}
								{assign var=troop value=`$troops[troops1]`}
								<th title="{$troop.name}"><img src="http://static.ikariem.org/img/characters/military/x60_y60/y60_{$troop.css}_faceright.gif" alt="{$troop.name}" title="{$troop.name}" /></th>
								{/section}
								{else}
								{section name=troops1 loop=$troops max=4}
								{assign var=troop value=`$troops[troops1]`}
								<th title="{$troop.name}"><img src="http://static.ikariem.org/img/characters/fleet/60x60/{$troop.css}_faceright.gif" alt="{$troop.name}" title="{$troop.name}" /></th>
								{/section}
								{/if}
							</tr>
							<tr class="count">
								{if $type == 'army'}
								{section name=troopsValue1 loop=$troopsValue max=7}
								<td>{$troopsValue[troopsValue1]}</td>
								{/section}
								{else}
								{section name=troopsValue1 loop=$troopsValue max=4}
								<td>{$troopsValue[troopsValue1]}</td>
								{/section}
								{/if}
							</tr>
						</table>
						
						<table cellpadding="0" cellspacing="0">
							<tr>
								{if $type == 'army'}
								{section name=troops1 loop=$troops max=7 start=7}
								{assign var=troop value=`$troops[troops1]`}
								<th title="{$troop.name}"><img src="http://static.ikariem.org/img/characters/military/x60_y60/y60_{$troop.css}_faceright.gif" alt="{$troop.name}" title="{$troop.name}" /></th>
								{/section}
								{else}
								{section name=troops1 loop=$troops max=4 start=4}
								{assign var=troop value=`$troops[troops1]`}
								<th title="{$troop.name}"><img src="http://static.ikariem.org/img/characters/fleet/60x60/{$troop.css}_faceright.gif" alt="{$troop.name}" title="{$troop.name}" /></th>
								{/section}
								{/if}
							</tr>
							<tr class="count">
								{if $type == 'army'}
								{section name=troopsValue1 loop=$troopsValue max=7 start=7}
								<td>{$troopsValue[troopsValue1]}</td>
								{/section}
								{else}
								{section name=troopsValue1 loop=$troopsValue max=4 start=4}
								<td>{$troopsValue[troopsValue1]}</td>
								{/section}
								{/if}
							</tr>
						</table>
					</div>
					
					<div class="footer"></div>
				</div>
				<div class="contentBox01h">
					<h3 class="header"><span class="textLabel">Defender</span></h3>
					<div class="content">
						<p style="text-align: center;">There are no allied units stationed in this town!</p>
					</div>
					<div class="footer"></div>
				
				</div>
				
				<div class="contentBox01h">
					<h3 class="header"><span class="textLabel">Occupying forces</span></h3>
					<div class="content">
						<p style="text-align: center;">There are no units from an enemy occupation force stationed here!</p>					
					</div>
					<div class="footer"></div>
				</div>
			
			</div>
		</div>
	</div>
	<script type="text/javascript">
	var tabView = new YAHOO.widget.TabView('demo');
	</script>
</div>
