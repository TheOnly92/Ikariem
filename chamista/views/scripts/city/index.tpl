<!-- -------------------------------------------------------------------------------------
	 ////////////////////////////////////////////////////////////////////////////////////
	 ///////////////////////////// dynamic side-boxes. //////////////////////////////////
	 //////////////////////////////////////////////////////////////////////////////////// -->
<div id="information" class="dynamic" style="z-index:1;">
	<h3 class="header">Town</h3>
	<div class="content">
		<ul class="cityinfo">
			<li class="name"><span class="textLabel">Name: </span>{$This_Town->town_name}</li>
			<li class="citylevel"><span class="textLabel">Town size: </span>{$This_Town->town_lvl}</li>
			<div class="centerButton">
				<a href="/city/military?type=army&id={$town_id}" class="button">Troops in town</a>
			</div>
		</ul>
	</div><!-- end content -->
	<div class="footer"></div>
</div>
<div class="dynamic" id="reportInboxLeft">
	<h3 class="header">Building construction list</h3>
	
	<div class="content">
		<img width="203" height="85" src="http://static.ikariem.org/img/research/area_economy.jpg"/>
		<p>A Premium account is required in order to use the building construction list.</p>
		<div class="centerButton">
			<a href="?view=premium" class="button">Ikariem PLUS</a>
		</div>
	</div>
	<div class="footer"></div>
</div>
<!---------------------------------------------------------------------------------------
	 ////////////////////////////////////////////////////////////////////////////////////
	 ///////////////// the main view. take care that it stretches. //////////////////////
	 //////////////////////////////////////////////////////////////////////////////////// -->
<div id="mainview" class="phase1">
	<ul id="locations">
		{foreach from=$grounds item=ground name=grounds}
		<li id="position{$smarty.foreach.grounds.index}" class="{$ground.class}">
		<div class="{$ground.divclass}"></div>
		<a href="{$ground.href}" title="{$ground.title}"><span class="textLabel">{$ground.title}</span></a>
		{if isset($ground.construction)}
		<div class="timetofinish">
			<span class="before"></span><span class="textLabel">Time till completion: </span><span id="cityCountdown">{$ground.construction.cityCountDown}</span><span class="after"></span>
		</div>
		<script type="text/javascript">
		{literal}
		var tmpCnt = getCountdown({{/literal}
			enddate: {$ground.construction.enddate},
			currentdate: {$ground.construction.currentdate},
			el: "cityCountdown"
		{literal}});
		tmpCnt.subscribe("finished", function() {{/literal}
			top.document.title = "Ikariem";
			setTimeout(function() {literal}{{/literal}
				location.href="/city?id={$town_id}";
				{literal}}{/literal},2000);
		{literal}});
		{/literal}
		</script>
		{/if}
		</li>
		{/foreach}
		{if $growth > 1}
		<li class="beachboys"></li>
		{/if}
		{if $loading == true}
		<li class="transporter"></li>
		{/if}
	</ul>
	{literal}
	<!--[if lt IE 7]>
	<style type="text/css">
	#city #container #mainview #locations .garnison,
	#city #container #mainview #locations .garnisonGate1,
	#city #container #mainview #locations .garnisonGate2,
	#city #container #mainview #locations .garnisonCenter,
	#city #container #mainview #locations .garnisonOutpost
	{
		background-image:url(/img/img/city/garnison.gif);
	}
	</style>
	<![endif]-->
	{/literal}
</div><!-- END mainview -->