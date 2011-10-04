<!-- ////////////////////////////////////////////////////////////////////////////////////
	 ///////////////////////////// dynamic side-boxes. //////////////////////////////////
	 //////////////////////////////////////////////////////////////////////////////////// -->
<div id="resUpgrade" class="dynamic{if isset($construction)} upgrading{/if}">
	<h3 class="header">Saw Mill <a class="help" href="?view=informations&articleId=10012&mainId=10012" title="Help"><span class="textLabel">Need help?</span></a></h3>
	<div class="content"><img src="http://static.ikariem.org/img/resources/img_wood.jpg" alt="" />
		{if isset($construction) && count($construction) > 0}
		<div class="isUpgrading">In Progress!</div> 
		<div class="buildingLevel"><span class="textLabel">Level: </span>{$construction.currentlevel}</div> 
		<div class="nextLevel"><span class="textLabel">Next Level: </span>{$construction.nextlevel}</div> 
		<div class="progressBar">
			<div class="bar" id="upgradeProgress" title="{$construction.progress}%" style="width:{$construction.progress}%;"></div>
		</div> 
		<script type="text/javascript"> 
		{literal}
		Event.onDOMReady(function() {
			var tmppbar = getProgressBar({{/literal}
				startdate: {$construction.startdate},
				enddate: {$construction.enddate},
				currentdate: {$construction.currentdate},
				bar: "upgradeProgress"
			{literal}});
			tmppbar.subscribe("update", function(){
				this.barEl.title=this.progress+"%";
			});
			tmppbar.subscribe("finished", function(){
				this.barEl.title="100%";
			});
		});
		{/literal}
		</script> 
		<div class="time" id="upgradeCountDown">{$construction.countDown}</div> 
		<script type="text/javascript"> 
		{literal}
		Event.onDOMReady(function() {
			var tmpCnt = getCountdown({{/literal}
				enddate: {$construction.enddate},
				currentdate: {$construction.currentdate},
				el: "upgradeCountDown"
			{literal}}, 2, " ", "", true, true);
			tmpCnt.subscribe("finished", function() {
				setTimeout(function() {
					location.href="/island/resource/id/{/literal}{$island.island_id}{literal}";
				},2000);
			})
		});
		{/literal}
		</script> 
		{else}
		<div class="buildingLevel"><span class="textLabel">Level: </span>{$island.island_saw_lvl}</div>
		<h4>Required for next Level:</h4>
		
		<ul class="resources">
			<li class="wood"><span class="textLabel">Building material: </span>{$upgrade_cost.wood|number_format}</li>
		</ul>
		<h4>Available:</h4>
		<div>
			<ul class="resources">
				<li class="wood"><span class="textLabel">Building material: </span>{$donated|number_format}</li>
			</ul>
		</div>
		<form id="donateForm" action="/island/donate?id={$island.island_id}" method="post">
		<input name="type" type="hidden" value="resource" />
		<div id="donate">
			<label for="donateWood">Donate:</label>
			<input id="donateWood" name="donation" type="text" autocomplete="off" class="textfield" />
			<a href="#setmax" title="donate as much as possible" onclick="Dom.get('donateWood').value={$maxDonate}; return false;">max</a>
			<div class="centerButton"><input type="submit" class="button" value="Donate for upgrade" /></div>
		</div>
		</form>
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
		<h1>Saw Mill</h1>
		
		<p>Wood from the nearby forest is sawed into boards at the saw mill. It
		is then used as a material for constructing buildings and much more. The
		saw mill is extended by all inhabitants of the island together. The
		larger it is, the more workers you can employ there.</p>
	</div>
	<form id="setWorkers" action="/island/setWorkers?id={$island.island_id}&cityId={$resource.townId}" method="post">
	<div id="setWorkersBox" class="contentBox">
		<h3 class="header"><span class="textLabel">Assign Workers</span></h3>
		<div class="content">
			<ul>
				<li class="citizens"><span class="textLabel">Citizens: </span>
					<span class="value" id="valueCitizens">{$resource.freeCitizens}</span>
				</li>
				<li class="workers">
					<span class="textLabel">Workers: </span>
					<span class="value" id="valueWorkers">{$resource.workers}</span>
				</li>
				<li class="gain" title="Worker production:{$woodProduction}" alt="Worker production:{$woodProduction}">
					<span class="textLabel">Capacity: </span>
					<div id="gainPoints">
						<div id="resiconcontainer"><img id="resicon" src="http://static.ikariem.org/img/resources/icon_wood.gif" width="25" height="20" /></div>
					</div>
					<div class="gainPerHour">
						<span id="valueResource"{if $island.overload} class="overcharged"{/if}>+{$woodProduction}</span>
						<span class="timeUnit">per Hour</span>
					</div>
				</li>
				<li class="costs">
					<span class="textLabel">Income of the town: </span>
					<span id="valueWorkCosts" class="{if $goldProduction >= 0}positive{else}negative{/if}">{if $goldProduction < 0}-{/if}{$goldProduction}</span>
					<img src="http://static.ikariem.org/img/resources/icon_gold.gif" title="Gold" alt="Gold" /><span class="timeUnit"> per Hour</span>
				</li>
			</ul>
			<div id="overchargeMsg" class="status nooc ocready oced">Overloaded!</div>
			<div class="slider" id="sliderbg">
				<div class="actualValue" id="actualValue"></div>
				<div class="overcharge" id="overcharge"></div>
				<div id="sliderthumb"></div>
			</div>
			<a class="setMin" href="#reset" onclick="sliders['default'].setActualValue(0); return false;" title="No Workers"><span class="textLabel">min</span></a>
			<a class="setMax" href="#max" onclick="sliders['default'].setActualValue({$maxWorkers}); return false;" title="maximum amount of Workers"><span class="textLabel">max</span></a>
			<input type="hidden" name="type" value="resource" />
			<input class="textfield" id="inputWorkers" type="text" name="rw" maxlength="4" autocomplete="off" />
			<input class="button" id="inputWorkersSubmit" type="submit" value="Confirm" />
		</div>
		<div class="footer"></div>
	</div>
	</form>
	
	<div id="resourceUsers" class="contentBox">
		<h3 class="header"><span class="textLabel">Other players on this island</span></h3>
		
		<div class="content">
			<table cellpadding="0" cellspacing="0">
				<thead>
					<tr>
						<th>Player
							<a href="?view=resource&type=resource&id=3565&sortBy=name&order=asc" class="unicode">&uArr;</a>
							<a href="?view=resource&type=resource&id=3565&sortBy=name&order=desc" class="unicode">&dArr;</a>
						</th>
						<th>Town</th>
						<th>Level</th>
						<th>Workers</th>
						<th>Donated
							<a href="?view=resource&type=resource&id=3565&sortBy=donation&order=asc" class="unicode">&uArr;</a>
							<a href="?view=resource&type=resource&id=3565&sortBy=donation&order=desc" class="unicode">&dArr;</a>
						</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
					{foreach from=$towns item=town name=town}
					{assign var='class' value='avatar'}
					{if $smarty.foreach.town.index % 2 == 0}
					{assign var='class' value="`$class` alt"}
					{/if}
					{if $town.owner == $resource.owner}
					{assign var='class' value="`$class` own"}
					{/if}
					<tr class="{$class}">
						<td class="ownerName">{$town.player}</td>
						<td class="cityName">{$town.town}</td>
						<td class="cityLevel">Level {$town.level}</td>
						<td class="cityWorkers">{$town.workers|number_format} Workers</td>
						<td class="ownerDonation">{$town.donated|number_format} <img src="http://static.ikariem.org/img/resources/icon_wood.gif" width="25" height="20" alt="Building material" /></td>
						<td class="actions">{if $town.owner == $resource.owner}&nbsp;{else}<a href="/diplomacyAdvisor/sendIKMessage?receiverId={$town.owner}"><img src="http://static.ikariem.org/img/interface/icon_message_write.gif" alt="Write message" /></a>{/if}</td>
					</tr>
					{/foreach}
				</tbody>
			</table>
		</div>
		<div class="footer"></div>
	</div>
