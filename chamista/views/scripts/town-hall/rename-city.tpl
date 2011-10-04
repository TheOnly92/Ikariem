<!---------------------------------------------------------------------------------------
	 ////////////////////////////////////////////////////////////////////////////////////
	 ///////////////////////////// dynamic side-boxes. //////////////////////////////////
	 //////////////////////////////////////////////////////////////////////////////////// -->
<div id="backTo" class="dynamic">
	<h3 class="header">Town hall</h3>
	<div class="content">
		<a href="/townHall?id={$town_id}&position=0" title="Back to the Town hall">
		<img src="http://static.ikariem.org/img/buildings/y100/townHall.gif" width="160" height="100" />
		<span class="textLabel">&lt;&lt; Back to the Town hall</span></a>
	</div>
	<div class="footer"></div>
</div>	 

<!---------------------------------------------------------------------------------------
	 ////////////////////////////////////////////////////////////////////////////////////
	 ///////////////// the main view. take care that it stretches. //////////////////////
	 //////////////////////////////////////////////////////////////////////////////////// -->
<div id="mainview">
	<h1 style="text-align:center">Town hall</h1>
	<form action="/townHall/rename?id={$town_id}"  method="post">
	<div id="renameCity" class="contentBox01h">
		<h3 class="header">Rename town</h3>
		<div class="content">
			<div class="oldCityName"><span class="textLabel">Old town name: </span>{$town_name}</div>
			<div class="newCityName"><label for="newCityName">New Town Name: </label><input type="text" class="textfield" id="newCityName" name="town_name" size="30" maxlength="15"/> <input type="submit" class="button" value="Accept town name" /></div>
		</div><!--end .content -->
		<div class="footer"></div>
	</div><!-- end .contentBox01 -->
	</form>
</div><!-- end #mainview -->