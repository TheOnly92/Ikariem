<!---------------------------------------------------------------------------------------
     ////////////////////////////////////////////////////////////////////////////////////
 	///////////////////////////// dynamic side-boxes. //////////////////////////////////
	 //////////////////////////////////////////////////////////////////////////////////// --> 
<div id="backTo" class="dynamic"> 
	<h3 class="header">Back to the town</h3> 
	<div class="content"> 
		<a href="/city?id={$town_id}" title="Back to the town"> 
			<img src="http://static.ikariem.org/img/img/action_back.gif" width="160" height="100" alt="" /> 
			<span class="textLabel">&lt;&lt; Back to the town</span>
		</a> 
	</div> 
	<div class="footer"></div> 
</div> 
<!---------------------------------------------------------------------------------------
	 ////////////////////////////////////////////////////////////////////////////////////
	 ///////////////// the main view. take care that it stretches. //////////////////////
	 //////////////////////////////////////////////////////////////////////////////////// --> 
 
<div id="mainview"> 
	<h1>Balances</h1> 
	<table cellspacing="0" cellspacing="0" id="balance" class="table01"> 
		<tr> 
			<td class="sigma">Amount of gold</td> 
			<td class="value res"></td> 
			<td class="value res"></td> 
			<td class="value res">{$total_gold|number_format}</td> 
		</tr> 
	</table> 
	
	<table cellspacing="0" cellspacing="0" id="balance" class="table01"> 
	<tr> 
		<th class="city"><img src="http://static.ikariem.org/img/layout/icon-city2.gif" alt="Town" /></th> 
		<th>Income</th> 
		<th>Scientists</th> 
		<th>Result</th> 
	</tr> 
	{foreach from=$city_view item=city name=town}
	<tr{if $smarty.foreach.town.index % 2 == 0} class="alt"{/if}>
		<td class="city">{$city.town}</td>
		<td class="value res">{$city.income|number_format}</td>
		<td class="value res">{$city.upkeep|number_format}</td>
		<td class="value res">{$city.result|number_format}</td>
	</tr>
	{/foreach}
 	<tr class="result"> 
 		<td class="sigma"><img src="http://static.ikariem.org/img/layout/sigma.gif" alt="Sum" /></td> 
 		<td class="value res">{$income_sum|number_format}</td> 
 		<td class="value res">{$upkeep_sum|number_format}</td> 
 		<td class="value res">{$result_sum|number_format}</td> 
 	</tr> 
	</table> 
	
	<table cellspacing="0" cellpadding="0" id="upkeepReductionTable" class="table01" border="0px"> 
		<tr> 
			<th colspan="4">Basic Costs</th> 
		</tr> 
		<tr > 
			<td class="reason">Troops</td> 
			<td class="costs">{$basic.troops|number_format}</td> 
			<td class="bar">
				<div class="greenBarDiv barBorder" style="width:99%" title="Costs"> 
					<div class="brownBarDiv" style="width:100%" title="Costs"></div> 
				</div> 
			</td> 
			<td class="hidden"></td> 
		</tr> 
		<tr class="altbottomLine"> 
			<td class="reason">- Research ({$basic.troops_discount}%)</td> 
			<td class="boldcosts">{$basic.troops_discount_value|number_format}</td> 
			<td class="bar">
			<div class="greenBarDiv barBorder" style="width:99%" title="Costs"> 
			<div class="brownBarDiv" style="width:{$basic.troops_discount_width}%" title="Costs"> 
			</div> 
			</div> 
			</td> 
			<td class="hidden">{$basic.troops_total|number_format}</td> 
		</tr> 
		<tr > 
			<td class="reason">Fleet</td> 
			<td class="costs">{$basic.fleets|number_format}</td> 
			<td class="bar">
				<div class="greenBarDiv barBorder" style="width:99%" title="Costs"> 
					<div class="brownBarDiv" style="width:100%" title="Costs"> </div>
				</div>
			</td> 
			<td class="hidden"></td> 
		</tr> 
		<tr class="altbottomLine"> 
			<td class="reason">- Research ({$basic.fleets_discount|number_Format}%)</td> 
			<td class="boldcosts">{$basic.fleets_discount_value|number_Format}</td> 
			<td class="bar">
				<div class="greenBarDiv barBorder" style="width:99%" title="Costs"> 
					<div class="brownBarDiv" style="width:{$basic.fleets_discount_width}%" title="Costs"></div>
				</div>
			</td>
			<td class="hidden">{$basic.fleets_total|number_format}</td> 
		</tr> 
		<tr class="result"> 
		<td class="reason"><img src="http://static.ikariem.org/img/layout/sigma.gif" alt="Sum" /></td> 
		<td class="costs"></td> 
		<td class="bar"></td> 
		<td class="hidden">{$basic.total|number_format}</td> 
		</tr> 
	</table> 
	 
	<table cellspacing="0" cellpadding="0" id="upkeepReductionTable" class="table01" border="0px"> 
		<tr> 
			<th colspan="4">Supply costs</th> 
		</tr> 
		<tr > 
			<td class="reason">Troops</td> 
			<td class="costs">0</td> 
			<td class="bar">
				<div class="greenBarDiv barBorder" style="width:99%" title="Costs"> 
					<div class="brownBarDiv" style="width:100%" title="Costs"></div> 
				</div> 
			</td> 
			<td class="hidden"></td> 
		</tr> 
		<tr class="altbottomLine"> 
			<td class="reason">- Research (0%)</td> 
			<td class="boldcosts">0</td> 
			<td class="bar">
				<div class="greenBarDiv barBorder" style="width:99%" title="Costs"> 
					<div class="brownBarDiv" style="width:100%" title="Costs"></div> 
				</div> 
			</td> 
			<td class="hidden">0</td> 
		</tr> 
		<tr > 
			<td class="reason">Fleet</td> 
			<td class="costs">0</td> 
			<td class="bar">
				<div class="greenBarDiv barBorder" style="width:99%" title="Costs"> 
					<div class="brownBarDiv" style="width:100%" title="Costs"></div> 
				</div> 
			</td> 
			<td class="hidden"></td> 
		</tr> 
		<tr class="altbottomLine"> 
			<td class="reason">- Research (0%)</td> 
			<td class="boldcosts">0</td> 
			<td class="bar">
				<div class="greenBarDiv barBorder" style="width:99%" title="Costs"> 
					<div class="brownBarDiv" style="width:100%" title="Costs"></div> 
				</div> 
			</td> 
			<td class="hidden">0</td> 
		</tr> 
		<tr class="result"> 
			<td class="reason"><img src="http://static.ikariem.org/img/layout/sigma.gif" alt="Sum" /></td> 
			<td class="costs"></td> 
			<td class="bar"></td> 
			<td class="hidden">0</td> 
		</tr> 
	</table> 
	
	<table cellspacing="0" cellpadding="0" id="upkeepReductionTable" class="table01" border="0px"> 
		<tr><th colspan="4">Total</th></tr> 
		<tr> 
			<td class="reason">Income</td> 
			<td class="costs"></td> 
			<td class="bar"> 
				<div class="greenBarDiv barBorder" style="width:99%" title="Income"> 
					<div class="brownBarDiv" style="width: 100%" title="Income"></div> 
				</div> 
			</td> 
			<td class="hidden">{$result_sum|number_format}</td> 
		</tr> 
		<tr> 
			<td class="reason"> - Upkeep</td> 
			<td class="costs"></td> 
			<td class="bar"> 
				<div class="redBarDiv barBorder" style="width:99%" title="Costs"> 
					<div class="brownBarDiv" style="width: 100%" title="Income"></div> 
				</div> 
			</td> 
			<td class="hidden">{$result_upkeep|number_format}</td> 
		</tr> 
		<tr class="result"> 
			<td class="reason"><img src="http://static.ikariem.org/img/layout/sigma.gif" alt="Sum" /></td> 
			<td class="costs"></td> 
			<td class="bar"></td> 
			<td class="hidden">{$total|number_format}</td> 
		</tr> 
	</table> 
</div> 