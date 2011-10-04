<!---------------------------------------------------------------------------------------
	 ////////////////////////////////////////////////////////////////////////////////////
	 ///////////////////////////// dynamic side-boxes. //////////////////////////////////
	 //////////////////////////////////////////////////////////////////////////////////// -->

{include file="dynamics/buildingUpgrade.tpl"}

   <!---------------------------------------------------------------------------------------------------------------------------------------->
<div class="dynamic" id="reportInboxLeft">
	<h3 class="header">Info</h3>
	<div class="content">
		<p>You can train {$hideout.maxSpy|number_format}</p>

		<ul>
		<li>{$hideout.canTrain|number_format} are waiting on their training</li>
		<li>{$hideout.spiesAvail|number_format} are currently working in the defense</li>
		<li>{$hideout.inUse|number_format} are currently in use</li>
		</ul>
	</div>
	<div class="footer"></div>

</div>
<div id="unitConstructionList" class="dynamic">
	<h3 class="header">Construction queue <a class="help" href="?view=informations&articleId=10021&mainId=10021" title="Help">
	<span class="textLabel">Help</span></a></h3>
	<div class="content">
		{if count($training_current) > 0}
		<h4>In training:</h4>
		<div class="currentUnit Spy" >
			<div class="amount"><span class="textLabel">Recruit 1 spies</span></div>
			<div class="progressbar">
				<div class="bar" id="buildProgress"></div>
			</div>
			<div class="time" id="buildCountDown">
				{$training_current.remaining}
				<span class="textLabel">until completion</span>
			</div>
			<script type="text/javascript">{literal}
			Event.onDOMReady(function () {
				getProgressBar({{/literal}
								startdate: {$training_current.startdate},
								enddate: {$training_current.enddate},
								currentdate: {$training_current.currentdate},
								interval: 3,
								bar: "buildProgress"
								{literal}})
			});
			Event.onDOMReady(function () {
				getCountdown({{/literal}
								enddate: {$training_current.enddate},
								currentdate: {$training_current.currentdate},
								el: "buildCountDown"
								{literal}}, 3, " ", "", true, true)
			});{/literal}
			</script>
		</div>
		{/if}
	</div>
	<div class="footer">&nbsp;</div>
</div>
<!---------------------------------------------------------------------------------------------------------------------------------------->
<!---------------------------------------------------------------------------------------
	 ////////////////////////////////////////////////////////////////////////////////////
	 ///////////////// the main view. take care that it stretches. //////////////////////
	 //////////////////////////////////////////////////////////////////////////////////// -->

