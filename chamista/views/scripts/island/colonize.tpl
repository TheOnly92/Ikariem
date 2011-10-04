<script type="text/javascript" src="/js/transportController.js"></script>
<script type="text/javascript">{literal}    
var transporterDisplay;
Event.onDOMReady(function() {transporterDisplay = new transportController({/literal}{$colonize.ships}{literal}, 500, Dom.get("transporterCount"), parseInt(Dom.get("peopleInput").value)+1250);});
{/literal}</script>

<!---------------------------------------------------------------------------------------
     ////////////////////////////////////////////////////////////////////////////////////
     ///////////////////////////// dynamic side-boxes. //////////////////////////////////
     //////////////////////////////////////////////////////////////////////////////////// -->

<div id="backTo" class="dynamic">
	<h3 class="header">Colonise</h3>
	<div class="content">
		<a href="/island?id={$island_id}" title="Back to the island">
			<img src="http://static.ikariem.org/img/img/action_back.gif" width="160" height="100" />
			<span class="textLabel">&lt;&lt; Back to the island</span>
		</a>
	</div>
	
	<div class="footer"></div>
</div>
				
			  

<!---------------------------------------------------------------------------------------
	 ////////////////////////////////////////////////////////////////////////////////////
	 ///////////////// the main view. take care that it stretches. //////////////////////
	 //////////////////////////////////////////////////////////////////////////////////// -->
<div id="mainview">

<div class="buildingDescription">
	<h1>Colonise</h1>
	<p>This place seems perfect for a new town! A shallow section of the coast provides access to the ocean and the fertile green hills in the area can feed a lot of people.</p>
</div>

