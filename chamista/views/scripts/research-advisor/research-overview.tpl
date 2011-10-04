<div id="backTo" class="dynamic">
	<h3 class="header">Academy</h3>
	<div class="content">
		<a href="/academy?id={$town_id}&position={$academyPos}" title="Back to the Academy">
			<img src="http://static.ikariem.org/img/buildings/y100/academy.gif" width="160" height="100" />
			<span class="textLabel">&lt;&lt; Back to the Academy</span>
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
		<h1>Previous Research Achievements</h1>
		<p>All our previous research achievements have been archived at the library. An interested visitor can take his time here, to have a close look at all our research.</p>	
	</div><!-- end buildingDescription -->
	
	<div class="contentBox01h">
		<h3 class="header"><span class="textLabel">Achievements researched so far</span></h3>
		<div class="content">
			<h4>Seafaring</h4>
			<ul>
				{foreach from=$seafaring item=tech}
				<li class="{if $tech.explored}explored{else}unexplored{/if}">
					<a href="/ikipedia/researchDetail?researchId={$tech.id}" title="{$tech.name}">{$tech.name}</a>
				</li>
				{/foreach}
			</ul>
			<br/>
			<hr />
			<h4>Economy</h4>
			<ul>
				{foreach from=$economy item=tech}
				<li class="{if $tech.explored}explored{else}unexplored{/if}">
					<a href="/ikipedia/researchDetail?researchId={$tech.id}" title="{$tech.name}">{$tech.name}</a>
				</li>
				{/foreach}
			</ul>
			<br/>
			<hr />
			<h4>Science</h4>
			<ul>
				{foreach from=$science item=tech}
				<li class="{if $tech.explored}explored{else}unexplored{/if}">
					<a href="/ikipedia/researchDetail?researchId={$tech.id}" title="{$tech.name}">{$tech.name}</a>
				</li>
				{/foreach}
			</ul>
			<br/>
			<hr />
			<h4>Military</h4>
			<ul>
				{foreach from=$military item=tech}
				<li class="{if $tech.explored}explored{else}unexplored{/if}">
					<a href="/ikipedia/researchDetail?researchId={$tech.id}" title="{$tech.name}">{$tech.name}</a>
				</li>
				{/foreach}
			</ul>
		</div>
		<div class="footer"></div>
	</div>
</div><!-- mainview -->
