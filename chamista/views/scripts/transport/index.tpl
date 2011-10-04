<!---------------------------------------------------------------------------------------
     ////////////////////////////////////////////////////////////////////////////////////
     ///////////////////////////// dynamic side-boxes. //////////////////////////////////
     //////////////////////////////////////////////////////////////////////////////////// -->
<div id="backTo" class="dynamic" style="z-index:1">
	<h3 class="header">Island</h3>
	<div class="content">
		<a href="?view=island&id=1114&cityId=98433" title="Island">
		<img style="max-width:100%" src="http://static.ikariem.org/img/img/action_back.gif" width="160" height="100" />
		<span class="textLabel">&lt;&lt; Island</span></a>
	</div>
	<div class="footer"></div>
</div>
<!---------------------------------------------------------------------------------------
     ////////////////////////////////////////////////////////////////////////////////////
     ///////////////// the main view. take care that it stretches. //////////////////////
     //////////////////////////////////////////////////////////////////////////////////// -->
<div id="mainview">
	<div class="buildingDescription">
		<h1>Transport</h1>
		<p>Choose, which goods you want to transport and how large the cargo should be.</p>
	</div>
	<form id="transport" action="/transport/loadTransportersWithFreight?id={$transport.currCityInfo.id}&destinationCityId={$transport.destinationInfo.id}" method="POST">
	<div id="setPremiumTransports" class="contentBox">
		<h3 class="header">Mercenary transporter</h3>
		<div class="content">
			<p>Some seafaring man would sail to the end of the world to get a bit Ambrosia! You are able to charter transporters for 5 Ambrosia to sail with your fleet and increase your transport capacity significant.</p>
			<label for="textfield_premium">Charter mercenary transporter:</label>
			<p class="costs">The mercenaries want: <span id="ambrosiaCosts">5</span><img src="http://static.ikariem.org/img/premium/ambrosia_icon.gif" width="24" height="20" alt="Ambrosia" title="Ambrosia"/> (You have 0)</p>
			<p class="PremiumTransportsButton">
				<div class="centerButton">
					<a class="button" href="?view=premiumPayment&oldView=premium">Acquire Ambrosia</a>
				</div>
			</p>
		</div><!--end .content -->
		<div class="footer"></div>
	</div><!-- end .contentBox01 -->
	<div id="transportGoods" class="contentBox">
		<h3 class="header">Transport goods!</h3>
		<div class="content">
			<p>Choose the goods that you want to transport from {$transport.currCityInfo.name} to {$transport.destinationInfo.name}. Keep in mind how many trade ships you will need for this.</p>
			<ul class="resourceAssign">
				<li class="wood">
					<label for="textfield_resource">Send along building material:</label>
					<div class="sliderinput">
						<div class="sliderbg">
							<div class="actualValue valuebg"></div>
							<div class="sliderthumb" id="sliderthumb_wood"></div>
						</div>
						<a id="slider_wood_min" class="setMin" href="#reset" title="Reset entry"><span class="textLabel">min</span></a>
						<a id="slider_wood_max" class="setMax" href="#max" title="send along everything"><span class="textLabel">max</span></a>
					</div>
					<input class="textfield" id="textfield_wood" type="text" name="cargo_resource"  value="0" size="4" maxlength="9">
				</li>
				<li class="wine">
					<label for="textfield_resource">Send along wine:</label>
					<div class="sliderinput">
						<div class="sliderbg">
							<div class="actualValue valuebg"></div>
							<div class="sliderthumb" id="sliderthumb_wine"></div>
						</div>
						<a id="slider_wine_min" class="setMin" href="#reset" title="Reset entry"><span class="textLabel">min</span></a>
						<a id="slider_wine_max" class="setMax" href="#max" title="send along everything"><span class="textLabel">max</span></a>
					</div>
					<input class="textfield" id="textfield_wine" type="text" name="cargo_tradegood1"  value="0" size="4" maxlength="9">
				</li>
				<li class="marble">
					<label for="textfield_resource">Send along marble:</label>
						<div class="sliderinput">
						<div class="sliderbg">
							<div class="actualValue valuebg"></div>
							<div class="sliderthumb" id="sliderthumb_marble"></div>
						</div>
						<a id="slider_marble_min" class="setMin" href="#reset" title="Reset entry"><span class="textLabel">min</span></a>
						<a id="slider_marble_max" class="setMax" href="#max" title="send along everything"><span class="textLabel">max</span></a>
					</div>
					<input class="textfield" id="textfield_marble" type="text" name="cargo_tradegood2"  value="0" size="4" maxlength="9">
				</li>
				<li class="glass">
					<label for="textfield_resource">Send along crystal glass:</label>
					<div class="sliderinput">
						<div class="sliderbg">
							<div class="actualValue valuebg"></div>
							<div class="sliderthumb" id="sliderthumb_glass"></div>
						</div>
						<a id="slider_glass_min" class="setMin" href="#reset" title="Reset entry"><span class="textLabel">min</span></a>
						<a id="slider_glass_max" class="setMax" href="#max" title="send along everything"><span class="textLabel">max</span></a>
					</div>
					<input class="textfield" id="textfield_glass" type="text" name="cargo_tradegood3"  value="0" size="4" maxlength="9">
				</li>
				<li class="sulfur">
					<label for="textfield_resource">Send along sulfur</label>
					<div class="sliderinput">
						<div class="sliderbg">
							<div class="actualValue valuebg"></div>
							<div class="sliderthumb" id="sliderthumb_sulfur"></div>
						</div>
						<a id="slider_sulfur_min" class="setMin" href="#reset" title="Reset entry"><span class="textLabel">min</span></a>
						<a id="slider_sulfur_max" class="setMax" href="#max" title="send along everything"><span class="textLabel">max</span></a>
					</div>
					<input class="textfield" id="textfield_sulfur" type="text" name="cargo_tradegood4"  value="0" size="4" maxlength="9">
				</li>
			</ul>
			<hr />
			<div id="missionSummary">
				<div class="common">
					<div class="journeyTarget"><span class="textLabel">Destination: </span>{$transport.destinationInfo.name}</div>
					<div class="journeyTime"><span class="textLabel">Duration of journey: </span>{$transport.travelTime}</div>
				</div>
				<div class="transporters">
					<span class="textLabel"> </span>
					<span><input id="transporterCount" name="transporters" size="3" maxlength="3" readonly="readonly" value="0" /> / <span id="totalTansporters">{$transport.transporters}</span></span>
				</div>
			</div>
			<div class="centerButton">
				<input id="submit" class="button" type="submit" value="Transport goods!">
			</div>
		</div><!--end .content -->
		</form>
		<div class="footer"></div>
	</div><!-- end .contentBox01 -->
