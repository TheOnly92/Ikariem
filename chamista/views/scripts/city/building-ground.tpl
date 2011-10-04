<!---------------------------------------------------------------------------------------
     ////////////////////////////////////////////////////////////////////////////////////
     ///////////////////////////// dynamic side-boxes. //////////////////////////////////
     //////////////////////////////////////////////////////////////////////////////////// -->

<!-- none... -->

<!---------------------------------------------------------------------------------------
	 ////////////////////////////////////////////////////////////////////////////////////
	 ///////////////// the main view. take care that it stretches. //////////////////////
	 //////////////////////////////////////////////////////////////////////////////////// -->
<div id="mainview">
	<div class="buildingDescription">
		<h1>Empty building ground</h1>
		<p>A vast and empty place catches your eye. Which Building should your citizens construct here?</p>
	</div>
	
	<div class="contentBox01h">
		<h3 class="header">Build building</h3>
		<div class="content">
			<ul id="buildings">
				{if count($buildings) > 0}
				{foreach from=$buildings item=building}
				<li class="building {$building.class}">
					<div class="buildinginfo">
						<h4>{$building.name}</h4>
						<a href="/ikipedia/buildingDetail?buildingId={$building.id}"><img src="http://static.ikariem.org{$building.img}" /></a>
						<p>{$building.desc}</p>
					</div>
					<hr />
					{if $building.canBuild === true}
					<div class="centerButton">
						<a class="button build" style="padding-left: 3px; padding-right: 3px;" href="/city/build?id={$town_id}&position={$position}&building={$building.id}"><span class="textLabel">Yes! Build now!</span></a>
					</div>
					{else}
					{if $building.canBuild == -3}
					<p class="cannotbuild">A building is already being constructed or expanded!</p>
					{else}
					<p class="cannotbuild">Not enough resources!</p>
					{/if}
					{/if}
					<div class="costs">
						<h5>Costs:</h5>
						<ul class="resources">
							{if isset($building.cost.wood)}
							<li class="wood" title="Building material"><span class="textLabel">Building material: </span>{$building.cost.wood|number_format}</li>
							{/if}
							<li class="time" title="Building time"><span class="textLabel">Building time: </span>{$building.time}</li>
						</ul>
					</div>
				</li>
				{/foreach}
				{else}
				<li>You cannot build new buildings at the moment.</li>
				{/if}
			</ul>
		
		</div>
		<div class="footer"></div>
	</div>
</div><!-- END mainview -->