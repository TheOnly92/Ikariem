<div class="dynamic" id="viewCityImperium">
	<h3 class="header">Builder`s overview</h3>
	<div class="content">
		<img src="http://static.ikariem.org/img/premium/sideAd_premiumTradeAdvisor.jpg" width="203" height="85" />
		<p>You have several colonies to administer? The <strong>Builder`s View</strong> gives you full control over all your towns!</p>
		<div class="centerButton">
			<a href="?view=premiumDetails" class="button">Have a look now!</a>
		</div>
	</div>
	<div class="footer"></div>
</div>

<div id="mainview">
	<div class="buildingDescription">
		<h1>Mayor</h1>
		<p></p>
	</div>
	<div class="contentBox01h">
		<h3 class="header">Current events ({$events_no})</h3>
		<div class="content">
			<table cellpadding="0" cellspacing="0" class="table01" id="inboxCity">
				<thead>
				<tr>
					<th></th>
					<th colspan="2">Location</th>

					<th>Date</th>
					<th>Subject</th>
					<th></th>
				</tr>
				</thead>
				<tbody>
				{foreach from=$events item=event name=event}
					{assign var='class' value=""}
					{if $smarty.foreach.event.index % 2 == 0}
						{assign var='class' value=' class="alt"'}
					{/if}
					<tr{$class}>
						{if $event.event_new}
						<td class="wichtig"></td>
						{else}
						<td class="empty"></td>
						{/if}
						<td class="city"></td>
						<td style="white-space:nowrap;"><a title="Jump to the town {$event.location_name}" href="/city?id={$event.location}">{$event.location_name}</a></td>
						<td class="date">{if $event.event_new}<b>{/if}{$event.date}{if $event.event_new}</b>{/if}</td>
						<td class="subject">{$event.subject}</td>
						<td class="empty"></td>
					</tr>
				{/foreach}
				{if $events_no > 10}
					<tr class="pgnt">
						<td class="empty"></td>
						<td></td>
						<td></td>
						<td colspan="i" class="paginator">
							{if $page > 1}
							<div class="last"><a href="/tradeAdvisor?page={$page-1}" title="...last 10"><img src="http://static.ikariem.org/img/img/resource/btn_min.gif" /></a></div>
							{/if}
							<div class="text" title="Reports {math equation="(x-1)*10+1" x=$page}-{$page*10}">{math equation="(x-1)*10+1" x=$page}-{$page*10}</div>
							{if $page * 10 < $events_no}
							<div class="next"><a href="/tradeAdvisor?page={$page+1}" title="next 10..."><img src="http://static.ikariem.org/img/img/resource/btn_max.gif" /></a></div>
							{/if}
						</td>
						<td></td>
						<td class="empty"></td>
					</tr>
				{/if}
				</tbody>
			</table>
		</div>
		<div class="footer"></div>
	</div>
</div>