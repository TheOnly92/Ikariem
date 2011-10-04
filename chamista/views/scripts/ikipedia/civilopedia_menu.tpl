<div id="civilopedia_menu" class="dynamic" style="margin-bottom:10px;"> 
	<h3 class="header">Ikipedia</h3> 
	<div class="main">		
		<ul> 
			<li>
				<a title="Learn more about FAQ - Buildings, Building Material and Population..." href="?view=informations&articleId=10007&mainId=10007">FAQ - Buildings, Building Material and Population</a> 
			</li>
			<li>
				<a title="Learn more about First Steps - What is a Browsergame?..." href="?view=informations&articleId=10027&mainId=10027">First Steps - What is a Browsergame?</a> 
			</li> 
			<li>
				<a title="Learn more about Navigation..." href="?view=informations&articleId=10005&mainId=10005">Navigation</a> 
			</li> 
			<li>
				<a title="Learn more about Population and Satisfaction..." href="?view=informations&articleId=10006&mainId=10006">Population and Satisfaction</a> 
			</li> 
			<li>
				<a title="Learn more about Gold and Finances..." href="?view=informations&articleId=10011&mainId=10011">Gold and Finances</a> 
			</li> 
			<li>
				<a title="Learn more about Resources - Building Material..." href="?view=informations&articleId=10012&mainId=10012">Resources - Building Material</a> 
			</li> 
			<li>
				<a title="Learn more about Resources - Luxury Goods..." href="?view=informations&articleId=10013&mainId=10013">Resources - Luxury Goods</a> 
			</li> 
			<li>
				<a title="Learn more about Art of warfare..." href="?view=informations&articleId=10036&mainId=10036">Art of warfare</a> 
			</li> 
			<li>
				<a title="Learn more about Espionage..." href="?view=informations&articleId=10021&mainId=10021">Espionage</a> 
			</li> 
			<li>
				<a title="Learn more about Alliances and Treaties..." href="?view=informations&articleId=10022&mainId=10022">Alliances and Treaties</a> 
			</li> 
			<li>
				<a title="Learn more about Religion..." href="?view=informations&articleId=10030&mainId=10030">Religion</a> 
			</li> 
			<li><a title="Learn more about Researches..." href="/ikipedia/researchDetail">Researches</a></li> 
			{if isset($researches)}
			<li> 
				<ul> 
					<ul> 
						<li><strong>Seafaring:</strong></li> 
						{foreach from=$researches.seafaring item=seafaring}
						<li><a title="Learn more about {$seafaring.name}..." href="/ikipedia/researchDetail?researchId={$seafaring.id}">{$seafaring.name}</a></li>
						{/foreach}
					</ul> 
					<ul> 
						<li><strong>Economy:</strong></li> 
						{foreach from=$researches.economy item=economy}
						<li><a title="Learn more about {$economy.name}..." href="/ikipedia/researchDetail?researchId={$economy.id}">{$economy.name}</a></li>
						{/foreach}
					</ul> 
					<ul> 
						<li><strong>Science:</strong></li> 
						{foreach from=$researches.science item=science}
						<li><a title="Learn more about {$science.name}..." href="/ikipedia/researchDetail?researchId={$science.id}">{$science.name}</a></li>
						{/foreach}
					</ul> 
					<ul> 
						<li><strong>Military:</strong></li> 
						{foreach from=$researches.military item=military}
						<li><a title="Learn more about {$military.name}..." href="/ikipedia/researchDetail?researchId={$military.id}">{$military.name}</a></li>
						{/foreach}
					</ul> 
				</ul>
			</li> 
			{/if}
			<li><a title="Learn more about Units..." href="/ikipedia/unitDescription">Units</a></li>
			{if isset($units)}
			<li>
				<ul>
					{foreach from=$units item=unit}
					<li><a title="Learn more about {$unit.name}..." href="/ikipedia/unitDescription?unitId={$unit.id}">{$unit.name}</a></li>
					{/foreach}
				</ul>
			</li>
			{/if}
			<li><a title="Learn more about Ships..." href="/ikipedia/shipDescription">Ships</a></li> 
			{if isset($ships)}
			<li>
				<ul>
					{foreach from=$ships item=unit}
					<li><a title="Learn more about {$unit.name}..." href="/ikipedia/shipDescription?shipId={$unit.id}">{$unit.name}</a></li>
					{/foreach}
				</ul>
			</li>
			{/if}
			<li><a title="Learn more about Building..." href="?view=buildingDetail&buildingId=4">Building</a></li> 
			<li><a title="Learn more about Wonders..." href="?view=wonderDetail&wonderId=1">Wonders</a></li> 
		</ul> 
	</div> 
	<div class="footer"></div> 
</div> 