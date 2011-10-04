{include file="dynamics/buildingUpgrade.tpl"}

<div id="information" class="dynamic">
    <h3 class="header">in level {$build_lvl+1}</h3>
	<div class="content">
        <table width="100%">
            <tr>

    	        <th class="center"><b>Resource</b></th>
    	        <th class="center"><b>Costs</b></th>
            </tr>
            <tr>
                <td class="center"><img src="http://static.ikariem.org/img/resources/icon_wood.gif" /></td>
                <td class="center">-{$nextDiscount}.00%</td>
            </tr>

        </table>
    </div>
    <div class="footer"></div>
</div>

<div id="mainview">
    <div id="reductionBuilding">
    	<div class="buildingDescription">
    		<h1>Carpenter</h1>
    		{if isset($construction)}
			{include file="dynamics/upgradeProgress.tpl"}
			{else}
    		<p>{$building_desc}</p>
    		{/if}
    	</div>
    	
		<div class="contentBox01h">
			<div class="buildingPictureImg"><img src="http://static.ikariem.org/img/img/city/small/building_carpentering.gif" /></div>
			<h3 class="header"><span class="textLabel">Carpenter`s Statistics Building</span></h3>
			<div class="content">
				<table cellspacing="0" cellspacing="0" border="0" style="margin:0 auto 0px;">
					<tr>
						<th class='brownHeader' colspan="3"></th>
					</tr>
					<tr >
						<td class="col1Style">Basic Costs:</td>
						<td class="col2Style"><span title="Total"><b>100.00%</b></span></td>
						<td class="col3Style"><div class="brownBarDiv barBorder" style="width:99%" title="Costs"></div></td>
					</tr>
					<tr class="alt">
						<td class="col1Style"><b>-</b> Researches ({$buildings.researches}.00%):</td>
						<td class="col2Style"><span title="Total"><b>{$buildings.researches_per}.00%</b></span></td>
						<td class="col3Style">
							<div class="greenBarDiv barBorder" style="width:99%" title="Costs">
								<div class="brownBarDiv" style="width:{$buildings.researches_per}%" title="Costs"></div>
							</div>
						</td>
					</tr>
					<tr>
						<td class="col1Style"><b>-</b> Carpenter ({$buildings.carpenter}.00%):</td>
						<td class="col2Style"><span title="Total"><b>{$buildings.carpenter_per}.00%</b></span></td>
						<td class="col3Style">
							<div class="greenBarDiv barBorder" style="width:{$buildings.researches_width}%" title="Costs">
								<div class="brownBarDiv" style="width:{math equation="(100-x)" x=$buildings.carpenter y=$buildings.researches_width}%" title="Costs"></div>
							</div>
						</td>
					</tr>
				</table>
			</div>
			<div class="footer"></div>
		</div>
		
		<div class="contentBox01h">
			<div class="buildingPictureImg"><img src="http://static.ikariem.org/img/img/city/small/building_carpentering.gif" /></div>
			<h3 class="header"><span class="textLabel">Carpenter`s Statistics Units</span></h3>
			<div class="content">
				<table cellspacing="0" cellspacing="0" border="0" style="margin:0 auto 0px;">
					<tr>
						<th class='brownHeader' colspan="3"></th>
					</tr>
					<tr >
						<td class="col1Style">Basic Costs:</td>
						<td class="col2Style">
						<span title="Total"><b>100.00%</b></span>
						</td>
						<td class="col3Style">
							<div class="brownBarDiv barBorder" style="width:99%" title="Costs"></div>
						</td>
					</tr>
					<tr class="alt">
						<td class="col1Style"><b>-</b> Carpenter ({$units.carpenter}.00%):</td>
						<td class="col2Style">
							<span title="Total"><b>{$units.carpenter_per}.00%</b></span>
						</td>
						<td class="col3Style">
							<div class="greenBarDiv barBorder" style="width:99%" title="Costs">
								<div class="brownBarDiv" style="width:{$units.carpenter_per}%" title="Costs"></div>
							</div>
						</td>
					</tr>
				</table>
			</div>
			<div class="footer"></div>
		</div>
		
		<div class="contentBox01h">
			<div class="buildingPictureImg"><img src="http://static.ikariem.org/img/img/city/small/building_carpentering.gif" /></div>
			<h3 class="header"><span class="textLabel">Carpenter`s Statistics Ships</span></h3>
			<div class="content">
				<table cellspacing="0" cellspacing="0" border="0" style="margin:0 auto 0px;">
					<tr>
						<th class='brownHeader' colspan="3"></th>
					</tr>
					<tr >
						<td class="col1Style">Basic Costs:</td>
						<td class="col2Style">
							<span title="Total"><b>100.00%</b></span>
						</td>
						<td class="col3Style">
							<div class="brownBarDiv barBorder" style="width:99%" title="Costs"></div>
						</td>
					</tr>
					<tr class="alt">
						<td class="col1Style"><b>-</b> Carpenter ({$units.carpenter}.00%):</td>
						<td class="col2Style">
							<span title="Total"><b>{$units.carpenter_per}.00%</b></span>
						</td>
						<td class="col3Style">
							<div class="greenBarDiv barBorder" style="width:99%" title="Costs">
								<div class="brownBarDiv" style="width:{$units.carpenter_per}%" title="Costs"></div>
							</div>
						</td>
					</tr>
				</table>
			</div>
			<div class="footer"></div>
		</div>
	</div>
</div>
