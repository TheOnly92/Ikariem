<div id="buildingUpgrade" class="dynamic upgrading"> 
	<h3 class="header">Upgrade <a class="help" href="?view=buildingDetail&buildingId=3" title="Help"><span class="textLabel">Need help?</span></a></h3> 
	<div class="content"> 
		<div class="isUpgrading">Is being expanded!</div> 
		<div class="buildingLevel"><span class="textLabel">Level </span>{$construction.build_lvl}</div> 
		<div class="nextLevel"><span class="textLabel">Next Level </span>{$construction.build_lvl+1}</div> 
		<div class="progressBar"><div class="bar" id="upgradeProgress" title="{$construction.progress}%" style="width:{$construction.progress}%;"></div></div> 
			<script type="text/javascript"> 
			{literal}
			Event.onDOMReady(function() {
				var tmppbar = getProgressBar( {{/literal}startdate: {$construction.startdate}, enddate: {$construction.enddate}, currentdate: {$construction.currentdate},	bar: "upgradeProgress"{literal}} );
				//display percentage in title of progressbar
				tmppbar.subscribe("update", function(){ this.barEl.title=this.progress+"%"; });
				tmppbar.subscribe("finished", function(){ this.barEl.title="100%"; });
				});
			{/literal}
			</script> 
		<div class="time" id="upgradeCountDown">{$construction.upgradeCountDown}</div> 
			<script type="text/javascript"> 
			{literal}
			Event.onDOMReady(function() {
				var tmpCnt = getCountdown({{/literal}enddate: {$construction.enddate},currentdate: {$construction.currentdate},el: "upgradeCountDown"{literal}}, 2, " ", "", true, true);
				//load building page 2000ms after finishing
				tmpCnt.subscribe("finished", function() {
					setTimeout(function() { location.href="{/literal}/{$construction.building}?id={$town_id}&position={$position}{literal}";},2000); });
				});
			{/literal}
			</script> 
		<a class="cancelUpgrade" href="/buildingsDemolition?id={$town_id}&position={$position}" title="Abort"><span class="textLabel">Abort</span></a> 
	</div> 
	<div class="footer"></div> 
</div> 