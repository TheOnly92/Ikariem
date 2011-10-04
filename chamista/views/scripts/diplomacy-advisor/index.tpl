<!---------------------------------------------------------------------------------------
     ////////////////////////////////////////////////////////////////////////////////////
     ///////////////////////////// dynamic side-boxes. //////////////////////////////////
     //////////////////////////////////////////////////////////////////////////////////// -->

<div class="dynamic">
	<h3 class="header">Influence</h3>
	<div class="content">
		<p>Diplomacy Points: 0</p>
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
		<li><a href="?view=islandBoard&id=3751">Roditia [Polis]</a></li>
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
			<td class="selected"><a href="/diplomacyAdvisor" title="Inbox"><em>Inbox ({$total_in})</em></a></td>
			<td><a href="/diplomacyAdvisor/outBox" title="Outbox"><em>Outbox ({$total_out})</em></a></td>
			<td><a href="?view=diplomacyAdvisorTreaty" title="Treaty Overview"><em>Treaty</em></a></td>
			<td><a href="?view=diplomacyAdvisorAlly" title="Information about your alliance"><em>Alliance</em></a></td>
			<!-- <td><a href="?view=diplomacyAdvisorArchive" title="Inbox"><em>Inbox</em></a></td>
			<td><a href="?view=diplomacyAdvisorArchiveOutBox" title="Outbox"><em>Outbox</em></a></td> -->
		</tr>
		</table>
	
	</div>
	<div id="tab1">
		<!---------------------------------------------------------------------------------------
		 ////////////////////////////////////////////////////////////////////////////////////
		 ///////////////////////////// Nachrichten	 //////////////////////////////////
		 //////////////////////////////////////////////////////////////////////////////////// -->
		<!------------------------------------------------------------------------------------->
		<script type="text/javascript" language="javascript">
		{literal}
		function show_hide_menus(ele)	{
			if (document.getElementById('tbl_'+ele).style.display=='') {
				document.getElementById('tbl_'+ele).style.display='none';
				// setCookie( ele, "none", 1);
				// var imgsource = plus_minus_img + ele + '.gif';
				// document.getElementById('img_'+ele).src=imgsource;
			} else {
				document.getElementById('tbl_'+ele).style.display='';
				//  setCookie( ele, "", 1);
				//  var imgsource = plus_minus_img + ele + '_m.gif';
				//  document.getElementById('img_'+ele).src=imgsource;
			}
		}
		
		
		function imgtoggle(obj) {
			if(obj.className=="open") {
				obj.className="close";
				obj.src="http://static.ikariem.org/img/layout/up-arrow.gif";
			} else if(obj.className=="close") {
				obj.className="open";
				obj.src="http://static.ikariem.org/img/layout/down-arrow.gif";
			} else if(obj.className=="gopen") {
				obj.className="gclose";
				obj.src="http://static.ikariem.org/img/layout/up-arrow.gif";
			} else {
				obj.className="gopen";
				obj.src="http://static.ikariem.org/img/layout/down-arrow.gif";
			}
		}
		
		function markAsRead(mid, funcname) {
			var newClass = 'entry globalmessage';
			if (!funcname) { funcname = 'markMessageAsRead'; newClass = 'entry'; }
			callback = null;
			sUrl = '/diplomacyAdvisor/'+funcname+'?id='+ mid;
			ajaxSendUrl(sUrl);
			document.getElementById('message'+mid).className=newClass;
		}
		
		
		function markAll(command) {
			var allInputs = document.getElementById('deleteMessages').getElementsByTagName('input');
			for (var i=0; i<allInputs.length; i++) {
				if (allInputs[i].getAttribute('type') == "checkbox") {
					if (command == 'all') {
						allInputs[i].checked = true;
					}
					if (command == 'unread') {
						if (allInputs[i].value=='unread') {
							allInputs[i].checked = true;
						} else {
							allInputs[i].checked = false;
						}
					}
					if (command == 'read') {
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
		}{/literal}
		</script>
		<div id="messages">
			<div class="contentBox01">
				<h3 class="header"><span class="textLabel">Messages</span></h3>
				<div class="content">
				
					<form action="/diplomacyAdvisor/processUserMessage?pos=inbox" method="post" name="deleteMessages" id="deleteMessages">
					<table cellpadding="0" cellspacing="0" class="table01" id="messages"  style="width:100%;margin:0px;border:none;">
						{if count($messages) > 0}
						<tr>
							<th>Action</th>
							<th></th>
							<th>Sender</th>
							<th>Subject</th>
							
							<th>Town</th>
							<th>Date</th>
						</tr>
						
						{foreach from=$messages item=message}
						<tr id="message{$message.id}" onMouseOver="this.bgColor='#ECD5AC'" onMouseOut="this.bgColor='#FDF7DD'" title="Click here to expand/hide the message" class="entry {if $message.new}new{/if}"{if $message.new} onClick ="markAsRead({$message.id});"{/if}>
							<td><input type="checkbox" name="deleteId[{$message.id}]" value="read" /></td>
							<td onclick="show_hide_menus('mail{$message.id}'); show_hide_menus('reply{$message.id}'); imgtoggle(getElementById('button{$message.id}'));">
								<img class="open" alt="" id="button{$message.id}" name="button{$message.id}" src="http://static.ikariem.org/img/layout/down-arrow.gif" />
							</td>
							<td onclick="show_hide_menus('mail{$message.id}'); show_hide_menus('reply{$message.id}'); imgtoggle(getElementById('button{$message.id}'));">
								<a href="#">{$message.sender_name}</a>
							</td>
							<td class="subject" onclick="show_hide_menus('mail{$message.id}'); show_hide_menus('reply{$message.id}'); imgtoggle(getElementById('button{$message.id}'));">
								{if $message.subject == 0}Message{/if}
							</td>
							<td title="To the town of the sender"><a href="/island?id={$message.town.island}&selectCity={$message.town.id}">{$message.town.name} [{$message.town.pos.x}:{$message.town.pos.y}]</a></td>
							<td onclick="show_hide_menus('mail{$message.id}'); show_hide_menus('reply{$message.id}'); imgtoggle(getElementById('button{$message.id}'));">
								{$message.date}
							</td>
						</tr>
						<tr id="tbl_mail{$message.id}" style="display:none;" class="text">
							<td colspan="6" class="msgText">
								<div style="overflow: auto; width: 100%;">{$message.body|nl2br}</div>
							</td>
						</tr>
						<tr id="tbl_reply{$message.id}" style="display:none;" class="text">
							<td colspan="6" class="reply">
								<span style="float:left;padding:5px;maring-left:5px;">
									<a class="button" href="/diplomacyAdvisor/sendIKMessage?receiverId={$message.sender_id}&replyTo={$message.id}">Answer</a>
									<a class="button" href="/diplomacyAdvisor/markAsDeletedByReceiver?id={$message.id}">Delete</a>
									<a class="button" href="/diplomacyAdvisor/forwardToGameOperator?id={$message.id}" title="This function forwards insults and offences to the Game-Operator. Do you really want to forward this message to the GO?" onClick="return(confirm('This function forwards insults and offences to the Game-Operator. Do you really want to forward this message to the GO?'))">report</a>
								</span>
								<span style="float:right;padding:5px;margin-right:5px;">
									<a href="/premium/archiveReport?id={$message.id}&type=3" class="button">Save in archive</a>
									&nbsp; (<span class="costAmbrosia" style="padding-top:5px;padding-bottom:5px;font-weight:bold;paddig-left:5px;padding-right:22px;background-image:url(skin/premium/ambrosia_icon.gif);background-repeat:no-repeat;background-position:100% 50%;###">1</span>)
								</span>
							</td>
						</tr>
						{/foreach}
						<tr>
							<td colspan="5" class="paginator">
								{if ($offset-1)*10+1 > 1}
								<a href="/diplomacyAdvisor?offset={$offset-1}">...last 10 <img src="http://static.ikariem.org/img/img/resource/btn_min.gif"/></a>
								{/if}
								{math equation="(x-1)*10+1" x=$offset} - {$disp_messages}
								{if $total_in > $disp_messages}
								<a href="/diplomacyAdvisor?offset={$offset+1}"><img src="http://static.ikariem.org/img/img/resource/btn_max.gif"/> next 10...</a>
								{/if}
							</td>
						</tr>
						
						<!--<div style="margin:0 auto; text-align:center;"-->
						<tr>
						
							<td colspan="6" style="text-align:left;">
								<a href="javascript:markAll('all');">All</a> |
								<a href="javascript:markAll('read');">Read</a> |
								<a href="javascript:markAll('unread');">Unread</a> |
								<a href="javascript:markAll('none');">None</a>
							</td>
						</tr>
						
						<tr>
							<td class="selection" colspan="6" style="text-align:left;" width="50%">
								<input type="submit" name="555"	value="Mark as read" class="button" />
								<input type="submit" name="666" value="Delete"		 class="button" />
							</td>
						</tr>
						{else}
						<tr><th colspan="6"><p>No messages available.</p></th></tr>
						{/if}
					</table>
					</form><!--</div-->
				
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
</div>
