<!---------------------------------------------------------------------------------------
     ////////////////////////////////////////////////////////////////////////////////////
     ///////////////////////////// dynamic side-boxes. //////////////////////////////////
     //////////////////////////////////////////////////////////////////////////////////// -->
<div id="backTo" class="dynamic">
	<h3 class="header">Send out spy</h3>
	<div class="content">
		<a href="/island?id={$sendSpy.islandId}" title="Back to the island">
		<img src="http://static.ikariem.org/img/img/action_back.gif" width="160" height="100" />
		<span class="textLabel">&lt;&lt; Back to the island</span></a>
	</div>
	<div class="footer"></div>
</div>

<!---------------------------------------------------------------------------------------
     ////////////////////////////////////////////////////////////////////////////////////
     ///////////////// the main view. take care that it stretches. //////////////////////
     //////////////////////////////////////////////////////////////////////////////////// -->
<div id="mainview">
	<div class="buildingDescription">
		<h1>Send out spy</h1>
		<p>Send your trained spies to the towns of other enemies and receive detailed information about these towns. As soon as your spy has infiltrated a town successfully, you can tell him what his mission is. Beware that there is <strong>always</strong> a given risk of being discovered!</p>
	</div>
	<form action="/sendSpy/espionage?destinationCityId={$sendSpy.destinationCityId}&islandId={$sendSpy.islandId}" method="POST">
		<div class="contentBox01h" id="sendSpy">
			<h3 class="header">Send out spy</h3>
			<div class="content">
				<p class="desc">
					Your spy will try to infiltrate {$sendSpy.townName} and have a look around. {$sendSpy.townName} is a town with a size of {$sendSpy.townSize}. It is easier for a spy to move around unnoticed in larger towns, since more people come and go making it easier for the spy to blend in and disappear.
				</p>
				<div class="costs"><span class="textLabel">Costs: </span>30</div>
			<div class="risk"><span class="textLabel">Risk of discovery:</span>
				<div title="Risk of discovery {$sendSpy.risk}%" class="statusBar">
					<div style="width:{$sendSpy.risk}%" class="bar"></div>
				</div>
				<div class="percentage">{$sendSpy.risk}%</div>
			</div>
			<hr/>
			<div id="missionSummary">
				<div class="common">
					<div class="journeyTarget" title="Target town"><span class="textLabel">Target town: </span>{$sendSpy.townName}</div>
				<div class="journeyTime" title="Travel time"><span class="textLabel">Travel time: </span>{$sendSpy.arrivalTime|date_format:"%d.%m.%y %H:%M:%S"}</div>
				</div>
			</div>
			<div class="centerButton">
				<a href="/sendSpy/espionage?destinationCityId={$sendSpy.destinationCityId}&islandId={$sendSpy.islandId}" class="button">Send out spy</a>
			</div>
		</div>
		<!--end .content -->
		<div class="footer"></div>
	</div>
	<!-- end .contentBox01 -->
</form>
</div>
<!-- mainview -->
