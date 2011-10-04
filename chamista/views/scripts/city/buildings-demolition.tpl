<!---------------------------------------------------------------------------------------
	 ////////////////////////////////////////////////////////////////////////////////////
	 ///////////////////////////// dynamic side-boxes. //////////////////////////////////
	 //////////////////////////////////////////////////////////////////////////////////// -->

{if isset($construction)}
{include file="dynamics/upgradeProgressSmall.tpl"}
{/if}

<div id="backTo" class="dynamic">
	<h3 class="header">{$building.build_name}</h3>
	<div class="content">
		<a href="/{$building.build_action}?id={$town_id}&position={$position}" title="Back to the {$building.build_name}">
		<img src="http://static.ikariem.org/img/buildings/y100/{$building.build_action}.gif" width="160" height="100" />
		<span class="textLabel">&lt;&lt; Back to the {$building.build_name}</span></a>

	</div>
	<div class="footer"></div>
</div>


<!---------------------------------------------------------------------------------------
	 ////////////////////////////////////////////////////////////////////////////////////
	 ///////////////// the main view. take care that it stretches. //////////////////////
	 //////////////////////////////////////////////////////////////////////////////////// -->
<div id="mainview">
	<h1>{$building.build_name}</h1>
	
	<div class="contentBox" id="demolition">
		<h3 class="header">Confirm downgrading of building</h3>
		
		<div class="content">
			<h4>Attention</h4>
			{if isset($construction)}
			<p>Are you really sure you want to cancel the expansion of your building? Keep in mind that at the current level only {$demolitionReturn}% of the purchase cost will be returned!</p>
			{else}
			<p>By confirming this action your building will be downgraded by one level. Should it reach level 0 it will then be completely torn down and the building space will become available again. Are you really sure you want to reduce the building level by one level? Keep in mind that at the current expansion level only {$demolitionReturn}% of the purchase cost will be returned!</p>
			{/if}
			<p>The following resources will be returned by demolishing:	</p>
			<ul class="resources">
				{if isset($cost.wood)}
				<li class="wood" title="Building material"><span class="textLabel">Building material: </span>{$cost.wood|number_format}</li>
				{/if}
				{if isset($cost.marble)}
				<li class="marble alt" title="Marble"><span class="textLabel">Marble: </span>{$cost.marble|number_format}</li>
				{/if}
			</ul>
			<hr />
			<a class="yes" href="/city/downgradeBuilding?level={$building.build_lvl}&id={$town_id}&position={$position}" title="Yes, I am sure">Yes, I am sure!</a>
			
			<a class="no" href="/{$building.build_action}?id={$town_id}&position={$position}" title="No, better not"><span class="textLabel">No, better not</span></a>
		</div>
		<div class="footer"></div>
	</div>
</div><!-- end #mainview -->