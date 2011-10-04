<!---------------------------------------------------------------------------------------
     ////////////////////////////////////////////////////////////////////////////////////
     ///////////////////////////// dynamic side-boxes. //////////////////////////////////
     //////////////////////////////////////////////////////////////////////////////////// -->
{include file="dynamics/buildingUpgrade.tpl"}
<div id="researchLibrary" class="dynamic">
	<h3 class="header">Library</h3>
	<div class="content">
		<img src="http://static.ikariem.org/img/research/img_library.jpg" width="203" height="85" />
		<p>In the library you will find information for all areas of research!</p>
		<div class="centerButton">
			<a href="/researchAdvisor/researchOverview" class="button">To the Library!</a>
		</div>
	</div>
	<div class="footer"></div>
</div>

<!---------------------------------------------------------------------------------------
     ////////////////////////////////////////////////////////////////////////////////////
     ///////////////// the main view. take care that it stretches. //////////////////////
     //////////////////////////////////////////////////////////////////////////////////// -->
<div id="mainview">

	<div class="buildingDescription">
		<h1>Academy</h1>
		{if isset($construction)}
		{include file="dynamics/upgradeProgress.tpl"}
		{else}
		<p>{$building_desc}</p>
		{/if}
	</div>
	<form id="setScientists" action="/academy/setScientists?position={$position}&cityId={$town_id}" method="post">
		<div class="contentBox01h">
			<h3 class="header"><span class="textLabel">Assign Workers</span></h3>
			<div class="content">
			
				<ul>
					<li class="citizens"><span class="textLabel">Citizens: </span><span class="value" id="valueCitizens">{$academy.freeCitizens}</span></li>
					<li class="scientists"><span class="textLabel">Scientists: </span><span class="value" id="valueWorkers">{$academy.scientists}</span></li>
					<li class="gain">
					<span class="textLabel">Research achievement: </span>
					<div id="gainPoints">
					<img id="lightbulb" src="http://static.ikariem.org/img/layout/bulb-on.gif" width="14" height="21" />
					</div>
					<div style="position:absolute; top:22px; left:145px;">
					<span id="valueResearch" class="positive overcharged">+{$academy.researchPoints}</span> <span class="timeUnit">per Hour</span>
					</div>
					</li>
					<li class="costs"><span class="textLabel">Income of the town: </span>
					<span id="valueWorkCosts" class="{if $academy.goldProduction > 0}positive{else}negative{/if}">{$academy.goldProduction}</span>
					<img src="http://static.ikariem.org/img/resources/icon_gold.gif" title="Gold" alt="Gold" /><span class="timeUnit"> per Hour</span></li>
				</ul>
				<div id="overchargeMsg" class="status nooc ocready oced">Overloaded!</div>
				<div class="slider" id="sliderbg">
					<div class="actualValue" id="actualValue"></div>
					<div class="overcharge" id="overcharge"></div>
					<div id="sliderthumb"></div>
				</div>
				<a class="setMin" href="#reset" onClick="sliders['default'].setActualValue(0); return false;" title="No Scientists"><span class="textLabel">min</span></a>
				<a class="setMax" href="#max" onClick="sliders['default'].setActualValue({$maxValue}); return false;" title="maximum amount of Scientists"><span class="textLabel">max</span></a>
				<input autocomplete="off" id="inputScientists" name="s" class="textfield" type="text" maxlength="4" />
				<div class="centerButton">
					<input type="submit" id="inputWorkersSubmit" class="button" value="Confirm" />
				</div>
			</div>
			<div class="footer"></div>
		
		</div>
	</form>

</div><!-- mainview -->

<script type="text/javascript">{literal}
create_slider({{/literal}
    id : "default",
      dir : 'ltr',
      maxValue : {$maxValue},
      overcharge : 0,
      iniValue : {$academy.scientists},
      bg : "sliderbg",
      thumb : "sliderthumb",
      topConstraint: -10,
      bottomConstraint: 344,
      bg_value : "actualValue",
      bg_overcharge : "overcharge",
      textfield:"inputScientists"
  {literal}});
Event.onDOMReady(function() {
  var resIconDisplay;
  var slider = sliders["default"];
  resIconDisplay = new resourceStack({
      container : "gainPoints",
      resourceicon : "lightbulb",
      width : 140
      });
  resIconDisplay.setIcons(Math.floor(slider.actualValue*1));
  var startSlider = slider.actualValue;
  slider.subscribe("valueChange", function() {{/literal}
      resIconDisplay.setIcons(Math.floor(slider.actualValue*1));

      var startCitizens = {$academy.freeCitizens};
      var startScientists = {$academy.scientists};
      var startIncomePerTimeUnit = {$academy.goldProduction};
      flagSliderMoved = 1;

      //res.setIcons(Math.round(slider.actualValue/(1+0.05*slider.actualValue)));
      Dom.get("valueWorkers").innerHTML = locaNumberFormat(slider.actualValue);
      Dom.get("valueCitizens").innerHTML = locaNumberFormat(startCitizens+startScientists - slider.actualValue);
      var valRes = 1*slider.actualValue;
      Dom.get("valueResearch").innerHTML = '+' + Math.floor(valRes);
      Dom.get("valueWorkCosts").innerHTML = startIncomePerTimeUnit  - 9*(slider.actualValue-startSlider);
  {literal}});

  var flagSliderMoved =0;
  slider.subscribe("slideEnd", function() {
      if (flagSliderMoved) {
          Dom.get('inputWorkersSubmit').className = 'buttonChanged';
      }
   });
  });{/literal}
</script>