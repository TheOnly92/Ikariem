{include file="ikipedia/civilopedia_menu.tpl"}

<!---------------------------------------------------------------------------------------
	 ////////////////////////////////////////////////////////////////////////////////////
	 ///////////////// the main view. take care that it stretches. //////////////////////
	 //////////////////////////////////////////////////////////////////////////////////// --> 
<div id="mainview"> 
	<h1>Unit description</h1> 
	<div class="contentBox01h"> 
		<h3 class="header"><span class="textLabel">{$unitDescription.name}</span></h3> 
		<div class="content"> 
			<div id="unit" class="s{$unitDescription.sid}"> 
				<div id="unitRes"> 
					<ul class="resources"> 
						<li class="wood firstpos" title="Building material"><span class="textLabel">'Building material': </span>{$unitDescription.wood|number_format}</li>
						{if $unitDescription.sulfur > 0}
						<li class="sulfur secondpos" title="Sulfur"><span class="textLabel">'Sulfur': </span>{$unitDescription.sulfur|number_format}</li>
						{/if} 
						{if $unitDescription.crystal > 0}
						<li class="glass secondpos" title="Crystal Glass"><span class="textLabel">'Crystal Glass': </span>{$unitDescription.crystal|number_format}</li>
						{/if}
						{if $unitDescription.wine > 0}
						<li class="wine secondpos" title="Wine"><span class="textLabel">'Wine': </span>{$unitDescription.wine|number_format}</li>
						{/if}
						<li class="citizens thirdpos" title="Citizens"><span class="textLabel">'Citizens': </span>{$unitDescription.citizen|number_format}</li> 
						<li class="upkeep fourthpos" title="Upkeep"><span class="textLabel">'Upkeep': </span>{$unitDescription.upkeep|number_format}</li> 
						<li class="building_level sixthpos" title="Level"><span class="textLabel">'Level': </span>{$unitDescription.barracks|number_format}</li> 
						<li class="completionTime seventhpos" title="Building time"><span class="textLabel">'Building time': </span>{$unitDescription.training}</li>
					</ul> 
				</div> 
			</div> 
			<div id="infoBox"> 
				<div class="infoBoxHeader"></div> 
				<div class="infoBoxContent"> 
					<h3>{$unitDescription.class}</h3> 
					<span class="textLabel">Hit points:</span><b>{$unitDescription.hp}</b><br/> 
					<span class="textLabel">Armour:</span><b>{if $unitDescription.armor > 0}{$unitDescription.armor}{else}-{/if}</b><br/> 
					<span class="textLabel">Speed:</span>{$unitDescription.speed}<br/> 
					<span class="textLabel">Size:</span>{$unitDescription.size}<br/> 
					<div class="weapon"> 
						<div class="weaponName">{$unitDescription.attack.primary.name}</div> 
						<span class="textLabel">Damage:</span>{$unitDescription.attack.primary.damage}<br/> 
						<span class="textLabel">Accuracy:</span> 
						<div class="damageFocusContainer"> 
							<div class="damageFocus" style="width: {$unitDescription.attack.primary.accuracy}%"></div> 
						</div> 
						{if $unitDescription.attack.primary.munition > 0}
						<span class="textLabel">Munition:</span> {$unitDescription.attack.primary.munition}
						{/if}
					</div> 
					{if $unitDescription.attack.secondary}
					<div class="weapon"> 
						<div class="weaponName">{$unitDescription.attack.secondary.name}</div> 
						<span class="textLabel">Damage:</span>{$unitDescription.attack.secondary.damage}<br/> 
						<span class="textLabel">Accuracy:</span> 
						<div class="damageFocusContainer"> 
							<div class="damageFocus" style="width: {$unitDescription.attack.secondary.accuracy}%"></div> 
						</div> 
						{if $unitDescription.attack.secondary.munition > 0}
						<span class="textLabel">Munition:</span> {$unitDescription.attack.secondary.munition}
						{/if}
					</div>
					{/if}
					<span class="req">Requirement(s)</span> 
					{if $unitDescription.requirements.tech.required != 0}
					<span class="{if $unitDescription.requirements.tech.user == 0}notAvailable{else}available{/if}">* <a href="/ikipedia/researchDetail?researchId={$unitDescription.requirements.tech.details.id}">{$unitDescription.requirements.tech.details.name}</a></span>
					{/if}
				</div> 
				<div class="infoBoxFooter"></div> 
			</div> 
			<div class="shortdesc"> 
				<h4 style="font-weight: bold; padding-bottom: 10px; color: #B03937;">{$unitDescription.name}</h4>
				{$unitDescription.desc} 
			</div>
		</div><!-- content --> 
		<div class="footer"></div> 
	</div>
</div> 