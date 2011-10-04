<!---------------------------------------------------------------------------------------
	 ////////////////////////////////////////////////////////////////////////////////////
	 ///////////////////////////// dynamic side-boxes. //////////////////////////////////
	 //////////////////////////////////////////////////////////////////////////////////// -->
<div id="backTo" class="dynamic">
	<h3 class="header">Trade fleet</h3>
	<div class="content">
		<a href="/city?id={$merchantNavy.town_id}" title="Back to the town">
		<img src="http://static.ikariem.org/img/img/action_back.gif" width="160" height="100" />
		<span class="textLabel">Back to the town</span></a>
	</div>
	<div class="footer"></div>
</div>

<div id="backTo" class="dynamic">
	<h3 class="header">To the island</h3>
	<div class="content">
		<div class="centerButton">
			<a class="button" href="/island?id={$merchantNavy.island_id}" title="Back to the island view">
				<span class="textLabel">Back to the island view</span>
			</a>
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
		<h1>Trade fleet</h1>
		<p>Here you will get an overview of all your trade fleets that are currently on their way over the seven seas.</p>
	</div>
	{if count($merchantNavy.goods) == 0 && count($merchantNavy.troops) == 0}
	<div class="contentBox">
		<h3 class="header">No transports</h3>
		<div class="content">
			<p>There are currently no trade ships on the way</p>
		</div>
		<div class="footer"></div>
	</div>
	{elseif count($merchantNavy.goods) > 0}
	<div class="contentBox">
		<h3 class="header">Goods transports</h3>
		<div class="content">
			<table cellpadding="0" cellspacing="0">
				<tr>
					<th class="transports"><img src="http://static.ikariem.org/img/characters/fleet/40x40/ship_transport_r_40x40.gif" width="40" height="40" title="Number of transport ships" alt="Quantity" /></th>
					<th class="source">Start</th>

					<th class="mission">Mission</th>
					<th class="target">Target</th>
					<th class="eta">Arrival</th>
					<th class="return">Return</th>
					<th class="actions">Actions</th>
				</tr>
				{foreach from=$merchantNavy.goods item=transport}
				<tr>
					<td class="transports">{$transport.quantity}</td>
					<td class="source"><a href="/island?cityId={$transport.source.id}">{$transport.source.name}</a></td>
					<td class="mission {$transport.mission.class}">{$transport.mission.name}</td>
					<td class="target"><a href="/island?cityId={$transport.target.id}">{$transport.target.name}</a> {if count($transport.target.owner) > 0 && $transport.target.owner.id > 0}<a href="/diplomacyAdvisor/sendIKMessage/receiverId/{$transport.target.owner.id}">({$transport.target.owner.name})</a>{/if}</td>
					<td id="eta{$transport.id}" class="eta">{$transport.eta.title}</td>
					<td id="ret{$transport.id}" class="return">{$transport.ret.title}</td>
					<td class="actions"></td>
				</tr>
				<tr>
					<td colspan="7" class="pulldown">
						<div class="pulldown">
							<div class="content">
								<table cellpadding="0" cellspacing="0">
									<tr>
										<td>
											<div class="payload">
												<span class="textLabel">Cargo:</span>{if count($transport.cargo.goods) > 0}{foreach from=$transport.cargo.goods item=good key=resource}{section name=good start=1 loop=$good.show}<img src="http://static.ikariem.org/img/resources/icon_{$resource}.gif" width="{$good.width}" height="20" title="{$good.total|number_format} {$good.name}" alt="{$good.name}"{if $smarty.section.good.index > 1} style="margin-left:-{$good.margin}px"{/if} />{/section}{/foreach}
												{else}
												No goods on board
												{/if}
											</div>
										</td>
										<td>
											<div class="space"><img src="http://static.ikariem.org/img/layout/crate.gif" width="22" height="22" alt="Cargo space" title="Cargo space" /> {$transport.cargo.load|number_format} / {$transport.cargo.total|number_format}</div>
										</td>
									</tr>
								</table>
							</div>
							<div class="btn"></div>
						</div>
					</td>
				</tr>
				{if isset($transport.eta.enddate)}
				<script type="text/javascript">
				{literal}
				getCountdown({{/literal}
					enddate: {$transport.eta.enddate},
					currentdate: {$transport.eta.currentdate},
					el: "eta{$transport.id}"
				{literal}});
				{/literal}
				</script>
				{/if}
				{/foreach}
			</table>
		</div>
		<div class="footer"></div>
	</div>
	{elseif count($merchantNavy.troops) > 0}
	<div class="contentBox">
		<h3 class="header">Troop transports</h3>
		<div class="content">
			<table cellpadding="0" cellspacing="0">
				<tr>
					<th class="transports"><img src="http://static.ikariem.org/img/characters/fleet/40x40/ship_transport_r_40x40.gif" width="40" height="40" title="Number of transport ships" alt="Quantity" /></th>
					<th class="source">Start</th>

					<th class="mission">Mission</th>
					<th class="target">Target</th>
					<th class="eta">Arrival</th>
					<th class="return">Return</th>
					<th class="actions">Actions</th>
				</tr>
				{foreach from=$merchantNavy.troops item=transport}
				<tr>
					<td class="transports">{$transport.quantity}</td>
					<td class="source"><a href="/island?cityId={$transport.source.id}">{$transport.source.name}</a></td>
					<td class="mission {$transport.mission.class}">{$transport.mission.name}</td>
					{if $transport.target.id > 0}
					<td class="target"><a href="/island?cityId={$transport.target.id}">{$transport.target.name}</a> {if count($transport.target.owner) > 0}<a href="/diplomacyAdvisor/sendIKMessage/receiverId/{$transport.target.owner.id}">({$transport.target.owner.name})</a>{/if}</td>
					{else}
					<td class="target">{$transport.target.name} (-)</td>
					{/if}
					<td id="eta{$transport.id}" class="eta">{$transport.eta.title}</td>
					<td id="ret{$transport.id}" class="return">{$transport.ret.title}</td>
					<td class="actions"></td>
				</tr>
				<tr>
					<td colspan="7" class="pulldown">
						<div class="pulldown">
							<div class="content">
								<table cellpadding="0" cellspacing="0">
									<tr>
										<td>
											<div class="payload">
												<span class="textLabel">Cargo:</span>{if count($transport.cargo.goods) > 0}{foreach from=$transport.cargo.goods item=good key=resource}{section name=good start=1 loop=$good.show}<img src="http://static.ikariem.org/img/resources/icon_{$resource}.gif" width="{$good.width}" height="20" title="{$good.total|number_format} {$good.name}" alt="{$good.name}"{if $smarty.section.good.index > 1} style="margin-left:-{$good.margin}px"{/if} />{/section}{/foreach}
												{else}
												No goods on board
												{/if}
											</div>
										</td>
										<td>
											<div class="space"><img src="http://static.ikariem.org/img/layout/crate.gif" width="22" height="22" alt="Cargo space" title="Cargo space" /> {$transport.cargo.load|number_format} / {$transport.cargo.total|number_format}</div>
										</td>
									</tr>
								</table>
							</div>
							<div class="btn"></div>
						</div>
					</td>
				</tr>
				{if isset($transport.eta.enddate)}
				<script type="text/javascript">
				{literal}
				getCountdown({{/literal}
					enddate: {$transport.eta.enddate},
					currentdate: {$transport.eta.currentdate},
					el: "eta{$transport.id}"
				{literal}});
				{/literal}
				</script>
				{/if}
				{/foreach}
			</table>
		</div>
		<div class="footer"></div>
	</div>
	{/if}
</div>

{if count($merchantNavy.goods) > 0 || count($merchantNavy.troops) > 0}
<script type="text/javascript">{literal}
//right now this is used only on this screen. Could be easily abstracted and moved elsewhere though
Event.onDOMReady( function() {
	var pulldowns = Dom.getElementsByClassName("pulldown", 'div', "mainview");
	for(i=0; i<pulldowns.length; i++) {
				var children = Dom.getChildren(pulldowns[i]);
				children[0].contentHeight=children[0].offsetHeight;
				children[0].style.height='0px';
				children[0].style.position="relative";
				children[0].style.overflow="hidden";
		children[1].onmouseover=function(e) {
					this.style.background="url(/img/interface/pulldown_contentbox_hover.gif)";
				};
		children[1].onmouseout=function(e) {
					this.style.background="url(/img/interface/pulldown_contentbox.gif)";
				};
		children[1].onclick=function(e) {
					var pulldown = Dom.getChildren(this.parentNode)[0];
					if(pulldown.offsetHeight>0) pulldown.style.height="0px";
					else pulldown.style.height=pulldown.contentHeight+'px';
				};
	}
});
{/literal}
</script>
{/if}