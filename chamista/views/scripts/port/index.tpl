<!---------------------------------------------------------------------------------------
	 ////////////////////////////////////////////////////////////////////////////////////
	 ///////////////////////////// dynamic side-boxes. //////////////////////////////////
	 //////////////////////////////////////////////////////////////////////////////////// -->

{include file="dynamics/buildingUpgrade.tpl"}

<div class="dynamic">
	<h3 class="header">Transport capacity<a class="help" href="?view=shipdescription&shipId=201" title="Help"><span class="textLabel">Help?</span></a></h3>
	<div class="content">
		<p>Trade ships are always available where they are needed.</p>
		<p><strong>Capacity per trade ship: </strong>500</p>
		<p><strong>Total capacity: </strong>{$ships*500|number_format}</p>

	</div>
	<div class="footer"></div>
</div>

<div class="dynamic">
	<h3 class="header">Loading Speed</h3>
	<div class="content">
		<p>The loading speed shows how fast cargo ships can be loaded and unloaded in your Harbour.</p>
		<p><strong>Loading Speed:</strong><br> {$loading_speed} Goods per minute</p>

	</div>
	<div class="footer"></div>
</div>
<!---------------------------------------------------------------------------------------
	 ////////////////////////////////////////////////////////////////////////////////////
	 ///////////////// the main view. take care that it stretches. //////////////////////
	 //////////////////////////////////////////////////////////////////////////////////// -->
<div id="mainview">
	<div class="buildingDescription">
		<h1>Trading port</h1>
		{if isset($construction)}
		{include file="dynamics/upgradeProgress.tpl"}
		{else}
		<p>{$building_desc}</p>
		{/if}
	</div>
	
	<div class="contentBox01h">
		<h3 class="header"><span class="textLabel">Buy Cargo Ship</span></h3>
		<div class="content">
			<ul id="units">
				<li class="unit">
					<div class="unitinfo">
						<h4>Cargo Ship</h4>
						<a title="Learn more about Cargo Ship..." href="?view=shipdescription&shipId=201">
						<img src="http://static.ikariem.org/img/characters/fleet/120x100/ship_transport_r_120x100.gif" /></a>
						<div class="unitcount"><span class="textLabel">Available: </span>
						{$ships}
						</div>
						<p>Trade ships are the most important support for your island empire. No matter whether they have to transport goods, units or news: your seamen will make sure, that everything arrives at its destination quick and safely.</p>
					</div>
					<label for="textfield_">Buy Cargo Ship:</label>
					<div class="forminput">
						Maximum: 160<br>
						{if $ships_buyable}
						<div class="leftButton">
						<a href="/port/increaseTransporter?id={$town_id}&position={$position}" class="button bigButton">Buy Cargo Ship</a>
						</div>
						{else}
						Insufficient Resources
						{/if}
					</div>
						<div class="costs">
						<ul class="resources">
							<li class="gold"><span class="textLabel">Gold: </span>{$ships_cost|number_format}</li>
						</ul>
					</div>
				</li>
			</ul>
		</div><!-- end .content -->
	   <div class="footer"></div>
	</div><!-- contentBox01h -->
	<!--//////////////////////////////////////////////////////////////////////////////////////////
	 	////////// Trade ships to send to colonies ///////////////////////////////////////////////
	 	//////////////////////////////////////////////////////////////////////////////////////////-->
	<div class="contentBox01h">
		<h3 class="header"><span class="textLabel">Send out trade ship</span></h3>
		<div class="content">
			<ul class="cities">
				{foreach from=$port.colonies item=colony}
				{if $town_id != $colony.town_id}
				<li><a title="Transport to {$colony.town_name}" href="/transport?destinationCityId={$colony.town_id}">({$colony.posx}:{$colony.posy}) {$colony.town_name}</a></li>
				{/if}
				{/foreach}
			</ul>
		</div><!-- end .content -->
		<div class="footer"></div>
	</div><!-- contentBox01h -->
	
	<div class="contentBox01h" style="z-index:100">
	<h3 class="header"><span class="textLabel">Fleets being loaded</span></h3>
	<div class="content master">
	
	<div class="tcap">Own cargo ships</div>
		{if count($fleetsLoaded.own) > 0}
		<table cellpadding="0" cellspacing="0" class="table01">
			<thead>
				<tr>
					<th class="origin">Destination:</th>
					<th>Quantity</th>
					<th>Mission</th>
					<th>Status</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$fleetsLoaded.own item=transport}
				<tr>
					<td>{$transport.destination}</td>
					<td>
						<div class="tooltip" style="position:absolute;width:100px;overflow:show;">
							<table border='0' cellspacing='0' class='stuff'>
								<tr><th colspan='2'>Goods on board</th></tr>
								{foreach from=$transport.stuff item=stuff}
								<tr><td class="unit"><img src="http://static.ikariem.org/img/resources/icon_{$stuff.css}.gif" /></td><td class="count">{$stuff.quantity|number_format}</td></tr>
								{/foreach}
							</table>
						</div>
						{$transport.quantity}
					</td>
					<td>{if $transport.mission == 0}Transport{elseif $transport.mission == 3}Colonize{/if}</td>
					<td>
						<div class="time" id="outgoingOwnCountDown">{$transport.progress.countDown}</div>
						<div class="progressBar"><div class="bar" id="outgoingOwnProgress"></div></div>
						<script type="text/javascript">{literal}
						Event.onDOMReady(function() {
							getCountdown({{/literal}
								enddate: {$transport.progress.enddate},
								currentdate: {$transport.progress.currentdate},
								el: "outgoingOwnCountDown"
							{literal}}, 2, " ", "", true, true);
							
							var tmppbar = getProgressBar({{/literal}
								startdate: {$transport.progress.startdate},
								enddate: {$transport.progress.enddate},
								currentdate: {$transport.progress.currentdate},
								bar: "outgoingOwnProgress"
							{literal}});
							tmppbar.subscribe("update", function(){
								this.barEl.title=this.progress+"%";
							});
							tmppbar.subscribe("finished", function(){
								this.barEl.title="100%";
							});
						});{/literal}
						</script>
						is loading
					</td>
					<td></td>
				</tr>
				{/foreach}
			</tbody>
		</table>
		{else}
		<p>No ships registered with the port master</p>
		{/if}
		<div class="tcap">Foreign ships</div>
			<p>No ships registered with the port master</p>
		</div>
		<div class="footer"></div>
	</div><!-- contentBox01h -->
	
	<div class="contentBox01h" style="z-index:50;">
		<h3 class="header"><span class="textLabel">Incoming Traders</span></h3>
		<div class="content master">
			<p>No ships registered with the port master</p>
		</div>
		<div class="footer"></div>
	</div><!-- contentBox01h -->

</div><!-- end #mainview -->