</div><!-- mainview -->

<script type="text/javascript">{literal}
var transporterCount = new transportController({{/literal}
	'availableTransporters' : {$transport.transporters},
	'capacityPerTransport' : 500,
	'spaceReserved' : 0
{literal}});
transporterCount.subscribe('usedTransChanged', function(v) {
	Dom.get('transporterCount').value=v;
});
transporterCount.subscribe('availTransChanged', function(v) {
	Dom.get('totalTansporters').innerHTML=v;
});

var iwood = new valueInput('textfield_wood', [0, {/literal}{$transport.currCityInfo.wood}{literal}]);
var swood = new Slider('sliderthumb_wood', {'from':iwood,'dir' : 'ltr'});
UIManager.connect(swood,iwood);
Event.addListener('slider_wood_min', 'click', function(ev){swood.setValue(swood.range[0]); Event.stopEvent(ev);});
Event.addListener('slider_wood_max', 'click', function(ev){swood.setValue(swood.range[1]); Event.stopEvent(ev);});
transporterCount.registerInput(swood);
var iwine = new valueInput('textfield_wine', [0, {/literal}{$transport.currCityInfo.wine}{literal}]);
var swine = new Slider('sliderthumb_wine', {'from':iwine,'dir' : 'ltr'});
UIManager.connect(swine,iwine);
Event.addListener('slider_wine_min', 'click', function(ev){swine.setValue(swine.range[0]); Event.stopEvent(ev);});
Event.addListener('slider_wine_max', 'click', function(ev){swine.setValue(swine.range[1]); Event.stopEvent(ev);});
transporterCount.registerInput(swine);
var imarble = new valueInput('textfield_marble', [0, {/literal}{$transport.currCityInfo.marble}{literal}]);
var smarble = new Slider('sliderthumb_marble', {'from':imarble,'dir' : 'ltr'});
UIManager.connect(smarble,imarble);
Event.addListener('slider_marble_min', 'click', function(ev){smarble.setValue(smarble.range[0]); Event.stopEvent(ev);});
Event.addListener('slider_marble_max', 'click', function(ev){smarble.setValue(smarble.range[1]); Event.stopEvent(ev);});
transporterCount.registerInput(smarble);
var iglass = new valueInput('textfield_glass', [0, {/literal}{$transport.currCityInfo.crystal}{literal}]);
var sglass = new Slider('sliderthumb_glass', {'from':iglass,'dir' : 'ltr'});
UIManager.connect(sglass,iglass);
Event.addListener('slider_glass_min', 'click', function(ev){sglass.setValue(sglass.range[0]); Event.stopEvent(ev);});
Event.addListener('slider_glass_max', 'click', function(ev){sglass.setValue(sglass.range[1]); Event.stopEvent(ev);});
transporterCount.registerInput(sglass);
var isulfur = new valueInput('textfield_sulfur', [0, {/literal}{$transport.currCityInfo.sulfur}{literal}]);
var ssulfur = new Slider('sliderthumb_sulfur', {'from':isulfur,'dir' : 'ltr'});
UIManager.connect(ssulfur,isulfur);
Event.addListener('slider_sulfur_min', 'click', function(ev){ssulfur.setValue(ssulfur.range[0]); Event.stopEvent(ev);});
Event.addListener('slider_sulfur_max', 'click', function(ev){ssulfur.setValue(ssulfur.range[1]); Event.stopEvent(ev);});
transporterCount.registerInput(ssulfur);
{/literal}
</script>
