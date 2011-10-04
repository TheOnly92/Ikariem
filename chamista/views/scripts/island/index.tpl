<div id="infocontainer" class="dynamic">
	<h3 class="header">Info</h3>
	<div id="information" class="content">
		<div class="accesshint">Attention: This area of the page will be updated when you select a town!</div>
	</div>
	<div class="footer"></div>
</div>
<div id="actioncontainer" class="dynamic"><h3 class="header">Actions</h3>
	<div id="actions" class="content">
		<div class="accesshint">Attention: This area of the page will be updated when you select a town!</div>
	</div>
	<div class="footer"></div>
</div>

<!-- the main view. take care that it stretches. -->
<div id="mainview" class="island{$island.island_type}">
	<h3>Towns on {$island.island_name}</h3>
	<ul id="cities">
		{section name=cities start=0 loop=16 step=1}
		{if isset($towns[$smarty.section.cities.index])}
		{assign var=town value=`$towns[$smarty.section.cities.index]`}
		<li id="cityLocation{$smarty.section.cities.index}" class="cityLocation city level{$town.town_lvl}">
			{if $town.town_lvl > 0}
			<div class="cityimg"></div>
			{else}
			<div class="buildCityImg"></div>
			{/if}
			<div class="selectimg"></div>
			{if $town.town_lvl > 0}
			<a href="#" id="city_{$town.town_id}" onclick="selectCity({$smarty.section.cities.index},{$town.town_id},{if $town.town_uid == $self_id || $town.has_spy == true}1{else}0{/if}); selectGroup.activate(this, 'cities'); return false;">
				<span class="textLabel">
					<span class="before"></span>
					{if $town.vacation}
					<span class="palm"></span>
					<span class="vacation" title="The player is currently on vacation">{$town.town_name} (v)</span>
					{else}
					{$town.town_name}
					{/if}
					<span class="after"></span>
				</span>
			</a>
			{/if}
			{if isset($selectCity) && $selectCity == $town.town_id}
			<script type="text/javascript">{literal}
			Event.onDOMReady(function() {
				selectCity(1, {/literal}{$town.town_id}{literal}, 0); selectGroup.activate(document.getElementById("city_{/literal}{$town.town_id}{literal}"), 'cities');
			});
			{/literal}</script>
			{/if}
			<ul class="cityinfo">
				<li class="name"><span class="textLabel">Name: </span>{$town.town_name}</li>
				<li class="citylevel"><span class="textLabel">Town size: </span>{$town.town_lvl}</li>
				<li class="owner">
					<span class="textLabel">Player: </span>{$town.owner_name}
					{if $town.town_uid != $self_id}
					<a href="/diplomacyAdvisor/sendIKMessage?receiverId={$town.town_uid}" class="messageSend" title="Send Message"><img src="http://static.ikariem.org/img/interface/icon_message_write.gif" alt="Send Message"/></a>
					{/if}
					<a href="/reportPlayer?usr_nick={$town.owner_name}&usr_id={$town.town_uid}&target={$town.town_id}" class="reportPlayer" title="Report this player to a game operator..."><img src="http://static.ikariem.org/img/layout/icon-kick.gif" alt="Report player" /></a>
				</li>
				<li class="name"><span class="textLabel">Points: </span>{$town.highscore|number_format}</li>
				<li class="ally"><span class="textLabel">Ally: </span>-</li>
				{if $town.has_spy == true}
				<li class="spy"><span class="textLabel">You have a spy in this town!</span></li>
				{/if}
			</ul>
			<ul class="cityactions">
				{if $town.town_uid != $self_id}
				<li class="diplomacy"><a href="/diplomacyAdvisor/sendIKMessage?receiverId={$town.town_uid}" title="Diplomacy"><span class="textLabel">Diplomacy</span></a></li>
				{/if}
				{if $currCity != $town.town_id}
				<li class="transport"><a href="/transport?destinationCityId={$town.town_id}" title="Transport goods"><span class="textLabel">Transport goods</span></a></li>
				{/if}
				{if $espionage.show == true}
				{if $espionage.enabled == true}
				<li class="espionage"><a href="/sendSpy?destinationCityId={$town.town_id}&islandId={$island.island_id}" title="Send out spy"><span class="textLabel">Send out spy</span></a></li>
				{else}
				<li class="espionage disabled" title="No spies available!"><span class="textLabel">Send out spy</span></li>
				{/if}
				{/if}
			</ul>
		</li>
		{else}
		<li id="cityLocation{$smarty.section.cities.index}" class="cityLocation buildplace">
			<div class="claim"></div>
			<a href="/island/colonize?id={$island.island_id}&position={$smarty.section.cities.index}" title="Would you like to colonize here?"><span class="textLabel"><span class="before"></span>Building ground<span class="after"></span></span></a>
		</li>
		{/if}
		{/section}
		{if $island.isCapital}
		<li id="barbarianVillage">
			<a href="#" id="barbarianLink" onclick="selectBarbarianVillage(); selectGroup.activate(this, 'cities'); return false;"> </a>
			<ul class="cityinfo" id="barbarianInformation">
				<li class="name"><span class="textLabel">Name: </span>Barbarian Village</li>
				<li class="citylevel"><span class="textLabel">Level: </span>{$island.barbarian.level}</li>
				<li class="name"><span class="textLabel">Barbarians: </span>{$island.barbarian.barbarians}</li>
				{if $island.barbarian.wall > 0}
				<li class="name"><span class="textLabel">Wall level: </span>{$island.barbarian.wall}</li>
				{/if}
			</ul>
			<ul class="cityactions" id="barbarianActions">
				{if $island.hasTroops}
				<li class="plunder"><a href="/plunder?destinationCityId=0&barbarianVillage=1" title="Pillage"><span class="textLabel">Pillage</span></a></li>
				{else}
				<li class="plunder disabled" title="No troops/transporters available for pillaging!"><span class="textLabel">Pillage</span></li>
				{/if}
			</ul>
		</li>
		{/if}
	</ul>
	<h3>Special places on {$island.island_name}</h3>
	<ul id="islandfeatures">
		<li class="wood level{$island.island_saw_lvl}">
			<a href="/island/resource?id={$island.island_id}" title="Forest {$island.island_saw_lvl}">
				<span class="textLabel">Forest</span>
			</a>
		</li>
		<li id="tradegood"  class="{$island.trade_plain} level{$island.island_resource_lvl}">
			<a class="" href="/island/tradegood?id={$island.island_id}" title="{$island.trade_mine} {$island.island_resource_lvl}">
				<span class="textLabel">{$island.trade_mine}</span>
			</a>
		</li>
		<li id="wonder" class="wonder{$island.island_wonder}">
			<a href="?view=wonder&id={$island.island_id}" title="Ares` stronghold"><span class="textLabel">Ares` stronghold</span></a>
		</li>
		<li class="forum"><a title="Agora" href="?view=islandBoard&id={$island.island_id}"><span class="textLabel">Agora</span></a></li>
	</ul>
</div>