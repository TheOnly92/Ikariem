<div class="dynamic" id=""> 
	<h3 class="header">Overview</h3> 
	<div class="content"> 
		<ul class="researchLeftMenu"> 
			
			<li class="scientists">Scientists: {$scientists|number_format}</li> 
			<li class="points">Research Points: {$researchPoints|number_format}</li> 
			<li class="time">per Hour: {$researchProduction|number_format}</li> 
		</ul> 
	</div> 
	<div class="footer"></div> 
</div> 
<div class="dynamic" id="viewResearchImperium"> 
	<h3 class="header">Research Overview</h3> 
	<div class="content"> 
		<img src="http://static.ikariem.org/img/premium/sideAd_premiumResearchAdvisor.jpg" width="203" height="85" /> 
		<p>Knowledge is power. And more knowledge is more power. Do you want to know more? Click here now!</p> 
		<div class="centerButton"> 
		  <a href="?view=premiumDetails" class="button">Have a look now!</a> 
		</div> 
	</div> 
	<div class="footer"></div> 
</div> 
<div id="researchLibrary" class="dynamic">
	<h3 class="header">Library</h3>
	<div class="content">
		<img src="http://static.ikariem.org/img/research/img_library.jpg" width="203" height="85" />
		<p>In the library you will find information for all areas of research!</p>
		<div class="centerButton">
			<a href="/researchAdvisor/researchOverview" class="button">To the Library!</a>
		</div>
	</div>
	<div class="footer"></div>
</div>
<div id="mainview"> 
	<div class="buildingDescription"> 
		<h1>Research Advisor</h1> 
		<p></p> 
	</div> 
	<div class="contentBox01h" id="currentResearch"> 
		<h3 class="header"><span class="textLabel">Current Research</span></h3> 
		<div class="content"> 
			<ul class="researchTypes"> 
				<li class="researchType "> 
					<div class="researchInfo" style="width:100px; min-height:120px;"> 
						<h4><a href="/ikipedia/researchDetail?researchId={$seafaring.id}">{$seafaring.name}</a></h4> 
						<div class="leftBranch"> 
							<img src="http://static.ikariem.org/img/layout/changeResearchSeafaring.jpg" /> 
							<div class="researchTypeLabel"> 
								<div class="unitcount"> 
									<span class="textLabel"> 
										<span class="before"></span>Seafaring<span class="after"></span> 
									</span>
								</div>
							</div> 
						</div> 
						<p>{$seafaring.desc}</p>
						{if $seafaring.action === true}
						<div class="researchButton"> 
							<a class="button build" style="padding-left:3px;padding-right:3px;"  href="/researchAdvisor/doResearch?type={php}echo Chamista_Model_Formula::tech_type(Chamista_Model_Formula::SEAFARING);{/php}"> 
								<span class="textLabel">Research</span> 
							</a> 
						</div> 
						{else}
						<div class="researchButton2">
							{$seafaring.action}
						</div>
						{/if}
							<div class="costs"> 
							<h5>Costs:</h5> 
							<ul class="resources"> 
								<li class="researchPoints">{$seafaring.points|number_format}</li> 
							</ul> 
						</div>
					</div> 
				</li>
				<li class="researchType alt"> 
					<div class="researchInfo" style="width:100px; min-height:120px;"> 
						<h4><a href="/ikipedia/researchDetail?researchId={$economy.id}">{$economy.name}</a></h4> 
						<div class="leftBranch"> 
							<img src="http://static.ikariem.org/img/layout/changeResearchEconomy.jpg" /> 
							<div class="researchTypeLabel"> 
								<div class="unitcount"> 
									<span class="textLabel"> 
										<span class="before"></span>Economy<span class="after"></span> 
									</span> 
								</div> 
							</div> 
						</div> 
						<p>{$economy.desc}</p>
						{if $economy.action === true}
						<div class="researchButton"> 
							<a class="button build" style="padding-left:3px;padding-right:3px;"  href="/researchAdvisor/doResearch?type={php}echo Chamista_Model_Formula::tech_type(Chamista_Model_Formula::ECONOMY);{/php}"> 
								<span class="textLabel">Research</span> 
							</a> 
						</div> 
						{else}
						<div class="researchButton2">
							{$economy.action}
						</div>
						{/if}
						<div class="costs"> 
							<h5>Costs:</h5> 
							<ul class="resources"> 
								<li class="researchPoints">{$economy.points|number_format}</li> 
							</ul> 
						</div>
					</div> 
				</li>
				<li class="researchType "> 
					<div class="researchInfo" style="width:100px; min-height:120px;"> 
						<h4><a href="/ikipedia/researchDetail?researchId={$science.id}">{$science.name}</a></h4>
						<div class="leftBranch"> 
							<img src="http://static.ikariem.org/img/layout/changeResearchKnowledge.jpg" /> 
								<div class="researchTypeLabel"> 
								<div class="unitcount"> 
									<span class="textLabel"> 
										<span class="before"></span>Science<span class="after"></span> 
									</span> 
								</div> 
							</div> 
						</div> 
						<p>{$science.desc}</p>
						{if $science.action === true}
						<div class="researchButton"> 
							<a class="button build" style="padding-left:3px;padding-right:3px;"  href="/researchAdvisor/doResearch?type={php}echo Chamista_Model_Formula::tech_type(Chamista_Model_Formula::SCIENCE);{/php}"> 
								<span class="textLabel">Research</span> 
							</a> 
						</div> 
						{else}
						<div class="researchButton2">
							{$science.action}
						</div>
						{/if}
						<div class="costs"> 
							<h5>Costs:</h5> 
							<ul class="resources"> 
								<li class="researchPoints">{$science.points|number_format}</li> 
							</ul> 
						</div>
					</div> 
				</li>
				<li class="researchType alt"> 
					<div class="researchInfo" style="width:100px; min-height:120px;"> 
						<h4><a href="/ikipedia/researchDetail?researchId={$military.id}">{$military.name}</a></h4>
						<div class="leftBranch"> 
							<img src="http://static.ikariem.org/img/layout/changeResearchMilitary.jpg" /> 
								<div class="researchTypeLabel"> 
								<div class="unitcount"> 
									<span class="textLabel"> 
										<span class="before"></span>Military<span class="after"></span> 
									</span> 
								</div> 
							</div> 
						</div> 
						<p>{$military.desc}</p>
						{if $military.action === true}
						<div class="researchButton"> 
							<a class="button build" style="padding-left:3px;padding-right:3px;"  href="/researchAdvisor/doResearch?type={php}echo Chamista_Model_Formula::tech_type(Chamista_Model_Formula::MILITARY);{/php}"> 
								<span class="textLabel">Research</span> 
							</a> 
						</div> 
						{else}
						<div class="researchButton2">
							{$military.action}
						</div>
						{/if}
						<div class="costs"> 
							<h5>Costs:</h5> 
							<ul class="resources"> 
								<li class="researchPoints">{$military.points|number_format}</li> 
							</ul> 
						</div>
					</div> 
				</li>
			</ul>
		</div>
		<div class="footer"></div> 
	</div> 
</div> 