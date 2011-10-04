<div id="backTo" class="dynamic">
	<h3 class="header">Barracks</h3>
	<div class="content">
        <a href="/barracks?id={$town_id}&position={$position}" title="Back to Barracks">
		<img src="http://static.ikariem.org/img/buildings/y100/barracks.gif" width="160" height="100" />
		<span class="textLabel">&lt;&lt; Back to Barracks</span></a>
	</div>
	<div class="footer"></div>
</div>

<div id="mainview">
	<div class="buildingDescription">
		<h1>Inspect Garrison</h1>
		<p>Dismiss unit(s)</p>	
	</div>
	{if $armyGarrisonEdit.showConfirm == true}
	<div class="contentBox01h">
		<h3 class="header">Are you sure?</h3>
		<div class="content">
			<p>Really disband {$armyGarrisonEdit.fire.total} units?</p>
			<form id="buildForm" action="/barracks/fireUnits?id={$town_id}&position={$position}" method="post">
			{foreach from=$armyGarrisonEdit.fire item=unit key=id}
			{if $id != 'total'}
			<input type="hidden" name="{$id}" value="{$unit}" />
			{/if}
			{/foreach}
			<div class="centerButton">
				<input class="button" type="submit" value="Yes!" />
			</div>
			</form>
		</div>
	</div>
	{/if}
	<form id="fireForm"  action="/barracks/armyGarrisonEdit?confirm=1&id={$town_id}&position={$position}" method="post">
		<div class="contentBox01h">
			<h3 class="header">Fire Units</h3>
			<div class="content">
				<ul id="units">
					{foreach from=$armyGarrisonEdit.units item=unit}
					<li class="unit {$unit.unit_id}">
						<div class="unitinfo">
							<h4>{$unit.name}</h4>
							<a title="To the description of {$unit.name}" href="/ikipedia/unitDescription?unitId={$unit.id}"><img src="http://static.ikariem.org/img/characters/military/120x100/{$unit.unit_id}_r_120x100.gif" /></a>
							<div class="unitcount"><span class="textLabel">Available: </span>{$unit.available}</div>
							<p>{$unit.desc}</p>
							<label for="textfield_{$unit.unit_id}">{$unit.name} Dismiss units:</label>
							<div class="sliderinput">
								<div class="sliderbg" id="sliderbg_{$unit.unit_id}">
									<div class="actualValue" id="actualValue_{$unit.unit_id}"></div>
									<div class="sliderthumb" id="sliderthumb_{$unit.unit_id}"></div>
								</div>
								<script type="text/javascript">
								{literal}
									create_slider({{/literal}
										dir: "ltr",
										id: "slider_{$unit.unit_id}",
										maxValue: {$unit.maxValue},
										overcharge: 0,
										iniValue: {if isset($armyGarrisonEdit.fire[$unit.id]) && $armyGarrisonEdit.fire[$unit.id] > 0}{$armyGarrisonEdit.fire[$unit.id]}{else}0{/if},
										bg: "sliderbg_{$unit.unit_id}",
										thumb: "sliderthumb_{$unit.unit_id}",
										topConstraint: -10,
										bottomConstraint: 326,
										bg_value: "actualValue_{$unit.unit_id}",
										textfield: "textfield_{$unit.unit_id}"
									{literal}})
									var slider = sliders["default"];
								{/literal}
								</script>
								<a class="setMin" href="#reset" onclick="sliders['slider_{$unit.unit_id}'].setActualValue(0); return false;" title="Reset entry"><span class="textLabel">min</span></a>
								<a class="setMax" href="#max" onclick="sliders['slider_{$unit.unit_id}'].setActualValue({$unit.maxValue}); return false;" title="Recruit as many as possible"><span class="textLabel">max</span></a>
							</div>
							<div class="forminput">
								<input class="textfield" id="textfield_{$unit.unit_id}" type="text" name="u{$unit.id}" value="0" size="4" maxlength="4" /> <a class="setMax" href="#max" onclick="sliders['slider_{$unit.unit_id}'].setActualValue({$unit.maxValue}); return false;" title="Recruit as many as possible"><span class="textLabel">max</span></a>
								<div class="centerButton">
									<input title="Fire Units!" class="button" type="submit" value="Dismiss units!" />
								</div>
							</div>
							<div class="costs">
								<h5>Costs:</h5>
								<ul class="resources">
									<li class="upkeep" title="Upkeep cost per hour"><span class="textLabel">Upkeep cost per hour: </span>{$unit.upkeep}</li>
								</ul>
							</div>
						</div>
					</li>
				{/foreach}
				</ul>

			</div>
			<div class="footer"></div>
		</div>
	</form>
</div>
