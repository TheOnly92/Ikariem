<!---------------------------------------------------------------------------------------
     ////////////////////////////////////////////////////////////////////////////////////
     ///////////////////////////// dynamic side-boxes. //////////////////////////////////
     //////////////////////////////////////////////////////////////////////////////////// -->

{include file="dynamics/buildingUpgrade.tpl"}

	<div class="dynamic" id="reportInboxLeft">
		<h3 class="header"></h3>
		<div class="content">
			<div class="centerButton">
				<a href="?view=armyGarrisonEdit&id=254479&position=5" class="button">Dismiss units</a>
	        </div>
        </div>
	    <div class="footer"></div>
	</div>
	<div id="unitConstructionList" class="dynamic"><h3 class="header">Construction queue <a class="help" href="/ikariem/unitDescription" title="Help"><span class="textLabel">Help</span></a></h3>
		<div class="content">
			{if count($barracks.trainingCurrent) > 0}
			<h4 style="">Under construction:</h4>
			{foreach from=$barracks.trainingCurrent.units item=unit}
			<div class="army_wrapper" title="{$unit.name}">
				<div class="army s{$unit.sid}">
					<span class="textLabel">{$unit.name}: </span>
					<div class="unitcounttextlabel">{$unit.value}</div>
				</div>
			</div>
			{/foreach}
			<div style="clear: both;"></div>
			<div class="results" style="">
				<div class="progressbar"><div class="bar" id="buildProgress" title="{$barracks.trainingCurrent.percentage}%" style="width:{$barracks.trainingCurrent.percentage}%"></div></div>
				<div class="time" id="buildCountDown">{$barracks.trainingCurrent.countdown}<span class="textLabel"> until completion</span></div>
			</div>
			{if count($barracks.training) > 0}
				{foreach from=$barracks.training item=row name=training}
				<div class="constructionBlock">
					<h4 style="">In Queue ({$smarty.foreach.training.index+1}):</h4>
					{foreach from=$row.units item=unit}
					<div class="army_wrapper" title="{$unit.name}">
						<div class="army s{$unit.sid}">
							<span class="textLabel">{$unit.name}: </span>
							<div class="unitcounttextlabel">{$unit.value}</div>
						</div>
					</div>
					{/foreach}
				</div>
				<div style="clear: both;"></div>
				<div class="constructionBlock">
					<div class="time" id="queueEntry{$smarty.foreach.training.index}" style="">{$row.done}<span class="textLabel"> until completion</span></div>
				</div>
				{/foreach}
			{/if}
			<div style="text-align:center; padding: 10px 0 10px 0;">
				<a class="button" href="javascript:myConfirm('Are you sure that you want to cancel this training job? You will lose all the resources you invested!','/barracks/abortUnits/id/{$town_id}/position/{$position}');">Abort</a>
			</div>
			{/if}
		  </div>
		<div class="footer"></div>

	</div>
<!---------------------------------------------------------------------------------------
     ////////////////////////////////////////////////////////////////////////////////////
     ///////////////// the main view. take care that it stretches. //////////////////////
     //////////////////////////////////////////////////////////////////////////////////// -->
	<div id="mainview">
		<div class="buildingDescription">
			<h1>Barracks</h1>
				{if isset($construction)}
				{include file="dynamics/upgradeProgress.tpl"}
				{else}
				<p>{$building_desc}</p>
				{/if}
		      		
		</div>
		
		
		<form id="buildForm"  action="/barracks/buildUnits?id={$town_id}&position={$position}" method="POST">

		<div class="contentBox01h" id="selected_units"> 
                <h3 class="header">Units scheduled for training</h3> 
                <div class="content"> 
                    <div id="unitCountIcons">&nbsp;</div> 
                    <div class="divider"></div> 
                    <div id="cost_wrapper"> 
                        <span id="accumulatedUnitCosts">&nbsp;</span> 
                        <span id="button_purchase"><input class="button" type="submit" id="button_recruit" value="recruit"></span> 
                    </div> 
                </div> 
                <div class="footer">&nbsp;</div> 
            </div> 

		<div class="contentBox01h">
			<h3 class="header">Recruit Units</h3>
			<div class="content">

				<ul id="units">
				{foreach from=$barracks.units item=unit}
					<li class="unit {$unit.unit_id}">
						<div class="unitinfo">
							<h4>{$unit.name}</h4>
							<a title="To the description of {$unit.name}" href="/ikipedia/unitDescription?unitId={$unit.id}"><img src="http://static.ikariem.org/img/characters/military/120x100/{$unit.unit_id}_r_120x100.gif" /></a>
							<div class="unitcount"><span class="textLabel">Available: </span>{$unit.available}</div>
							<p>{$unit.desc}</p>
							{if $unit.action === true && !isset($construction)}
							<label for="textfield_{$unit.unit_id}">Recruit {$unit.name}</label>
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
										iniValue: 0,
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
							{else}
							<hr />
							{/if}
							<div class="forminput">
							{if isset($construction)}
							{else}
							{if $unit.action === true}
								<input class="textfield" id="textfield_{$unit.unit_id}" type="text" name="u{$unit.id}" value="0" size="4" maxlength="4" /> <a class="setMax" href="#max" onclick="sliders['slider_{$unit.unit_id}'].setActualValue({$unit.maxValue}); return false;" title="Recruit as many as possible"><span class="textLabel">max</span></a>
							{else}
								{$unit.action}
							{/if}
							{/if}
							</div>
							<div class="costs">
								<h5>Costs:</h5>
								<ul class="resources">
									<li class="citizens" title="Citizens"><span class="textLabel">Citizens: </span>{$unit.citizen}</li>
									{if $unit.cost.wood}
									<li class="wood" title="Building material"><span class="textLabel">Building material: </span>{$unit.cost.wood}</li>
									{/if}
									{if isset($unit.cost.sulfur)}
									<li class="sulfur" title="Sulfur"><span class="textLabel">Sulfur: </span>{$unit.cost.sulfur}</li>
									{/if}
									<li class="upkeep" title="Upkeep cost per hour"><span class="textLabel">Upkeep cost per hour: </span>{$unit.upkeep}</li>
									<li class="time" title="Building time"><span class="textLabel">Building time: </span>{$unit.time}</li>
								</ul>
							</div>
						</div>
					</li>
				{/foreach}
				</ul>
			</div>

			<div class="footer"></div>
		</div>
		</form> <!-- End buildForm -->
	</div><!-- END mainview -->
{if count($barracks.trainingCurrent) > 0}
<script type="text/javascript">
{literal}
	getCountdown({{/literal}
		enddate: {$barracks.trainingCurrent.enddate},
		currentdate: {$barracks.trainingCurrent.currentdate},
		el: "buildCountDown"
		{literal}}, 2, " ", "", true, true);

	var tmppbar = getProgressBar({{/literal}
		startdate: {$barracks.trainingCurrent.startdate},
		enddate: {$barracks.trainingCurrent.enddate},
		currentdate: {$barracks.trainingCurrent.currentdate},
		bar: "buildProgress"
		{literal}});
	tmppbar.subscribe("update", function(){
		this.barEl.title=this.progress+"%";
		});
	tmppbar.subscribe("finished", function(){
		this.barEl.title="100%";
		});
{/literal}
</script>
{/if}