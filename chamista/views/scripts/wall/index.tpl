{include file="dynamics/buildingUpgrade.tpl"}

<div id="mainview"> 
    <div class="buildingDescription"> 
        <h1>Town wall</h1> 
        {if isset($construction)}
        {include file="dynamics/upgradeProgress.tpl"}
        {else}
         <p>{$building_desc}</p>
        {/if} 
    </div> 
		
    <div class="contentBox01h"> 
        <h3 class="header">Information</h3> 
        <div class="content"> 
        	<div class="bgWall"> 
        		<div id="wallInfoBox"> 
		        	<div class="infoBoxHeader"></div> 
		        	<div class="infoBoxContent"> 
			        	<div class="weapon"> 
				        	<div class="weaponName">{$wall_info.combat}</div> 
				        	<span class="textLabel">Damage</span><b>{$wall_info.damage}</b> 
				        	<span class="textLabel">Accuracy</span> 
				        	<div class="damageFocusContainer" title="{$wall_info.accuracy*100}%"> 
				        		<div class="damageFocus" style="width: {$wall_info.accuracy*100}%;"></div> 
                        	</div> 
                        </div> 
			        	<span class="textLabel">Hit points</span><b>{$wall_info.hitpoints}</b><br/> 
			        	<span class="textLabel">Armour</span><b>{$wall_info.armor}</b><br/> 
			        	
			        	<span class="textLabel">Garrison limit</span><b>1300</b><br/>	       
		        	</div> 
		        	<div class="infoBoxFooter"></div> 
	        	</div>	        	
	        </div> 
	    </div> 
        <div class="footer"></div> 
    </div> 
</div> 