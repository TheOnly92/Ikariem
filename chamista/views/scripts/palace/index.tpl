<!---------------------------------------------------------------------------------------
	 ////////////////////////////////////////////////////////////////////////////////////
	 ///////////////////////////// dynamic side-boxes. //////////////////////////////////
	 //////////////////////////////////////////////////////////////////////////////////// -->
<!-- all items going to the left side get class dynamic -->

{include file="dynamics/buildingUpgrade.tpl"}

<!---------------------------------------------------------------------------------------
	 ////////////////////////////////////////////////////////////////////////////////////
	 ///////////////// the main view. take care that it stretches. //////////////////////
	 //////////////////////////////////////////////////////////////////////////////////// -->
<div id="mainview">

	<div class="buildingDescription">
		<h1>Palace</h1>
		{if isset($construction)}
		{include file="dynamics/upgradeProgress.tpl"}
		{else}
		<p>{$building_desc}</p>
		{/if}
	</div><!--buildingDescription -->
	
	<div class="contentBox01h">
		<h3 class="header"><span class="textLabel">Towns in your empire</span></h3>
		<div class="content">
			<table cellpadding="0" cellspacing="0" class="table01">
				<thead>
					<tr>
						<th class="crown"></th>
						<th>Town</th>
						<th>Level</th>
						<th>Palace</th>
						<th>Island</th>
						<th>Resource</th>
					</tr>
				</thead>
				<tbody>
					{foreach from=$palace.towns item=town}
					<tr>
						<td>{if $town.capital}<img src="http://static.ikariem.org/img/layout/crown.gif" width="20" height="20" alt="Capital" title="Capital" />{/if}</td>
						<td>{$town.name}</td>
						<td>{$town.level}</td>
						<td>{$town.palace}</td>
						<td>{$town.island.name} [{$town.island.pos.x}:{$town.island.pos.y}]</td>
						<td><img src="http://static.ikariem.org/img/resources/icon_{$town.resource.css}.gif"  title="{$town.resource.name}" alt="{$town.resource.name}" /></td>
					</tr>
					{/foreach}
				</tbody>
			</table>
		</div><!--content -->
		<div class="footer"></div>
	</div><!-- contentbox01h -->
	
	<div class="contentBox01h">
		<h3 class="header"><span class="textLabel">Occupied cities</span></h3>
		
		<div class="content">
			<table cellpadding="0" cellspacing="0" class="table01">
				<thead>
				<tr>
					<th class="crown"></th>
					<th>Town</th>
					<th>Level</th>
					<th>Island</th>
					<th>Resource</th>
					<th>Action</th>
				</tr>
				</thead>
				<tbody>
					<tr><td colspan="6">You have not occupied any cities.</td></tr>
				</tbody>
			
			</table>
		</div><!--content -->
		<div class="footer"></div>
	</div><!-- contentbox01h -->

</div><!-- end #mainview -->