</div>
<script type="text/javascript">
{literal}create_slider({{/literal}
	dir : 'ltr',
	id : "default",
	maxValue : {$maxWorkers},
	overcharge : {if $island.overload}{$overload}{else}0{/if},
	iniValue : {$resource.workers},
	bg : "sliderbg",
	thumb : "sliderthumb",
	topConstraint: -10,
	bottomConstraint: 344,
	bg_value : "actualValue",
	bg_overcharge : "overcharge",
	textfield:"inputWorkers"
{literal}});
Event.onDOMReady(function() {{/literal}
	var slider = sliders["default"];
	var res = new resourceStack({literal}{{/literal}
		container : "resiconcontainer",
		resourceicon : "resicon",
		width : 140
	{literal}});
	res.setIcons(Math.floor(slider.actualValue/(1+0.05*slider.actualValue)));
	slider.subscribe("valueChange", function() {
		res.setIcons(Math.floor(slider.actualValue/(1+0.05*slider.actualValue)));
	});
	var startSlider = slider.actualValue;
	var valueWorkers = Dom.get("valueWorkers");
	var valueCitizens = Dom.get("valueCitizens");
	var valueResource = Dom.get("valueResource");
	var valueWorkCosts = Dom.get("valueWorkCosts");
	var inputWorkersSubmit = Dom.get("inputWorkersSubmit");
	
	slider.flagSliderMoved =0;
	slider.subscribe("valueChange", function() {{/literal}
		var startCitizens = {$resource.freeCitizens};
		var startResourceWorkers = {$resource.workers};
		var startIncomePerTimeUnit = {$resource.income};
		this.flagSliderMoved = 1;
		//res.setIcons(Math.round(slider.actualValue/(1+0.05*slider.actualValue)));
		valueWorkers.innerHTML = locaNumberFormat(slider.actualValue);
		valueCitizens.innerHTML = locaNumberFormat(startCitizens+startResourceWorkers - slider.actualValue);
		var valRes = {$resource.corruption} * {$smarty.const.SPEED} * (Math.min({$maxWorkers}, slider.actualValue) + Math.max(0, 0.25 * (slider.actualValue-{$maxWorkers})));
		valueResource.innerHTML = '+' + Math.floor(valRes);
		valueWorkCosts.innerHTML = startIncomePerTimeUnit  - 3*(slider.actualValue-startSlider);
	{literal}});
	slider.subscribe("slideEnd", function() {
		if (this.flagSliderMoved) {
			inputWorkersSubmit.className = 'buttonChanged';
		}
	});
});{/literal}
</script>