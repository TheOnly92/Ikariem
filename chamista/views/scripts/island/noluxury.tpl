<!---------------------------------------------------------------------------------------
	 ////////////////////////////////////////////////////////////////////////////////////
	 ///////////////////////////// dynamic side-boxes. //////////////////////////////////
	 //////////////////////////////////////////////////////////////////////////////////// -->
<div id="backTo" class="dynamic">
	<h3 class="header"></h3>
	<div class="content">
		<a href="/island?id={$island_id}" title="Back to the island">
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
	<div class="buildingDescription" style="background:url(/img/img/island/resource_{$resource_css}.gif) no-repeat right 10px; min-height:180px;">  
		<h1>{$resource_mine}</h1>
		<p class="description">{$description}</p>
	</div><!-- end buildingDescription -->
</div><!-- #mainview -->
