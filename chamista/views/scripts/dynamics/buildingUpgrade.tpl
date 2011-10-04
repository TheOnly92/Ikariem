<div id="buildingUpgrade" class="dynamic">
	<h3 class="header">Expand <a class="help" href="?view=buildingDetail&buildingId=0" title="Help"><span class="textLabel">Need help?</span></a></h3>
	<div class="content">
		<div class="buildingLevel">
			<span class="textLabel">Level </span>{$buildingUpgrade.currLvl}
		</div>
		
		<h4>Needed for level {$buildingUpgrade.nextLvl}:</h4>
		<ul class="resources">
		{if isset($buildingUpgrade.upgradeCost.wood)}
			<li class="wood" title="Building material"><span class="textLabel">Building material: </span>{$buildingUpgrade.upgradeCost.wood|number_format}</li>
		{/if}
		{if isset($buildingUpgrade.upgradeCost.marble)}
			<li class="marble alt" title="Marble"><span class="textLabel">Marble: </span>{$buildingUpgrade.upgradeCost.marble|number_format}</li>
		{/if}
		{if isset($buildingUpgrade.upgradeCost.crystal)}
			<li class="glass alt" title="Crystal Glass"><span class="textLabel">Crystal Glass: </span>{$buildingUpgrade.upgradeCost.crystal|number_format}</li>
		{/if}
			<li class="time" title="Building time"><span class="textLabel">Building time: </span>{$buildingUpgrade.upgradeTime}</li>
		</ul>
		<ul class="actions">
			<li class="upgrade">
				{if isset($upgrade_disabled) && $upgrade_disabled == true}
				<a class="disabled" href="#" title="Expansion not possible at the moment!"></a>
				{else}
				<a href="/city/upgradeBuilding?id={$town_id}&position={$position}&level={$buildingUpgrade.nextLvl}" title="Upgrade {$build_name}!"><span class="textLabel">Upgrade</span></a>
				{/if}
			</li>
			<li class="downgrade">
				{if isset($construction) || isset($upgrade_disabled) && $upgrade_disabled == true}
				<a class="disabled" href="#" title="You can't demolish this building right now!"></a>
				{else}
				<a href="/city/buildingsDemolition?id={$town_id}&position={$position}" title="Demolish {$build_name}"><span class="textLabel">Demolish</span></a>
				{/if}
			</li>
		</ul>
	
	</div>
	<div class="footer"></div>
</div>