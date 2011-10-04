<!---------------------------------------------------------------------------------------
     ////////////////////////////////////////////////////////////////////////////////////
     ///////////////////////////// dynamic side-boxes. //////////////////////////////////
     //////////////////////////////////////////////////////////////////////////////////// -->
<div id="backTo" class="dynamic">
	<h3 class="header">Hideout</h3>
	<div class="content">
		<a href="/safehouse?id={$town_id}&position={$position}" title="Back to Hideout">
			<img src="http://static.ikariem.org/img/buildings/y100/safehouse.gif" width="160" height="100"/>
			<span class="textLabel">&lt;&lt; Back to the Hideout</span>
		</a>
	</div>
	<div class="footer"></div>
</div>
<!---------------------------------------------------------------------------------------
     ////////////////////////////////////////////////////////////////////////////////////
     ///////////////// the main view. take care that it stretches. //////////////////////
     //////////////////////////////////////////////////////////////////////////////////// -->
<div id="mainview">
	<div class="buildingDescription">
		<h1>Missions</h1>
		<p>Send your spy an assignment</p>
	</div>
	<!-- ende .buildingDescription -->
	<div class="contentBox01h">
		<h3 class="header"><span class="textLabel">Choose a mission for the spy in {$safehouseMissions.desTownName}</span></h3>
		<div class="content" style="position:relative">
			<div class="percentage">{$safehouseMissions.currentRisk}%</div>
			<h4><span class="textLabel">Current risk of detection:</span></h4>
			<div class="missionText">
				<div title="Risk of discovery:{$safehouseMissions.currentRisk}%" class="statusBar">
					<div style="width:{$safehouseMissions.currentRisk}%;" class="bar"></div>
				</div>
			</div>
			<ul id="missionlist">
				<!-- Schatzkammer -->
				<li class="gold">
					<div title="Title of mission" class="missionType">Spy out treasure chamber</div>
					<div title="Mission`s description" class="missionDesc">It won`t be easy to risk a glance into the town`s treasure chamber. In return we will then know how much gold is stored there.
					</div>
					<div title="Costs of this mission" class="missionCosts">
						<strong>Costs:</strong> <span class="textLabel">Gold:</span> <span class="gold">45</span>
					</div>
					<div title="Risk of this mission" class="missionRisk"><strong>Risk:</strong>
						5%</div>
					<div title="4" class="missionStart">
						<div class="centerButton">
							<a class="button" href="index.php?action=Espionage&function=executeMission&actionRequest=e5ec223df0ab7766e92c4e7d723345f9&id=180406&position=6&spy=894609&mission=4">Start mission</a>
						</div>
					</div>
				</li>
				<!-- Lager -->
				<li class="resources">
					<div title="Title of mission" class="missionType">Inspect warehouse</div>
					<div title="Mission`s description" class="missionDesc">We can find out how many resources the town has by looking at the warehouse. We might then find out if an attack would pay off.
					</div>
					<div title="Costs of this mission" class="missionCosts"><strong>Costs</strong>
						<span class="textLabel">Gold:
						</span>
						<span class="gold">75</span>
					</div>
					<div title="Risk of this mission" class="missionRisk"><strong>Risk:</strong>5%</div>
					<div title="5" class="missionStart">
						<div class="centerButton">
							<a class="button" href="index.php?action=Espionage&function=executeMission&actionRequest=e5ec223df0ab7766e92c4e7d723345f9&id=180406&position=6&spy=894609&mission=5">Start mission</a>
						</div>
					</div>
				</li>
				<!-- Forschung -->
				<li class="research">
					<div title="Title of mission" class="missionType">Spy out level of research</div>
					<div title="Mission`s description" class="missionDesc">Our spy is smart enough to work as a scientist. That`s how he can also find out, how advanced the town`s research is.
					</div>
					<div title="Costs of this mission" class="missionCosts"><strong>Costs:</strong>
						<span class="textLabel">Gold:
						</span>
						<span class="gold">90</span>
					</div>
					<div title="Risk of this mission" class="missionRisk"><strong>Risk:</strong>
						5%</div>
					<div title="3" class="missionStart">
						<div class="centerButton">
							<a class="button" href="index.php?action=Espionage&function=executeMission&actionRequest=e5ec223df0ab7766e92c4e7d723345f9&id=180406&position=6&spy=894609&mission=3">Start mission</a>
						</div>
					</div>
				</li>
				<!-- Onlinestatus -->
				<li class="online">
					<div title="Title of mission" class="missionType">Online status</div>
					<div title="Mission`s description" class="missionDesc">With a little luck we can find out if the leader currently has an eye on his people.
					</div>
					<div title="Costs of this mission" class="missionCosts"><strong>Costs:</strong>
						<span class="textLabel">Gold:
						</span>
						<span class="gold">240</span>
					</div>
					<div title="Risk of this mission" class="missionRisk"><strong>Risk:</strong>
						14%</div>
					<div title="21" class="missionStart">
						<div class="centerButton">
							<a class="button" href="index.php?action=Espionage&function=executeMission&actionRequest=e5ec223df0ab7766e92c4e7d723345f9&id=180406&position=6&spy=894609&mission=21">Start mission</a>
						</div>
					</div>
				</li>
				<!-- Garnison -->
				<li class="garrison">
					<div title="Title of mission" class="missionType">Spy out garrison</div>
					<div title="Mission`s description" class="missionDesc">If we hide at the right spot during the morning roll call, we can count how many soldiers are stationed in this garrison. Thus we could plan an attack much more precisely!
					</div>
					<div title="Costs of this mission" class="missionCosts"><strong>Costs:</strong>
						<span class="textLabel">Gold:
						</span>
						<span class="gold">150</span>
					</div>
					<div title="Risk of this mission" class="missionRisk"><strong>Risk:</strong>
						34%</div>
					<div title="6" class="missionStart">
						<div class="centerButton">
							<a class="button" href="index.php?action=Espionage&function=executeMission&actionRequest=e5ec223df0ab7766e92c4e7d723345f9&id=180406&position=6&spy=894609&mission=6">Start mission</a>
						</div>
					</div>
				</li>
				<!-- Flottenbewegungen -->
				<li class="fleet">
					<div title="Title of mission" class="missionType">Observe fleet and troop movements</div>
					<div title="Mission`s description" class="missionDesc">If we keep one eye on the town gate and another on the port, we can find out what the citizens are up to. We can find out with whom they are at war with and who they trade with.
					</div>
					<div title="Costs of this mission" class="missionCosts"><strong>Costs:</strong>
						<span class="textLabel">Gold:
						</span>
						<span class="gold">750</span>
					</div>
					<div title="Risk of this mission" class="missionRisk"><strong>Risk:</strong>
						44%</div>
					<div title="7" class="missionStart">
						<div class="centerButton">
							<a class="button" href="index.php?action=Espionage&function=executeMission&actionRequest=e5ec223df0ab7766e92c4e7d723345f9&id=180406&position=6&spy=894609&mission=7">Start mission</a>
						</div>
					</div>
				</li>
				<!-- Nachrichten -->
				<li class="message">
					<div title="Title of mission" class="missionType">Observe communication</div>
					<div title="Mission`s description" class="missionDesc">If our spy gets himself hired as a messenger, he can report to us whom our target exchanges messages and trades with!
					</div>
					<div title="Costs of this mission" class="missionCosts"><strong>Costs:</strong>
						<span class="textLabel">Gold:
						</span>
						<span class="gold">360</span>
					</div>
					<div title="Risk of this mission" class="missionRisk"><strong>Risk:</strong>
						54%</div>
					<div title="10" class="missionStart">
						<div class="centerButton">
							<a class="button" href="index.php?action=Espionage&function=executeMission&actionRequest=e5ec223df0ab7766e92c4e7d723345f9&id=180406&position=6&spy=894609&mission=10">Start mission</a>
						</div>
					</div>
				</li>
				<!-- RŸckzug -->
				<li class="retreat">
					<div title="Title of mission" class="missionType">Recall spy</div>
					<div title="Mission`s description" class="missionDesc">We can recall the spy at any time. His return shouldn`t be noticed in the town.
					</div>
					<div title="Costs of this mission" class="missionCosts"><strong>Costs:</strong>
						<span class="textLabel">Gold:
						</span>
						<span class="gold">0</span>
					</div>
					<div title="Risk of this mission" class="missionRisk"><strong>Risk:</strong>
						5%</div>
					<div title="8" class="missionStart">
						<div class="centerButton">
							<a class="button" href="index.php?action=Espionage&function=executeMission&actionRequest=e5ec223df0ab7766e92c4e7d723345f9&id=180406&position=6&spy=894609&mission=8">Start mission</a>
						</div>
					</div>
				</li>
			</ul>
		</div>
		<div class="footer"></div>
	</div>
</div>