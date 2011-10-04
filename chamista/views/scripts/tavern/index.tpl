<script type="text/javascript">

classValuePerSatisfaction = new Array();
classNamePerSatisfaction = new Array();
classValuePerSatisfaction[0] = 300; 
classNamePerSatisfaction[0] = 'ecstatic'; 
classValuePerSatisfaction[1] = 50; 
classNamePerSatisfaction[1] = 'happy'; 
classValuePerSatisfaction[2] = 0; 
classNamePerSatisfaction[2] = 'neutral'; 
classValuePerSatisfaction[3] = -50; 
classNamePerSatisfaction[3] = 'sad'; 
classValuePerSatisfaction[4] = -1000; 
classNamePerSatisfaction[4] = 'outraged'; 

satPerWine = new Array();

savedWine = new Array();

satPerWine[0] = 0;
savedWine[0] = '&nbsp;';
{foreach from=$tavern.serve key=k item=serve name=serve}
satPerWine[{$smarty.foreach.serve.index+1}] = {$serve*15};
savedWine[{$smarty.foreach.serve.index+1}] = '&nbsp;';
{/foreach}

</script>

<!---------------------------------------------------------------------------------------
     ////////////////////////////////////////////////////////////////////////////////////
     ///////////////////////////// dynamic side-boxes. //////////////////////////////////
     //////////////////////////////////////////////////////////////////////////////////// -->
{include file="dynamics/buildingUpgrade.tpl"}

<!---------------------------------------------------------------------------------------
     ////////////////////////////////////////////////////////////////////////////////////
     ///////////////// the main view. take care that it stretches. //////////////////////
     //////////////////////////////////////////////////////////////////////////////////// -->
<div id="mainview">
	<div class="buildingDescription">
		<h1>Tavern</h1>
		{if isset($construction)}
		{include file="dynamics/upgradeProgress.tpl"}
		{else}
		<p>{$building_desc}</p>
		{/if}
	</div><!-- end of .buildingDescription -->
	
	<div class="contentBox01h">
		<h3 class="header"><span class="textLabel">Serving of drinks</span></h3>
		<div class="content">
			<form id="wineAssignForm" action="/tavern/assignWinePerTick?id={$town_id}&position={$position}" method="POST">
			<ul id="units">	
				<li class="unit">
					<div class="unitinfo">
						<h4>Serve wine</h4>
						<img src="http://static.ikariem.org/img/resources/wine-big.gif" style="margin-left:10px;" />
						<p>You can determine exactly how much wine is served to your population. The more wine you provide, the happier your citizens will be. 
						Attention: Your innkeeper demands an hour`s ration every time you change the rationing.</p>
					</div>
					<div class="sliderinput">
						<div id="sliderbg_wine" class="sliderbg" title="slider value = 0">
							<div id="actualValue_wine" class="actualValue" style="clip: rect(0px, 10px, auto, 0px);"></div>
							<div id="sliderthumb_wine" class="sliderthumb" style="left: 0px; top: 0px;"></div>  
						</div>
						<script type="text/javascript">{literal}
						create_slider({
							dir : 'ltr',
							id : "slider_wine",
							maxValue : {/literal}{$build_lvl}{literal},
							overcharge : 0,
							iniValue : {/literal}{$tavern.serving}{literal},
							bg : "sliderbg_wine",
							thumb : "sliderthumb_wine",
							topConstraint: -10,
							bottomConstraint: 326,
							bg_value : "actualValue_wine",
							textfield:"wineAmount"
						});
						
						Event.onDOMReady(function() {
							var slider = sliders["slider_wine"];
							
							var startSatisfaction = {/literal}{$happiness}{literal};
							slider.subscribe("valueChange", function() {
								var val = classValuePerSatisfaction.length-1;
								
								for (n=0;n<5;n++) {
									if (classValuePerSatisfaction[n] <= (startSatisfaction + 60*slider.actualValue)) {
										val = n;
										break;
									}
								}
								window.status = startSatisfaction + 60*slider.actualValue;
								Dom.get('citySatisfaction').className = classNamePerSatisfaction[val];
								if(satPerWine[slider.actualValue]) {
									slider.UpdateField1.innerHTML = satPerWine[slider.actualValue];
									slider.UpdateField2.innerHTML = savedWine[slider.actualValue];
								} else {
									slider.UpdateField1.innerHTML = "0";
									slider.UpdateField2.innerHTML = "&nbsp;"
								}
							});
							
							slider.UpdateField1 = Dom.get("bonus");
							slider.UpdateField1.innerHTML = satPerWine[slider.actualValue];
							
							slider.UpdateField2 = Dom.get("savedWine");
							slider.UpdateField2.innerHTML = savedWine[slider.actualValue];
							
							//slider.subscribe("slideEnd", function() {
							//});
						});{/literal}
						</script>
						<a class="setMin" href="#reset" onClick="sliders['slider_wine'].setActualValue(0); return false;" title="Reset entry"><span class="textLabel">min</span></a>
						<a class="setMax" href="#max" onClick="sliders['slider_wine'].setActualValue({$build_lvl}); return false;" title="Serve as much as possible"><span class="textLabel">max</span></a>
					</div><!-- end .sliderinput -->
					<div class="forminput">
						<a title="Serve as much as possible" onclick="sliders['slider_wine'].setActualValue({$build_lvl}); return false;" href="#max" class="setMax"><span class="textLabel">max</span></a>
						<div class="centerButton">
							<input type="submit" value="Cheers!" class="button"/>
						</div>
						<div id="citySatisfaction" class="{$satisfaction_img}"></div>
					</div>
					<div id="serve" class="textfield">
						<select id="wineAmount" name="amount" size="1">
							<option value="0" {if $tavern.serving == 0}selected="selected"{/if}>No wine</option>
							{foreach from=$tavern.serve item=serve key=k}
							<option value="{$k}"{if $tavern.serving == $k} selected="selected"{/if}>{$serve} Wine per hour</option>
							{/foreach}
						</select>
						<span class="bonus">+<span id="bonus" class="value">40</span> Satisfied citizens</span>
						<br/>
						<span class="savedWine"><span id="savedWine"></span></span>
					</div>
					<!--
					<div class="satisfaction"><span>Current satisfaction:</span> 66</div>
					-->
				</li>
			</ul>
			</form>
		</div>
		<div class="footer"></div>
	</div>
</div>