<div id="mainview">
	<div class="buildingDescription">
		<h1>Hideout</h1>
		<p>{$building_desc}</p>
	</div>
	<!-- end of .buildingDescription -->
	
	<div class="yui-navset">
		<ul class="yui-nav">
		<li{if $hideout.tab == 'general'} class="selected"{/if}><a href="/safehouse?id={$town_id}&position={$position}" title="Hideout"><em>Hideout</em></a></li>
		<li><a href="?view=safehouse&id=91112&position=5&tab=reports" title="Espionage reports"><em>Espionage reports</em></a></li>
		<li><a href="?view=safehouse&id=91112&position=5&tab=archive"><em>Archive</em></a></li>
		</ul>
	</div>
	{if $hideout.tab == 'general'}
	<form id="buildForm"  action="/safehouse/buildSpy?id={$town_id}&position={$position}" method="POST">
	<div class="contentBox01h">
	<h3 class="header"><span class="textLabel">Train spy</span></h3>
	<div class="content">
		<ul id="units">
			<li class="unit">
				<div class="unitinfo">
					<h4>Train spy</h4>
					<img src="http://static.ikariem.org/img/characters/military/120x100/spy_120x100.gif" />
					<p>This citizen is loyal and discreet. An ideal candidate for a spy.
						Training time for a spy :
					</p>
				</div>
				{if $hideout.trainable > 0}
				<!-- Spy training -->
				<div class="sliderinput">
					<div class="sliderbg" id="sliderbg_spy">
						<div class="actualValue" id="actualValue_spy"></div>
						<div class="sliderthumb" id="sliderthumb_spy"></div>
					</div>
					<script type="text/javascript">{literal}
					create_slider({{/literal}
						dir : 'ltr',
						id : "slider_spy",
						maxValue : {$hideout.trainable},
						overcharge : 0,
						iniValue : 0,
						bg : "sliderbg_spy",
						thumb : "sliderthumb_spy",
						topConstraint: -10,
						bottomConstraint: 326,
						bg_value : "actualValue_spy",
						textfield:"textfield_spy"
					{literal}});
					var slider = sliders["slider_spy"];
					
					this.activateButton = function() {
						document.getElementById('buttonBuildSpy').disabled=false;
						document.getElementById('buttonBuildSpy').setAttribute("class", "button");
					}
					this.deactivateButton = function() {
						document.getElementById('buttonBuildSpy').disabled=true;
						document.getElementById('buttonBuildSpy').setAttribute("class", "button_inactive");
					}
					
					Event.onDOMReady(function() {
						if(sliders["slider_spy"].actualValue == 0) {
							deactivateButton();
						} else {
							activateButton();
						}
						sliders["slider_spy"].subscribe("change", function() {
							if(sliders["slider_spy"].actualValue == 0) {
								 deactivateButton();
							} else {
								 activateButton();
							}
						})
					}){/literal}
					</script>
					<a class="setMin" href="#reset" onClick="sliders['slider_spy'].setActualValue(0); return false;" title="Reset entry"><span class="textLabel">min</span></a>
					<a class="setMax" href="#max" onClick="sliders['slider_spy'].setActualValue({$hideout.trainable}); return false;" title="Recruit as many as possible"><span class="textLabel">max</span></a>
				</div>
				{/if}
				<div class="forminput">
					{if $hideout.trainable == 0}
					Maximum number of spies has been reached!
					{else}
					<input id="textfield_spy" class="textfield" type="text" maxlength="3" size="4" value="0" name="textfield_spy">
					<input class="button" type="submit" disabled="disabled" value="Train spy" id="buttonBuildSpy">
					{/if}
				</div>
				<div class="costs">
					<h5>Costs:</h5>
					<ul class="resources">
						<li class="gold"><span class="textLabel">Gold: </span>
							150
						</li>
						<li class="glass"><span class="textLabel">Crystal Glass: </span>
							80
						</li>
						<li class="time">
							{$hideout.trainingTime}
						</li>
					</ul>
				</div>
			</li>
		</ul>
		</div>
		
		<div class="footer">&nbsp;</div>
	</div>
	</form>
	<!-- Spy infobox -->
	<div class="contentBox01h">
		<h3 class="header"><span class="textLabel">Spy on mission</span></h3>
		<div class="content">
		<!-- dummy -->
		{foreach from=$spies item=spy}
		<div class="spyinfo">
			<ul>
				<li title="Residence" class="city">
					<a href="/city?id={$spy.townId}">{$spy.destinationTown} ({$spy.destinationPos.x},{$spy.destinationPos.y})</a>
				</li>
				<li title="Status" class="status">{$spy.mission}</li>
				{if $spy.missionId != 0}
				<li title="Status" class="status">
					<script type="text/javascript">
					{literal}
						Event.onDOMReady(function() {
							getCountdown({
								enddate: {/literal}{$spy.arrivalTime}{literal},
								currentdate: {/literal}{$smarty.now}{literal},
								el: "SpyCountDown{/literal}{$spy.id}{literal}"
							}, 3, " ", "", true, true);
						});
					{/literal}
					</script>
					<div class="time"><span class="textLabel">Arrival</span> <span id="SpyCountDown{$spy.id}">{$spy.countDown}</span></div>
				</li>
				{/if}
				<li class="risk"><span class="textLabel">Risk of discovery</span>:<br />
					<div class="statusBar">
						<div style="width: {$spy.risk}%" class="bar"></div>
					</div>
					<div class="percentage">{$spy.risk}%</div>
				</li>
				<!-- Mission Buttons -->
				{if $spy.missionId == 0}
				<div class="missionButton"><a title="Send your spy an assignment" href="/safehouse/missions?id={$town_id}&position={$position}&spy={$spy.id}">Mission</a></div>
				<div class="missionAbort"><a title="Recall spy to his home town" href="/sendSpy/executeMission?id={$town.id}&position={$position}&spy={$spy.id}&mission=3">Withdraw</a></div>
				{/if}
			</ul>
		</div>
		{foreachelse}
		<!-- Individual spy profile -->
		<div style="padding:10px;">None of your spies is currently on a mission!</div>
		{/foreach}
		</div>
		<div class="footer"></div>
	</div>
	<div style="height:150px"></div><br />
	{/if}
</div>