{if count($colonize.error) == 0}
<form id="transport" onsubmit="checkTransporters()" action="/transport/startColonization?id={$island_id}&desiredPosition={$position}" method="post">
<input type="hidden" id="peopleInput" name="cargo_people" value="40" />
<input type="hidden" id="goldInput" name="cargo_gold" value="9000" />
<input type="hidden" id="resourceInput" name="cargo_resource" value="1250" />
<input type="hidden" id="tradegood1Input" name="cargo_tradegood1" value="0" />
<input type="hidden" id="tradegood2Input" name="cargo_tradegood2" value="0" />
<input type="hidden" id="tradegood3Input" name="cargo_tradegood3" value="0" />
<input type="hidden" id="tradegood4Input" name="cargo_tradegood4" value="0" />
{/if}
<div id="createColony" class="contentBox01h" style="z-index:50">
	<h3 class="header">Found a colony on Roditia</h3>
	<div class="content">
	
		<p>You can <em>found a colony</em> here. Colonies are towns just as like your capital, however they are governed by your capital. The <em>capital's palace's level</em> determines how many colonies you can own. So in order to found many towns, you must expand your palace!</p>
		{if $colonize.palace == 0}
		<div class="noPalace">
			<img src="http://static.ikariem.org/img/buildings/y100/palace.gif">
			<h4>Palace needed!</h4>
			
			<p>You can only found a colony, if build a <strong>palace</strong>! <em>Prepare this step well, as a palace is expensive and there will be high costs afterwards for every new colony!</em></p>
		</div>
		{else}
		<div class="costs">
			<img src="http://static.ikariem.org/img/img/colony_build.jpg">
			<p>The founding of a colony needs:</p>
			
			<ul class="resources">
				<li class="citizens"><span class="textLabel">Citizens: </span>40</li>
				<li class="gold"><span class="textLabel">Gold: </span>9,000</li>
				<li class="wood"><span class="textLabel">Building material: </span>1,250</li>
			</ul>
		</div>

		{if count($colonize.error) == 0}
		<p>You can also send along additional resources, if you want to give your colony more for a start.:</p>
		<ul class="resourceAssign">
			<li class="wood">
				<label for="textfield_resource">Send along building material::</label>
				
				<div class="sliderinput">
					<div class="sliderbg" id="sliderbg_resource">
						<div class="actualValue" id="actualValue_resource"></div>
						<div class="sliderthumb" id="sliderthumb_resource"></div>  
					</div>
					<script type="text/javascript">{literal}
					create_slider({{/literal}
						dir : 'ltr',
						id : "slider_resource",
						maxValue : {$colonize.max.wood},
						overcharge : 0,
						iniValue : 0,
						bg : "sliderbg_resource",
						thumb : "sliderthumb_resource",
						topConstraint: -10,
						bottomConstraint: 326,
						bg_value : "actualValue_resource",
						textfield:"textfield_resource"
					{literal}});
					Event.onDOMReady(function() {
						var slider = sliders["slider_resource"];
						slider.UpdateField1 = Dom.get("resourceInput");
						slider.subscribe("valueChange", function() {
							updateColonizeSummary('resource', slider.actualValue);
						});
						slider.subscribe("slideEnd", function() {
							slider.UpdateField1.value = 1250+slider.actualValue;
						});
						transporterDisplay.registerSlider(slider);
					});
					{/literal}</script>
					<a class="setMin" href="#reset" onClick="setColonizeMinValue('slider_resource'); return false;" title="Reset entry"><span class="textLabel">min</span></a>
					<a class="setMax" href="#max" onClick="setColonizeMaxValue('slider_resource'); return false;" title="Send along everything"><span class="textLabel">max</span></a>
					
				</div>
				<input class="textfield" id="textfield_resource" type="text" name="sendresource" value="0" size="4" maxlength="9">
			</li>
			<li class="wine">
				<label for="textfield_wine">Send along wine::</label>
				<div class="sliderinput">
					<div class="sliderbg" id="sliderbg_wine">
						<div class="actualValue" id="actualValue_wine"></div>
						<div class="sliderthumb" id="sliderthumb_wine"></div>  
					</div>
					<script type="text/javascript">{literal}
					create_slider({{/literal}
						dir : 'ltr',
						id : "slider_wine",
						maxValue : {$colonize.max.wine},
						overcharge : 0,
						iniValue : 0,
						bg : "sliderbg_wine",
						thumb : "sliderthumb_wine",
						topConstraint: -10,
						bottomConstraint: 326,
						bg_value : "actualValue_wine",
						textfield:"textfield_wine"
					{literal}});
					Event.onDOMReady(function() {
						var slider = sliders["slider_wine"];
						slider.UpdateField1 = Dom.get("tradegood1Input");
						slider.subscribe("valueChange", function() {
							updateColonizeSummary('wine', slider.actualValue);
						});
						slider.subscribe("slideEnd", function() {
							slider.UpdateField1.value = slider.actualValue;
						});
						transporterDisplay.registerSlider(slider);
					});
					{/literal}</script>
					<a class="setMin" href="#reset" onClick="setColonizeMinValue('slider_wine'); return false;" title="Reset entry"><span class="textLabel">min</span></a>
					<a class="setMax" href="#max" onClick="setColonizeMaxValue('slider_wine'); return false;" title="Send along everything"><span class="textLabel">max</span></a>
				</div>
				<input class="textfield" id="textfield_wine" type="text" name="sendwine"  value="0" size="4" maxlength="9">
			</li>
			
			<li class="marble">
				<label for="textfield_marble">Send along marble::</label>
				<div class="sliderinput">
					<div class="sliderbg" id="sliderbg_marble">
						<div class="actualValue" id="actualValue_marble"></div>
						<div class="sliderthumb" id="sliderthumb_marble"></div>  
					</div>
					<script type="text/javascript">{literal}
					create_slider({{/literal}
						dir : 'ltr',
						id : "slider_marble",
						maxValue : {$colonize.max.marble},
						overcharge : 0,
						iniValue : 0,
						bg : "sliderbg_marble",
						thumb : "sliderthumb_marble",
						topConstraint: -10,
						bottomConstraint: 326,
						bg_value : "actualValue_marble",
						textfield:"textfield_marble"
					{literal}});
					Event.onDOMReady(function() {
						var slider = sliders["slider_marble"];
						slider.UpdateField1 = Dom.get("tradegood2Input");
						slider.subscribe("valueChange", function() {
							updateColonizeSummary('marble', slider.actualValue);
						});
						slider.subscribe("slideEnd", function() {
							slider.UpdateField1.value = slider.actualValue;
						});
						transporterDisplay.registerSlider(slider);
					});
					{/literal}</script>
					
					<a class="setMin" href="#reset" onClick="setColonizeMinValue('slider_marble'); return false;" title="Reset entry"><span class="textLabel">min</span></a>
					<a class="setMax" href="#max" onClick="setColonizeMaxValue('slider_marble'); return false;" title="Send along everything"><span class="textLabel">max</span></a>
				</div>
				<input class="textfield" id="textfield_marble" type="text" name="sendmarble"  value="0" size="4" maxlength="9">
			</li>
			<li class="glass">
				<label for="textfield_crystal">Send along crystal glass::</label>
				
				<div class="sliderinput">
					<div class="sliderbg" id="sliderbg_crystal">
						<div class="actualValue" id="actualValue_crystal"></div>
						<div class="sliderthumb" id="sliderthumb_crystal"></div>  
					</div>
					<script type="text/javascript">{literal}
					create_slider({{/literal}
						dir : 'ltr',
						id : "slider_crystal",
						maxValue : {$colonize.max.crystal},
						overcharge : 0,
						iniValue : 0,
						bg : "sliderbg_crystal",
						thumb : "sliderthumb_crystal",
						topConstraint: -10,
						bottomConstraint: 326,
						bg_value : "actualValue_crystal",
						textfield:"textfield_crystal"
					{literal}});
					Event.onDOMReady(function() {
						var slider = sliders["slider_crystal"];
						slider.UpdateField1 = Dom.get("tradegood3Input");
						slider.subscribe("valueChange", function() {
							updateColonizeSummary('crystal', slider.actualValue);
						});
						slider.subscribe("slideEnd", function() {
							slider.UpdateField1.value = slider.actualValue;
						});
						transporterDisplay.registerSlider(slider);
					});
					{/literal}</script>
					<a class="setMin" href="#reset" onClick="setColonizeMinValue('slider_crystal'); return false;" title="Reset entry"><span class="textLabel">min</span></a>
					<a class="setMax" href="#max" onClick="setColonizeMaxValue('slider_crystal'); return false;" title="Send along everything"><span class="textLabel">max</span></a>
				
				</div>
				<input class="textfield" id="textfield_crystal" type="text" name="sendcrystal"  value="0" size="4" maxlength="9">
			</li>
			<li class="sulfur">
				<label for="textfield_sulfur">Send along sulfur:</label>
				<div class="sliderinput">
					<div class="sliderbg" id="sliderbg_sulfur">
						<div class="actualValue" id="actualValue_sulfur"></div>
						<div class="sliderthumb" id="sliderthumb_sulfur"></div>  
					</div>
					<script type="text/javascript">{literal}
					create_slider({{/literal}
						dir : 'ltr',
						id : "slider_sulfur",
						maxValue : {$colonize.max.sulfur},
						overcharge : 0,
						iniValue : 0,
						bg : "sliderbg_sulfur",
						thumb : "sliderthumb_sulfur",
						topConstraint: -10,
						bottomConstraint: 326,
						bg_value : "actualValue_sulfur",
						textfield:"textfield_sulfur"
					{literal}});
					Event.onDOMReady(function() {
						var slider = sliders["slider_sulfur"];
						slider.UpdateField1 = Dom.get("tradegood4Input");
						
						slider.subscribe("valueChange", function() {
							updateColonizeSummary('sulfur', slider.actualValue);
						});
						slider.subscribe("slideEnd", function() {
							//alert('dudu');
							slider.UpdateField1.value = slider.actualValue;
						});
						transporterDisplay.registerSlider(slider);
					});
					{/literal}</script>
					<a class="setMin" href="#reset" onClick="setColonizeMinValue('slider_sulfur'); return false;" title="Reset entry"><span class="textLabel">min</span></a>
					<a class="setMax" href="#max" onClick="setColonizeMaxValue('slider_sulfur'); return false;" title="Send along everything"><span class="textLabel">max</span></a>
				</div>
				<input class="textfield" id="textfield_sulfur" type="text" name="sendsulfur"  value="0" size="4" maxlength="9">
			</li>
			
			<li>
				<script type="text/javascript">{literal}
				function setColonizeMinValue(sName) {
					sliders[sName].setActualValue(0);
					transporterDisplay.sliderEnd();
				}
				function setColonizeMaxValue(sName) {
					maxLoadableVal = transporterDisplay.getMaxLoadable(sliders[sName]);
					sliders[sName].setActualValue(maxLoadableVal);
					transporterDisplay.sliderEnd();
				}
						
				var colonizeSummaries =new Array();
				colonizeSummaries['resource'] = 0;
				colonizeSummaries['wine'] = 0;
				colonizeSummaries['marble'] = 0;
				colonizeSummaries['crystal'] = 0;
				colonizeSummaries['sulfur'] = 0;
				function updateColonizeSummary(sName, sVal) {
					colonizeSummaries[sName] = sVal;
					var sum =  colonizeSummaries['resource'];
					sum +=  colonizeSummaries['wine'];
					sum +=  colonizeSummaries['marble'];
					sum +=  colonizeSummaries['crystal'];
					sum +=  colonizeSummaries['sulfur'];
					Dom.get('sendSummary').innerHTML = sum + '/{/literal}{$colonize.cargo}{literal}';
				}
				{/literal}</script>
				<div class="summaryText">Free loading capacity:</div>		
				<div class="summary" id="sendSummary">0/{$colonize.cargo}</div>
			</li>
		</ul>
		<hr />
		<div id="missionSummary">
		
			<div class="common">
				<div class="journeyTarget"><span class="textLabel">Destination:: </span>{$colonize.destination.name}</div>
				<div class="journeyTime"><span class="textLabel">Travel time: </span>{$colonize.destination.time}</div>
			</div>
			<div class="transporters">
				<span class="textLabel">Trade ships: </span>
				
				<span><input id="transporterCount" name="transporters" size="3" maxlength="3" readonly="readonly" value="3" /> / {$colonize.ships}</span>
			</div>
		</div>
		<div class="centerButton">
			<input id="colonizeBtn" class="button" type="submit" value="Plant colony!">
		</div>
		{else}
		<div class="errors">
			<h4>You haven't met all the requirements needed to found a colony: </h4>
			<ul>
				{foreach from=$colonize.error item=error}
				<li><span>{if $error.no < 4}You don't have enough {if $error.no == 1}citizens{elseif $error.no == 2}golds{elseif $error.no == 3}building materials{/if}! You still need <strong>{$error.lack|number_format} {if $error.no == 1}citizens{elseif $error.no == 2}golds{elseif $error.no == 3}building materials{/if}</strong>
				{if $error.no == 1}<br /><em>Attention: Workers and Scientists are not counted! Maybe you could withdraw some workers or scientists, to get the amount of citizens you need?</em>{/if}
				{else}
				You already have {$error.colonies} colonies and a palace level {$colonize.palace}! Expand the palace in your home town!
				{/if}</span></li>
				{/foreach}
			</ul>
		</div>
		{/if}
		{/if}
	</div><!--end .content -->
	<div class="footer"></div>
</div><!-- end .contentBox01 -->
{if count($colonize.error) == 0}</form>{/if}
 </div><!-- mainview -->
