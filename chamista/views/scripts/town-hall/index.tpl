<!---------------------------------------------------------------------------------------
	 ////////////////////////////////////////////////////////////////////////////////////
	 ///////////////////////////// dynamic side-boxes. //////////////////////////////////
	 //////////////////////////////////////////////////////////////////////////////////// -->
{include file="dynamics/buildingUpgrade.tpl"}
{if !$is_capital}
<div class="dynamic" id="abandon">
	<h3 class="header">Abandon colony</h3>
	<div class="content">
		<p>You can give up your colony. All resources, citizens and units will
		be lost.</p>
		<a href="?view=abolishColony&amp;id={$town_id}" class="button">Abandon colony</a>
	</div>
	<div class="footer"></div>
</div>
{/if}
<div class="dynamic" id="finances">
	<h3 class="header">Switch to finance overview</h3>
		<div class="content">
			<p>You can view the complete finance overview here.</p>
			<a href="/finances" class="button">Switch to finance overview</a>
		</div>
	<div class="footer"></div>
</div>


<!---------------------------------------------------------------------------------------
	 ////////////////////////////////////////////////////////////////////////////////////
	 ///////////////// the main view. take care that it stretches. //////////////////////
	 //////////////////////////////////////////////////////////////////////////////////// -->
<div id="mainview">
	{if isset($construction)}<div class="buildingDescription">{/if}
	<h1 style="text-align: center">Town hall</h1>
	{if isset($construction)}
	{include file="dynamics/upgradeProgress.tpl"}
	{/if}
	{if isset($construction)}</div>{/if}
	<div id="CityOverview" class="contentBox">
		<h3 class="header">{if $is_capital}Capital{else}Colony{/if} "{$town_name}" <a href="/townHall/renameCity?id={$town_id}&position=0" title="Rename town">rename</a></h3>
		<div class="content">
			<img class="citizen" src="http://static.ikariem.org/img/characters/y100_citizen_faceright.gif" width="42" height="100" title="" alt="" />
			<ul class="stats">
				<li class="space"><span class="textLabel">Housing space: </span><span class="value occupied">{$town_population|number_format}</span>/<span class="value total">{$population_limit|number_format}</span></li>
				<li class="growth growth_positive"><span class="textLabel">Growth: {$growth|round:2}</span><span class="value"> per Hour</span></li>
				<li class="actions"><span class="textLabel">Action Points: </span>{$town_actions}/{$max_actions}</li>
				<li class="incomegold incomegold_{if $gold_income >= 0}positive{else}negative{/if}"><span class="textLabel">Net gold: </span><span class="value">{$gold_income|number_format}</span></li>
				<li class="garrisonLimit"><span class="textLabel">Garrison limit: </span><span class="value occupied">650</span></li>
				<li class="corruption"><span class="textLabel">Corruption: </span> <span class="value positive"> <span title="Current corruption">{$corruption}%</span> </span></li>
				<li class="happiness happiness_{$satisfaction_img}"><span class="textLabel">Satisfaction:</span>{$satisfaction_word}</li>
			</ul>
			<div id="PopulationGraph">
				<h4>Population and production:</h4>
				{php}
				$free_width = round($this->get_template_vars('freeCitizens') / $this->get_template_vars('population') * 320 + 60);
				$lumber_width = round($this->get_template_vars('lumberWorkers') / $this->get_template_vars('population') * 320 + 60);
				$special_width = round($this->get_template_vars('resourceWorkers') / $this->get_template_vars('population') * 320 + 60);
				$scientist_width = round($this->get_template_vars('scientists') / $this->get_template_vars('population') * 320 + 60);
				$priest_width = round($this->get_template_vars('priest') / $this->get_template_vars('population') * 320 + 60);
				// If the width appears to be a little less or a little more due to the round-offs.
				if ($free_width+$lumber_width+$special_width+$scientist_width+$priest_width < 620) {
					$priest_width++;
				}
				if ($free_width+$lumber_width+$special_width+$scientist_width+$priest_width > 620) {
					$priest_width--;
				}
				$this->assign('free_width',$free_width);
				$this->assign('lumber_width',$lumber_width);
				$this->assign('special_width',$special_width);
				$this->assign('scientist_width',$scientist_width);
				$this->assign('priest_width',$priest_width);
				{/php}
				<div class="citizens" style="left:20px;width:{$free_width}px">
					<span class="type"><span class="count">{$freeCitizens}</span> <img src="http://static.ikariem.org/img/characters/40h/citizen_r.gif" title="Citizens" alt="Citizens" /></span> <span class="production"><span class="textLabel">produce </span><img src="http://static.ikariem.org/img/resources/icon_gold.gif" alt="Gold" /> +{$freeCitizens*3|number_format}</span>
				</div> 
				<div class="woodworkers" style="left:{$free_width+20}px;width:{$lumber_width}px">
					<span class="type"><span class="count">{$lumberWorkers}</span> <img src="http://static.ikariem.org/img/characters/40h/woodworker_r.gif" title="Workers" alt="Workers" /></span> <span class="production"><span class="textLabel">produce </span><img src="http://static.ikariem.org/img/resources/icon_wood.gif" alt="Building material" /> +{$woodProduction|number_format}</span>
				</div> 
				<div class="specialworkers" style="left:{$free_width+$lumber_width+20}px;width:{$special_width}px"> 
					<span class="type"> <span class="count">{$resourceWorkers}</span> <img src="http://static.ikariem.org/img/characters/40h/luxuryworker_r.gif" title="Workers" alt="Workers" /> </span> 
					<span class="production"> <span class="textLabel">produce </span> 
					<img src="http://static.ikariem.org/img/resources/icon_{$resource}.gif" /> +{$resourceProd|number_format}</span> 
				</div> 
				<div class="scientists" style="left:{$free_width+$lumber_width+$special_width+20}px;width:{$scientist_width}px"><span class="type"><span class="count">{$scientists}</span> <img src="http://static.ikariem.org/img/characters/40h/scientist_r.gif" title="Scientists" alt="Scientists" /></span> <span class="production"><span class="textLabel">produce </span><img src="http://static.ikariem.org/img/resources/icon_research.gif" alt="Research Points" /> +{$researchProd|number_format}</span></div> 
				<div class="priests" style="left:{$free_width+$lumber_width+$special_width+$scientist_width+20}px;width:{$priest_width}px"><span class="type"><span class="count">0</span> <img src="http://static.ikariem.org/img/characters/40h/templer_r.gif" title="Priests" alt="Priests" /></span> </div>
			</div>
			<div id="notices">
				<h4>Notices:</h4>
				{if $corruption > 0}
				<div class="warning">
					<h5>There is corruption in this colony!</h5>
					<p>The productivity and satisfaction in this town is clearly declining! Expand your governor`s residence - it should have as many levels as you have colonies!</p>
				</div>
				{else}
				<p>There are no special incidents! Congratulations, everything in your town is going fine!</p>
				{/if}
			</div>
		</div>
		<div class="footer"></div>
	</div>
	<div class="contentBox">
		<h3 class="header">Satisfaction</h3>
		<div class="content">
			<p>The satisfaction in your town is put together from different elements. This chart can help you, to identify problems and possibilities.</p>
			<div id="SatisfactionOverview">
				{php}
				$satisfaction = $this->get_template_vars('satisfaction');
				$positive = $satisfaction['basic'] + $satisfaction['research'] + $satisfaction['capital'] + $satisfaction['tavern']['base'] + $satisfaction['tavern']['wine'];
				$negative = $satisfaction['population'] + $satisfaction['corruption'];
				$use = 0;
				if ($positive < $negative) {
					$use = $negative;
				} else {
					$use = $positive;
				}
				$width = array(
					'basic' => round($satisfaction['basic'] / $use * 170 + 60),
					'research' => round($satisfaction['research'] / $use * 170 + 60),
					'capital' => round($satisfaction['capital'] / $use * 170 + 60),
					'population' => round($satisfaction['population'] / $use * 170 + 60),
					'tavern' => array(
						'base' => round($satisfaction['tavern']['base'] / $use * 300 + 60),
						'wine' => round($satisfaction['tavern']['wine'] / $use * 300 + 60),
					),
					'corruption' => round($satisfaction['corruption'] / $use * 170 + 60),
				);
				if ($width['population'] > $width['basic']) $width['population'] += 60;
				if ($width['population'] > $width['basic'] + $width['research']) $width['population'] += 60;
				if ($width['population'] > $width['basic'] + $width['research'] + $width['capital']) $width['population'] += 60;
				$this->assign('width',$width);
				{/php}
				<div class="positives"> 
					<h4>Bonuses</h4> 
					<div class="cat basic"> 
						<h5>Basic bonuses</h5> 
						<div class="base" style="left:100px;width:{$width.basic}px"><span class="value">+{$satisfaction.basic|number_format}</span> <img src="http://static.ikariem.org/img/icons/city_30x30.gif" width="30" height="30" title="Basic bonus" alt="Basic bonus" /></div> 
						<div class="research1" style="left:{$width.basic+100}px;width:{$width.research}px"><span class="value">+{$satisfaction.research|number_format}</span> <img src="http://static.ikariem.org/img/icons/researchbonus_30x30.gif" width="30" height="30" title="through research" alt="through research" /></div> 
						<div class="capital" style="left:{$width.basic+$width.research+100}px;width:{$width.capital}px"><span class="value">+{$satisfaction.capital|number_format}</span> <img src="http://static.ikariem.org/img/layout/crown.gif" width="20" height="20" title="Capital`s bonus" alt="Capital`s bonus" /></div> 
					</div> 
					<div class="cat wine"> 
						<h5>Wine</h5> 
						{if !$satisfaction.tavern}
						<p>There is no tavern available in this town yet!</p>
						{else}
						<div class="tavern" style="left:100px;width:{$width.tavern.base}px"><span class="value">+{$satisfaction.tavern.base|number_format}</span> <img src="http://static.ikariem.org/img/buildings/tavern_30x30.gif" width="30" height="30" title="by tavern level" alt="by tavern level" /></div>
						<div class="serving" style="left:{$width.tavern.base+100}px;width:{$width.tavern.wine}px"><span class="value">+{$satisfaction.tavern.wine|number_format}</span> <img src="http://static.ikariem.org/img/resources/icon_wine.gif" width="24" height="20" title="by serving wine" alt="by serving wine" /></div>
						{/if} 
					</div> 
					<div class="cat culture"> 
						<h5>Culture</h5> 
						<p>There is no museum available in this town!</p> 
					</div> 
				</div> 
				<div class="negatives"> 
					<h4>Deductions</h4> 
					<div class="cat overpopulation" > 
						<h5>Population:</h5> 
						<div class="bar" style="left:100px;width:{$width.population}px;"><span class="value">-{$satisfaction.population|number_format}</span></div> 
					</div> 
					{if $corruption > 0}
					<div class="cat corruption">
						<h5>Corruption:</h5>
						<div class="bar" style="left:100px;width:{$width.corruption}px;"><span class="value">-{$satisfaction.corruption|number_format}</span></div>
					</div>
					{/if}
				</div> 
				<div class="happiness happiness_{$satisfaction_img}"> 
					<h4>Total satisfaction:</h4> 
					<div class="value">{$happiness|number_format}</div> 
					<div class="text">{$satisfaction_word}</div> 
				</div> 
			</div>
		</div><!--end .content -->
		<div class="footer"></div>
	</div><!-- end .contentBox -->
</div><!-- end #mainview -->