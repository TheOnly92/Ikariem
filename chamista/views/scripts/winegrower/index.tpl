{include file="dynamics/buildingUpgrade.tpl"}

<div id="information" class="dynamic">
    <h3 class="header">in level {$build_lvl+1}</h3>
	<div class="content">
        <table width="100%">
            <tr>

    	        <th class="center"><b>Resource</b></th>
    	        <th class="center"><b>Bonus</b></th>
            </tr>
            <tr>
                <td class="center"><img src="http://static.ikariem.org/img/resources/icon_wine.gif" /></td>
                <td class="center">+{$nextBonus}%</td>
            </tr>

        </table>
    </div>
    <div class="footer"></div>
</div>

<div id="mainview">
    <div id="bonusBuilding">
    	<div class="buildingDescription">
    		<h1>Winery</h1>
    		{if isset($construction)}
			{include file="dynamics/upgradeProgress.tpl"}
			{else}
    		<p>{$building_desc}</p>
    		{/if}
    	</div>
    	<div class="contentBox01h">
	<div class="buildingPictureImg"><img src="http://static.ikariem.org/img/img/city/small/building_winegrower.gif"/></div>
	<h3 class="header"><span class="textLabel">Wine production</span></h3>
	<div class="content">
		<table cellspacing="0" cellspacing="0" border="0" style="margin:0 auto 0px;">
			<colgroup><col width="150"/><col width="70"/><col width="%"/></colgroup>
			<tr>
				<th class='brownHeader' colspan="3"></th>
			</tr>
			<tr>
				<td class="col1Style">
					<label>Basic Production:</label>
				</td>
				<td class="col2Style">
					<span title="Basic Production">{$winegrower.rawProduction|number_format:2}</span>
				</td>
				<td class="col3Style">
					<div class="green" style="width:{math equation="99-x" x=$winegrower.bonus}%" title="100.00%"></div>
				</td>
			</tr>
			<tr class='alt'>
				<td class="col1Style">
					<label>Winery:</label>
				</td>
				<td class="col2Style">
					<span title="Winery">{$winegrower.bonusProduction|number_format:2}</span>
				</td>
				<td class="col3Style">
					<div class="yellow" style="width:{$winegrower.bonus}%" title="+{$winegrower.bonus}.00%"></div>
				</td>
			</tr>
			<tr class="buildingResult">
				<td class="col1Style">
					<img src="http://static.ikariem.org/img/layout/sigma.gif"/>
				</td>
				<td class="col2Style">
					<span title="Total"><b>{$winegrower.totalProduction|number_format:2}</b></span>
				</td>
				<td class="col3Style">
					<div class="green" style="width:99%" title="{math equation="100+x" x=$winegrower.bonus}.00%"></div>
				</td>
			</tr>
		</table>
	</div>
	<div class="footer"></div>
	</div>
	</div>
</div>
