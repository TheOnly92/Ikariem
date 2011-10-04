{include file="dynamics/buildingUpgrade.tpl"}
<div id="trader" class="dynamic">
    <h3 class="header">Premium-Trader</h3>
    <div class="content">
        <img src="http://static.ikariem.org/img/research/area_economy.jpg" width="203" height="85" />
        <p style="text-align:center;">Directly exchange resources! Rates:<br />

        <strong style="font-size:1.2em;">Building material: 1 to 1!</strong><br />
        <strong style="font-size:1.2em;">Luxury goods: 1 to 1!</strong></p>
        <div class="centerButton">
            <a href="?view=premiumTrader&oldView=branchOffice&id=91112&position=4" class="button" title="to Trader">to Trader</a>
        </div>
    </div>
    <div class="footer"></div>

</div>
<div class="dynamic">
    <h3 class="header">Storage capacity <a class="help" href="/ikipedia/buildingDetail/buildingId/13" title="Help"><span class="textLabel">Need help?</span></a></h3>
    <div class="content">
        <p><strong>Current storage capacity:</strong> 400</p>
    </div>
    <div class="footer"></div>

</div>
				<div id="mainview">
					<div class="buildingDescription">
						<h1>Trading post</h1>
				{if isset($construction)}
				{include file="dynamics/upgradeProgress.tpl"}
				{else}
				<p>{$building_desc}</p>
				{/if}
					     					</div><!--buildingDescription -->
					
						<div class="contentBox01h">
							<h3 class="header"><span class="textLabel">Offers from trade partners</span></h3>

							<div class="content">
								<div>
									<table cellpadding="0" cellspacing="0" border="0" class="tablekontor">
										<tr>
											<th>Town</th>
											<th>Harbor size</th>
											<th>Piece</th>

											<th>Resource</th>
											<th>Purchase price</th>
											<th>Distance</th>
											<th>Trade?</th>
										</tr>
									</table>
								</div>

							</div><!-- content -->
							<div class="footer"></div>
						</div><!-- contentbox01h -->

				    <!-- Bargain finder box -->
					<form action="/branchOffice?id={$town_id}&position={$position}" method="POST">
						<div class="contentBox01h" id="finder">
							<h3 class="header"><span class="textLabel">Bargain hunter</span></h3>
							<div class="content">

								<div>
                                    <table class="search">
                                        <tr>
                                            <td>
                                                <input type="radio" name="type" value="444"{if $branchOffice.search.type == 444} checked="checked"{/if} />
                                            </td>

                                            <td class="text">
                                                I am looking for
                                            </td>                                    
                                            <td>
                                                <input type="radio" name="type" value="333"{if $branchOffice.search.type == 333} checked="checked"{/if}  />
                                            </td>   
                                            <td class="text">
                                                I offer
                                            </td>
                                            <td>
                                                <div>

                                                    <select name="searchResource">
                                                        <option value="0"{if $branchOffice.search.searchResource == 0} selected="selected"{/if}>Building material</option>
                                                        <option value="1"{if $branchOffice.search.searchResource == 1} selected="selected"{/if}>Wine</option>
                                                        <option value="2"{if $branchOffice.search.searchResource == 2} selected="selected"{/if}>Marble</option>
                                                        <option value="3"{if $branchOffice.search.searchResource == 3} selected="selected"{/if}>Crystal glass</option>
                                                        <option value="4"{if $branchOffice.search.searchResource == 4} selected="selected"{/if}>Sulfur</option>

                                                    </select>
                                                </div>
                                            </td>
                                            <td>
                                                    Search radius:
                                             </td>
                                             <td>
                                                    <select size="1" name="range">
                                                    	{section name=range start=0 loop=$branchOffice.maxSearch}
                                                    		<option{if $branchOffice.search.range == $smarty.section.range.index+1} selected="selected"{/if}>{$smarty.section.range.index+1}</option>
                                                    	{/section}
                                                                    </select>
                                             </td>
                                             <td>
                                                    Islands within radius                                            </td>
                                        </tr>                                    
                                    </table>
                                    </div>
								<div>
                                
									<div class="centerButton">

                                        <input type="submit" class="button" style="clear:right;" value="Find bargain"/>
                                    </div>
								</div>
							</div><!-- content -->
							<div class="footer"></div>
						</div><!-- contentbox01h -->
					</form>
					<!-- Schnäppchenfinder box ende -->

					<div class="contentBox01h">
						<h3 class="header"><span class="textLabel">Results</span></h3>
						<div class="content">
							<div>
							<!-- Ergebnis Schnäppchenfinder -->
								<table cellpadding="0" cellspacing="0" border="0" class="tablekontor">
									<tr>
                                        <th>Town
                                            <a href="/branchOffice?id={$town_id}&position={$position}&range={$branchOffice.search.range}&orderBy=cityAsc&searchResource={$branchOffice.search.searchResource}&type={$branchOffice.search.type}" class="unicode">&uArr;</a>
                                            <a href="/branchOffice?id={$town_id}&position={$position}&range={$branchOffice.search.range}&orderBy=cityDesc/searchResource/{$branchOffice.search.searchResource}&type={$branchOffice.search.type}" class="unicode">&dArr;</a></th>
										
										<th>Harbor size
    									    <a href="/branchOffice?id={$town_id}&position={$position}&range={$branchOffice.search.range}&orderBy=portLevelAsc&searchResource={$branchOffice.search.searchResource}&type={$branchOffice.search.type}" class="unicode">&uArr;</a>
                                            <a href="/branchOffice?id={$town_id}&position={$position}&range={$branchOffice.search.range}&orderBy=portLevelDesc&searchResource={$branchOffice.search.searchResource}&type={$branchOffice.search.type}" class="unicode">&dArr;</a>
										</th>
										
										<th>Piece
											<a href="/branchOffice?id={$town_id}&position={$position}&range={$branchOffice.search.range}&orderBy=ressAsc&searchResource={$branchOffice.search.searchResource}&type={$branchOffice.search.type}" class="unicode">&uArr;</a>
                                            <a href="/branchOffice?id={$town_id}&position={$position}&range={$branchOffice.search.range}&orderBy=ressDesc&searchResource={$branchOffice.search.searchResource}&type={$branchOffice.search.type}" class="unicode">&dArr;</a></th>

										<th>Resource</th>

										<th>Purchase price
											<a href="/branchOffice?id={$town_id}&position={$position}&range={$branchOffice.search.range}&orderBy=sellAsc&searchResource={$branchOffice.search.searchResource}&type={$branchOffice.search.type}" class="unicode">&uArr;</a>
                                            <a href="/branchOffice?id={$town_id}&position={$position}&range={$branchOffice.search.range}&orderBy=sellDesc&searchResource={$branchOffice.search.searchResource}&type={$branchOffice.search.type}" class="unicode">&dArr;</a></th>
										
										<th>Distance
											<a href="/branchOffice?id={$town_id}&position={$position}&range={$branchOffice.search.range}&orderBy=distanceAsc&searchResource={$branchOffice.search.searchResource}&type={$branchOffice.search.type}" class="unicode">&uArr;</a>
                                            <a href="/branchOffice?id={$town_id}&position={$position}&range={$branchOffice.search.range}&orderBy=distanceDesc&searchResource={$branchOffice.search.searchResource}&type={$branchOffice.search.type}" class="unicode">&dArr;</a></th>
										
										<th>Trade?</th>
									</tr>
									{if count($branchOffice.bargainResults) > 0}
									{foreach from=$branchOffice.bargainResults item=bargain name=bargain}
									<tr{if $smarty.foreach.bargain.index % 2 == 0} class="alt"{/if}>
										<td>{$bargain.town.name} ({$bargain.town.owner})</td>
										<td>{$bargain.harbor}</td>
										<td>{$bargain.piece}</td>
										<td><img src="http://static.ikariem.org/img/resources/icon_{$bargain.resource.css}.gif" alt="{$bargain.resource.name}" title="{$bargain.resource.name}" />
										<td>{$bargain.price} <img src="http://static.ikariem.org/img/resources/icon_gold.gif" /> Per Piece</td>
										<td>{$bargain.distance}</td>
										<td><a href="/branchOffice/takeOffer?destinationCityId={$bargain.town.id}&position={$position}&id={$town_id}&type={$bargain.type}&resource={$bargain.resource.id}"><img src="http://static.ikariem.org/img/layout/icon-kiste.gif" alt="" title="" /></a></td>
									</tr>
									{/foreach}
									<tr>
                                    	<td colspan="6" class="paginator">
                                    		                                                                  			</td>
                              		</tr>
       								{else}
       								<tr>
                                    	<td colspan="6" class="paginator">
                                    	No offers available at the moment
                                    		                                                                  			</td>
                              		</tr>
       								{/if}
                                    </td>
                                </tr>
								</table>

								<!-- Ergebnis Schnäppchenfinder ende -->
							</div>
						</div><!-- content -->
						<div class="footer"></div>
					</div><!-- contentbox01h -->

					<!-- eigene Angebote -->
					<form name="formkontor"  action="/branchOffice/updateOffers?id={$town_id}&position={$position}" method="POST">
						<br />
						<div class="contentBox01h">
							<h3 class="header"><span class="textLabel">Own Offers</span></h3>
							<div class="content">
								<table cellpadding="0" cellspacing="0" border="0" class="tablekontor">

									<tr>
										<th colspan="2">Type of offer</th><th>Amount</th><th>Price</th>
									</tr>
									<tr>
										<td class="icon"><img src="http://static.ikariem.org/img/resources/icon_wood.gif" alt="Building material" title="Saw Mill"/></td>
										<td class="select"><select name="resourceTradeType" id="resourceTradeType" size="1"><option value="333"{if $branchOffice.ownOffers.wood_type == 1} selected="selected"{/if}>Buy</option><option value="444"{if $branchOffice.ownOffers.wood_type == 0} selected="selected"{/if}>Sell</option></select></td>

										<td><input type="text" size="4" name="resource" id="resource" value="{$branchOffice.ownOffers.wood_value}" /></td>
										<td><input type="text" size="2" name="resourcePrice"  id="resourcePrice"  maxlength="2" value="{$branchOffice.ownOffers.wood_price}" /><img src="http://static.ikariem.org/img/resources/icon_gold.gif"/> Per Piece</td>
									</tr>
									<tr class="alt">
										<td class="icon"><img src="http://static.ikariem.org/img/resources/icon_wine.gif" alt="Wine" title="Vineyard"/></td>
										<td class="select"><select name="tradegood1TradeType" id="tradegood1TradeType" size="1"><option value="333"{if $branchOffice.ownOffers.wine_type == 1} selected="selected"{/if}>Buy</option><option value="444"{if $branchOffice.ownOffers.wine_type == 0} selected="selected"{/if}>Sell</option></select></td>
										<td><input type="text" size="4" name="tradegood1" id="tradegood1" value="{$branchOffice.ownOffers.wine_value}" /></td>

										<td><input type="text" size="2" name="tradegood1Price" id="tradegood1Price" maxlength="2" value="{$branchOffice.ownOffers.wine_price}" /><img src="http://static.ikariem.org/img/resources/icon_gold.gif" /> Per Piece</td>
									</tr>
									<tr>
										<td class="icon"><img src="http://static.ikariem.org/img/resources/icon_marble.gif" alt="Marble" title="Quarry"/></td>
										<td class="select"><select name="tradegood2TradeType" id="tradegood2TradeType" size="1"><option value="333"{if $branchOffice.ownOffers.marble_type == 1} selected="selected"{/if}>Buy</option><option value="444"{if $branchOffice.ownOffers.marble_type == 0} selected="selected"{/if}>Sell</option></select></td>
										<td><input type="text" size="4" name="tradegood2" id="tradegood2" value="{$branchOffice.ownOffers.marble_value}" /></td>
										<td><input type="text" size="2" name="tradegood2Price" id="tradegood2Price" maxlength="2" value="{$branchOffice.ownOffers.marble_price}"/><img src="http://static.ikariem.org/img/resources/icon_gold.gif"/> Per Piece</td>

									</tr>
									<tr class="alt">
										<td class="icon"><img src="http://static.ikariem.org/img/resources/icon_glass.gif" alt="Crystal glass" title="Crystal mine"/></td>
										<td class="select"><select name="tradegood3TradeType" id="tradegood3TradeType" size="1"><option value="333"{if $branchOffice.ownOffers.crystal_type == 1} selected="selected"{/if}>Buy</option><option value="444"{if $branchOffice.ownOffers.crystal_type == 0} selected="selected"{/if}>Sell</option></select></td>
										<td><input type="text" size="4" name="tradegood3" id="tradegood3" value="{$branchOffice.ownOffers.crystal_value}" /></td>
										<td><input type="text" size="2" name="tradegood3Price" id="tradegood3Price" maxlength="2" value="{$branchOffice.ownOffers.crystal_price}"/><img src="http://static.ikariem.org/img/resources/icon_gold.gif"/> Per Piece</td>
									</tr>

									<tr>
										<td class="icon"><img src="http://static.ikariem.org/img/resources/icon_sulfur.gif" alt="Sulfur" title="Sulfur Pit"/></td>
										<td class="select"><select name="tradegood4TradeType" id="tradegood4TradeType" size="1"><option value="333"{if $branchOffice.ownOffers.sulfur_type == 1} selected="selected"{/if}>Buy</option><option value="444"{if $branchOffice.ownOffers.sulfur_type == 0} selected="selected"{/if}>Sell</option></select></td>
										<td><input type="text" size="4" name="tradegood4" id="tradegood4" value="{$branchOffice.ownOffers.sulfur_value}" /></td>
										<td><input type="text" size="2" name="tradegood4Price" id="tradegood4Price" maxlength="2" value="{$branchOffice.ownOffers.sulfur_price}" /><img src="http://static.ikariem.org/img/resources/icon_gold.gif"/> Per Piece</td>
									</tr>
								</table>

								<div><p>Reserved gold for acquisitions: <span id="reservedGold">0</span> <img src="http://static.ikariem.org/img/resources/icon_gold.gif" /></p><input type="submit" class="button" value="Update offers"/></div>
							</div><!-- content -->
							<div class="footer"></div>
						</div><!-- contentbox01h -->
					</form>
					<!-- eigene Angebote ende -->
				</div><!-- end #mainview -->

