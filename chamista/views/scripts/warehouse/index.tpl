{include file="dynamics/buildingUpgrade.tpl"}
     
    <div id="information" class="dynamic">

        <h3 class="header">In level {$build_lvl}</h3>
    	<div class="content">
            <table class="safeinnextlevel">
            <tr>
    	        <th>Resource</th>
                <th>Safe</th>
                <th>Capacity</th>

            </tr>
                    <tr>
                    <td class="resource"><img src="http://static.ikariem.org/img/resources/icon_wood.gif" title="Building material" alt="Building material" /></td>
                    <td class="amount">+80</td>
                    <td class="amount">+8,000</td>
                </tr>
                    <tr>
                    <td class="resource"><img src="http://static.ikariem.org/img/resources/icon_wine.gif" title="Wine" alt="Wine" /></td>

                    <td class="amount">+80</td>
                    <td class="amount">+8,000</td>
                </tr>
                    <tr>
                    <td class="resource"><img src="http://static.ikariem.org/img/resources/icon_marble.gif" title="Marble" alt="Marble" /></td>
                    <td class="amount">+80</td>
                    <td class="amount">+8,000</td>

                </tr>
                    <tr>
                    <td class="resource"><img src="http://static.ikariem.org/img/resources/icon_glass.gif" title="Crystal glass" alt="Crystal glass" /></td>
                    <td class="amount">+80</td>
                    <td class="amount">+8,000</td>
                </tr>
                    <tr>
                    <td class="resource"><img src="http://static.ikariem.org/img/resources/icon_sulfur.gif" title="Sulphur" alt="Sulphur" /></td>

                    <td class="amount">+80</td>
                    <td class="amount">+8,000</td>
                </tr>
                </table>
        </div>
        <div class="footer"></div>
    </div>
<div id="mainview">

    <div class="buildingDescription">
	    <h1>Warehouse</h1>
		{if isset($construction)}
		{include file="dynamics/upgradeProgress.tpl"}
		{else}
		<p>{$building_desc}</p>
		{/if}
		     	
    </div>
			
    <div class="contentBox01h">
	    <h3 class="header"><span class="textLabel">Goods in the warehouse</span></h3>
        <div class="content">

            <p style="padding-top:10px;padding-left:18px;padding-right:10px;padding-bottom:0px;">
                        </p>
            <table class="table01">
                <thead>
                <tr>
                    <th>Safe</th>
                    <th>Lootable</th>

                    <th>Total</th>
                    <th>Capacity</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td class="sicher">
                        <table cellpadding="0" cellspacing="0">

                            <tr>
                                <td><img src="http://static.ikariem.org/img/resources/icon_wood.gif" title="Building material" alt="Building material" /></td>
                                <td><span class="secure">{$warehouseSafe}</span></td>
                            </tr>
                            <tr>
                                <td><img src="http://static.ikariem.org/img/resources/icon_wine.gif" title="Wine" alt="Wine" /></td>
                                <td><span class="secure">{$warehouseSafe}</span></td>
                            </tr>

                            <tr>
                                <td><img src="http://static.ikariem.org/img/resources/icon_marble.gif" title="Marble" alt="Marble" /></td>
                                <td><span class="secure">{$warehouseSafe}</span></td>
                            </tr>
                            <tr>
                                <td><img src="http://static.ikariem.org/img/resources/icon_glass.gif" title="Crystal glass" alt="Crystal glass" /></td>
                                <td><span class="secure">{$warehouseSafe}</span></td>
                            </tr>

                            <tr>
                                <td><img src="http://static.ikariem.org/img/resources/icon_sulfur.gif" title="Sulphur" alt="Sulphur" /></td>
                                <td><span class="secure">{$warehouseSafe}</span></td>
                            </tr>
                        </table>
                    </td>
                    <td class="klaubar">
                        <table cellpadding="0" cellspacing="0">

                            <tr>
                                <td><img src="http://static.ikariem.org/img/resources/icon_wood.gif" title="Building material" alt="Building material" /></td>
                                <td><span class="insecure">{$lootable.wood|number_format}</span></td>
                            </tr>
                            <tr>
                                <td><img src="http://static.ikariem.org/img/resources/icon_wine.gif" title="Wine" alt="Wine" /></td>
                                <td><span class="insecure">{$lootable.wine|number_format}</span></td>
                            </tr>

                            <tr>
                                <td><img src="http://static.ikariem.org/img/resources/icon_marble.gif" title="Marble" alt="Marble" /></td>
                                <td><span class="insecure">{$lootable.marble|number_format}</span></td>
                            </tr>
                            <tr>
                                <td><img src="http://static.ikariem.org/img/resources/icon_glass.gif" title="Crystal glass" alt="Crystal glass" /></td>
                                <td><span class="insecure">{$lootable.crystal|number_format}</span></td>
                            </tr>

                            <tr>
                                <td><img src="http://static.ikariem.org/img/resources/icon_sulfur.gif" title="Sulphur" alt="Sulphur" /></td>
                                <td><span class="insecure">{$lootable.sulfur|number_format}</span></td>
                            </tr>
                        </table>
                    </td>
                    <td class="gesamt">
                        <table cellpadding="0" cellspacing="0">

                            <tr>
                                <td><img src="http://static.ikariem.org/img/resources/icon_wood.gif" title="Building material" alt="Building material" /></td>
                                <td>{$resources.wood|number_format}</td>
                            </tr>
                            <tr>
                                <td><img src="http://static.ikariem.org/img/resources/icon_wine.gif" title="Wine" alt="Wine" /></td>
                                <td>{$resources.wine|number_format}</td>
                            </tr>

                            <tr>
                                <td><img src="http://static.ikariem.org/img/resources/icon_marble.gif" title="Marble" alt="Marble" /></td>
                                <td>{$resources.marble|number_format}</td>
                            </tr>
                            <tr>
                                <td><img src="http://static.ikariem.org/img/resources/icon_glass.gif" title="Crystal glass" alt="Crystal glass" /></td>
                                <td>{$resources.crystal|number_format}</td>
                            </tr>

                            <tr>
                                <td><img src="http://static.ikariem.org/img/resources/icon_sulfur.gif" title="Sulphur" alt="Sulphur" /></td>
                                <td>{$resources.sulfur|number_format}</td>
                            </tr>
                        </table>
                    </td>
                    <td class="capacity">
                        <table cellpadding="0" cellspacing="0">

                            <tr>
                                <td><img src="http://static.ikariem.org/img/resources/icon_wood.gif" title="Building material" alt="Building material" /></td>
                                <td>{$capacity|number_format}</td>
                            </tr>
                            <tr>
                                <td><img src="http://static.ikariem.org/img/resources/icon_wine.gif" title="Wine" alt="Wine" /></td>
                                <td>{$capacity|number_format}</td>
                            </tr>

                            <tr>
                                <td><img src="http://static.ikariem.org/img/resources/icon_marble.gif" title="Marble" alt="Marble" /></td>
                                <td>{$capacity|number_format}</td>
                            </tr>
                            <tr>
                                <td><img src="http://static.ikariem.org/img/resources/icon_glass.gif" title="Crystal glass" alt="Crystal glass" /></td>
                                <td>{$capacity|number_format}</td>
                            </tr>

                            <tr>
                                <td><img src="http://static.ikariem.org/img/resources/icon_sulfur.gif" title="Sulphur" alt="Sulphur" /></td>
                                <td>{$capacity|number_format}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                </tbody>

            </table>
        </div>
        <div class="footer"></div>
	</div>
</div>