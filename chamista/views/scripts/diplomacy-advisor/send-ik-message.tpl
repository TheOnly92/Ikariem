<div id="backTo" class="dynamic" style="z-index:1">
	<h3 class="header">Diplomatic Advisor</h3>
	<div class="content">
		<a href="/diplomacyAdvisor" title="Diplomatic Advisor">
			<img style="max-width:100%" src="http://static.ikariem.org/img/img/action_back.gif" width="160" height="100" />
			<span class="textLabel">&lt;&lt; Diplomatic Advisor</span>
		</a>
	</div>
	<div class="footer"></div>
</div>
<div id="mainview">
	<h1>Write message</h1>
	<p>Here you can write a message to other players or offer them a treaty, as long as you have already researched the various forms of treaties.</p>
	<div id="notice">
		<form action="/diplomacyAdvisor/send?receiverId={$receiver_id}" method="post">
		<div id="mailRecipient">
			<span class="maillabels"><label>Receiver:</label></span>
			<span>{$receiver_name}</span>
		</div>
		<div id="mailSubject">
			<span class="maillabels"><label for="treaties">Subject:</label></span>
			<span>
				<select name="msgType" id="treaties">
					<option value="0" selected="selected">Message</option>
				</select>
			</span>
		</div>
		<span class="maillabels"><label for="text">Message:</label></span><br />
		<span><textarea id="text" class="textfield" name="content" >{$content}</textarea></span><br />
		<div id="nr_chars_div" style="display:none">Remaining <span id="nr_chars"></span>&nbsp;characters available.</div>
		<div class="centerButton">
			<input type="submit" class="button" onclick="return confirmIfNeccessary(document.getElementById('treaties').value,'Are you sure?')" title="Submit" value="Submit">
		</div>
		</form>
	</div>
	<script type="text/javascript">{literal}
	function strLen(str) {
		var newStr = str.replace(/(\r\n)|(\n\r)|\r|\n/g,"\n");
		return newStr.length;
	}
	
	// Daten aus PHP einlesen:
	messagesThatNeedConfirm = new Array();
	messagesThatNeedConfirm[0] = 64;
	messagesThatNeedConfirm[1] = 70;
	messagesThatNeedConfirm[2] = 75;
	messagesThatNeedConfirm[3] = 76;
	messagesThatNeedConfirm[4] = 81;
	messagesThatNeedConfirm[5] = 87;
	messagesThatNeedConfirm[6] = 94;
	messagesThatNeedConfirm[7] = 93;
	messagesThatNeedConfirm[8] = 99;
	// Funktion zur Anzeige einer Sicherheitsabfrage
	function confirmIfNeccessary(msgType,confirmText) {
		var confirm = false;
		for (elem in messagesThatNeedConfirm) {
			if (messagesThatNeedConfirm[elem] == msgType) {
				confirm = true;
			}
		}
		if (confirm == true) {
			return window.confirm (confirmText);
		} else {
			return true;
		}
	};
	
	YAHOO.util.Event.addListener("text", "keyup", function() {
		var nr_chars = 8000-strLen(document.getElementById('text').value);
		if (nr_chars<0) {
			document.getElementById('nr_chars').innerHTML='<span style="color: #f00; font-weight: bold">' + nr_chars + '</span>';
		} else {
			document.getElementById('nr_chars').innerHTML=nr_chars;
		}
		document.getElementById('nr_chars_div').style.display='block';
	});{/literal}
	</script>
</div>