<script language="javascript">{literal}
	function checkBranchOffice(e, num) { 
        var tradeType = new Array();
        var tradegood = new Array();
        var tradegoodPrice = new Array();
        var resources = new Array();
        
        tradeType[0] = Dom.get("resourceTradeType");
        tradeType[1] = Dom.get("tradegood1TradeType");
        tradeType[2] = Dom.get("tradegood2TradeType");
        tradeType[3] = Dom.get("tradegood3TradeType");
        tradeType[4] = Dom.get("tradegood4TradeType");
        
        tradegood[0] = Dom.get("resource");
        tradegoodPrice[0] = Dom.get("resourcePrice");
        tradegood[1] = Dom.get("tradegood1");
        tradegoodPrice[1] = Dom.get("tradegood1Price");
        tradegood[2] = Dom.get("tradegood2");
        tradegoodPrice[2] = Dom.get("tradegood2Price");
        tradegood[3] = Dom.get("tradegood3");
        tradegoodPrice[3] = Dom.get("tradegood3Price");
        tradegood[4] = Dom.get("tradegood4");
        tradegoodPrice[4] = Dom.get("tradegood4Price");
        
        resources[0] = 9618;
        resources[1] = 472;
        resources[2] = 167;
        resources[3] = 172;
        resources[4] = 152;
        
        var gold = 5221.77819808;
        var costs = 0;
        var sumCosts = 0;
        var storageCapacity = 400;
        var sumStorage = 0;
        
        for (i=0; i<5; i++) {
            if (tradeType[i].value == 333) {
                sumCosts += tradegood[i].value * tradegoodPrice[i].value;
            } else {
                sumStorage += tradegood[i].value * 1; // *1 converts to int
            }
        }
        
        if(sumCosts > gold) {
            sumCosts -= tradegood[num].value * tradegoodPrice[num].value;
            tradegood[num].value = 0;
            dif = gold - sumCosts;
            tradegood[num].value = Math.floor(dif / tradegoodPrice[num].value);
            sumCosts += tradegood[num].value * tradegoodPrice[num].value;
            
            if (sumCosts > gold) {
                sumCosts -= tradegood[num].value * tradegoodPrice[num].value;
                tradegood[num].value = 0;
            }            
        }
        
        if(sumStorage > storageCapacity) {
            sumStorage -= tradegood[num].value;
            tradegood[num].value = 0;
            dif = storageCapacity - sumStorage;
            tradegood[num].value = dif;
            sumStorage += dif;
            
            if (sumStorage > storageCapacity) {
                sumStorage -= tradegood[num].value;
                tradegood[num].value = 0;
            }           
        }
        
        Dom.get("reservedGold").innerHTML = sumCosts;        

        for (i=0; i<5; i++) {
            if (tradeType[i].value == 444) {
                if (tradegood[i].value > resources[i]) {
                    tradegood[i].value = resources[i];
                    tradegood[i].focus();
                    break;
                }
            }
        }       
        
	}
    
	Event.onDOMReady(function() {
    	Event.addListener(Dom.get("resourceTradeType"),   "change", checkBranchOffice, 0);
    	Event.addListener(Dom.get("tradegood1TradeType"), "change", checkBranchOffice, 1);
    	Event.addListener(Dom.get("tradegood2TradeType"), "change", checkBranchOffice, 2);
    	Event.addListener(Dom.get("tradegood3TradeType"), "change", checkBranchOffice, 3);
    	Event.addListener(Dom.get("tradegood4TradeType"), "change", checkBranchOffice, 4);
    	Event.addListener(Dom.get("resource"), 			  "keyup", checkBranchOffice, 0);
    	Event.addListener(Dom.get("resourcePrice"), 	  "keyup", checkBranchOffice, 0);
    	Event.addListener(Dom.get("tradegood1"),		  "keyup", checkBranchOffice, 1);
    	Event.addListener(Dom.get("tradegood1Price"), 	  "keyup", checkBranchOffice, 1);
    	Event.addListener(Dom.get("tradegood2"),		  "keyup", checkBranchOffice, 2);
    	Event.addListener(Dom.get("tradegood2Price"), 	  "keyup", checkBranchOffice, 2);
    	Event.addListener(Dom.get("tradegood3"),		  "keyup", checkBranchOffice, 3);
    	Event.addListener(Dom.get("tradegood3Price"), 	  "keyup", checkBranchOffice, 3);
    	Event.addListener(Dom.get("tradegood4"),		  "keyup", checkBranchOffice, 4);
    	Event.addListener(Dom.get("tradegood4Price"),	  "keyup", checkBranchOffice, 4);
    	
	});{/literal}
</script>