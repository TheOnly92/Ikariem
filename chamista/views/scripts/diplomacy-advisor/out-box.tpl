<!---------------------------------------------------------------------------------------
     ////////////////////////////////////////////////////////////////////////////////////
     ///////////////////////////// dynamic side-boxes. //////////////////////////////////
     //////////////////////////////////////////////////////////////////////////////////// -->

<div class="dynamic">
	<h3 class="header">Influence</h3>
	<div class="content">
		<p>Diplomacy Points: 3</p>
	</div>
	<div class="footer"></div>
</div>

<div class="dynamic" id="viewDiplomacyImperium">
	<h3 class="header">Diplomacy Overview</h3>
	<div class="content">
		<img src="http://static.ikariem.org/img/premium/sideAd_premiumDiplomacyAdvisor.jpg" width="203" height="85" />
		<p>Words don`t come that easy? You will certainly find it much easier to negotiate, when you know more about your opponents!</p>
		<div class="centerButton">
			<a href="?view=premiumDetails" class="button">Have a look now!</a>
		</div>
	</div>
	<div class="footer"></div>
</div>

<div class="dynamic" id="islandBoardOverview">
	<h3 class="header">Agora</h3>
	<div class="content">
		<ul>
			<li><a href="?view=islandBoard&id=1534">Etyos [Wine Fountain]</a></li><li><a href="?view=islandBoard&id=1535">Schulyios [Rocky Mountain]</a></li><li><a href="?view=islandBoard&id=1536">Nausios [Crystal Hill]</a></li>
		</ul>
	</div>
	<div class="footer"></div>
</div>
  

<!---------------------------------------------------------------------------------------
	 ////////////////////////////////////////////////////////////////////////////////////
	 ///////////////// the main view. take care that it stretches. //////////////////////
	 //////////////////////////////////////////////////////////////////////////////////// -->
<div id="mainview">
	<div id="diplomacyDescription" class="buildingDescription">
		<h1>Diplomatic Advisor</h1>
		<p></p>
	</div>
	<div class="diplomacyAdvisorTabs">
		<table cellpadding="0" cellspacing="0" id="tabz">
			<tr>
				<td><a href="/diplomacyAdvisor" title="Inbox"><em>Inbox ({$total_in})</em></a></td>
				<td class="selected"><a href="?view=diplomacyAdvisorOutBox" title="Outbox"><em>Outbox ({$total_out})</em></a></td>
				<td><a href="?view=diplomacyAdvisorTreaty" title="Treaty Overview"><em>Treaty</em></a></td>
				<td><a href="?view=diplomacyAdvisorAlly" title="Information about your alliance"><em>Alliance</em></a></td>
			</tr>
		</table>
		</div>
	<div id="tab2">
		<!---------------------------------------------------------------------------------------
		 ////////////////////////////////////////////////////////////////////////////////////
		 ///////////////////////////// Nachrichten outbox	////////////////////////////////
		 //////////////////////////////////////////////////////////////////////////////////// -->
		<script type="text/javascript" language="javascript">{literal}
		function markAll(command) {
			var allInputs = document.getElementById('deleteMessages').getElementsByTagName('input');
			for (var i=0; i<allInputs.length; i++) {
				if (allInputs[i].getAttribute('type') == "checkbox") {
					if (command == 'all') {
						allInputs[i].checked = true;
					}
					if (command == 'unread'){
						if (allInputs[i].value=='unread') {
							allInputs[i].checked = true;
						} else {
							allInputs[i].checked = false;
						}
					}
					if (command == 'read'){
						if(allInputs[i].value=='read') {
							allInputs[i].checked = true;
						} else {
							allInputs[i].checked = false;
						}
					}
					if (command == 'none') {
						allInputs[i].checked = false;
					}
				}
			}
		}
		
		function show_hide_menus(ele)	{
			if (document.getElementById('tbl_'+ele).style.display=='') {
				document.getElementById('tbl_'+ele).style.display='none';
			} else {
				document.getElementById('tbl_'+ele).style.display='';
			}
		}
		
		function imgtoggle(obj){
			if(obj.className=="open"){
				obj.className="close";
				obj.src="http://static.ikariem.org/img/layout/up-arrow.gif";
			}else{
				obj.className="open";
				obj.src="http://static.ikariem.org/img/layout/down-arrow.gif";
			}
		}{/literal}
		</script>
		<div class="contentBox01">
			<h3 class="header"><span class="textLabel">Messages</span></h3>
			<div class="content">
				<form action="/diplomacyAdvisor/processUserMessage?pos=outbox" method="post" name="deleteMessages" id="deleteMessages">
				<table cellpadding="0" cellspacing="0" class="table01" style="width:100%;margin:0px;border:none;">
				{if count($messages) > 0}
					<tr>
						<th>Action</th>
						<th></th>
						<th>To</th>
						<th>Subject</th>
						<th>Date</th>
					</tr>
					{foreach from=$messages item=message}
					<tr title="Click here to show/hide the message!" class="entry" onMouseOver="this.bgColor='#ECD5AC'" onMouseOut="this.bgColor='#FDF7DD'">
						<td><input type="checkbox" name="deleteId[{$message.id}]" value="1" /></td>
						<td onclick="show_hide_menus('mail{$message.id}');imgtoggle(getElementById('button{$message.id}'));">
							<img class="open" alt="" id="button{$message.id}" name="buton{$message.id}" src="http://static.ikariem.org/img/layout/down-arrow.gif" />
						</td>
						<td onclick="show_hide_menus('mail{$message.id}');imgtoggle(getElementById('button{$message.id}'));">
							<a href="#">{$message.receiver_name}</a>
						</td>
						<td class="subject" onclick="show_hide_menus('mail{$message.id}');imgtoggle(getElementById('button{$message.id}'));">
							{if $message.subject == 0}Message{/if}
						</td>
						<td onclick="show_hide_menus('mail{$message.id}');imgtoggle(getElementById('button{$message.id}'));">
							{$message.date}
						</td>
					</tr>
					<tr id="tbl_mail{$message.id}" style="display:none;" class="text">
						<td colspan="5" class="msgText">
							<div style="overflow: auto; width: 100%;">
								{$message.body|nl2br}
							</div>
							<br />
							<span style="float:right;padding:5px;margin-right:5px;"><a style="bottom:3px;" href="/premium/archiveReport/id/{$message.id}/type/4" class="button center">Save in archive</a>
							&nbsp; (<span class="costAmbrosia" style="padding-top:5px;padding-bottom:5px;font-weight:bold;padding-left:5px;padding-right:22px;background-image:url(/img/premium/ambrosia_icon.gif);background-repeat:no-repeat;background-position:100% 50%;###">1</span>)</span>
						</td>
					</tr>
					{/foreach}
					<tr>
						<td colspan="5" class="paginator">
							{if ($offset-1)*10+1 > 1}
							<a href="/diplomacyAdvisor/outBox?offset={$offset-1}">...last 10 <img src="http://static.ikariem.org/img/img/resource/btn_min.gif"/></a>
							{/if}
							{math equation="(x-1)*10+1" x=$offset} - {$disp_messages}
							{if $total_out > $disp_messages}
							<a href="/diplomacyAdvisor/outBox?offset={$offset+1}"><img src="http://static.ikariem.org/img/img/resource/btn_max.gif"/> next 10...</a>
							{/if}
						</td>
					</tr>
					<tr>
						<td colspan="6" style="text-align:left;">
							<a href="javascript:markAll('all');">All</a> |
							<a href="javascript:markAll('none');">None</a>
						</td>
					</tr>
					<tr>
						<td class="selection" colspan="6" style="text-align:left;">
							<input type="hidden" name="pos" value="outbox"/>
							<input type="submit" name="666" value="Delete" class="button" />
						</td>
					</tr>
				{else}
				<tr><th colspan="6"><p>No messages available.</p></th></tr>
				{/if}
				</table>
				</form>
			</div>
			<div class="footer"></div>
		</div>
		
		<div class="dynamic" id="viewDiplomacyImperium" style="z-index:1;">
			<h3 class="header">Message Archive</h3>
			<div class="content">
				<div class="centerButton">
					<div>
						<a class="button" href="?view=diplomacyAdvisorArchive" title="Inbox"><b>Inbox</b></a>
						<a class="button" href="?view=diplomacyAdvisorArchiveOutBox" title="Outbox"><b>Outbox</b></a>
					</div>
				</div>
			</div>
			<div class="footer"></div>
		</div>
	</div>
</div>
