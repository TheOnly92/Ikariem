function changeAction(type) {    
	if (type != "register" && document.loginForm.universe.value == '') {
		alert('No action selected');
	} else {
		if(type == "login") {
			var url = "http://" + document.loginForm.universe.value + "/index/login";
			document.getElementById('loginForm').action = url;		
		} else if (type=="getpw") {
			var url = "http://" + document.loginForm.universe.value + "/game/reg/mail.php";
			document.loginForm.action = url;
		    document.loginForm.submit();
		} else if(type == "register") {
			var url = "http://" + document.registerForm.universe.value + "/register";
			document.registerForm.action = url;
		}
	}
}

function showInfo(id) {
	printMessage(id, 'infotext');
}

function printMessage(code, div) {
	var textclass = "";
	if (div == null) {
		div = "statustext";
	}
    
	switch (code) {
		case "0":
			text = "";
			textclass = "fine"; 
			break;
		case "101":
			text = ""; 
			textclass = "warning"; 
			break;
		case "102":
			text = "";
			textclass = "warning"; 
			break;
		case "103":
			text = "";
			textclass = "warning"; 
			break;
		case "104":
			text = "";
			textclass = "warning"; 
			break;
		case "105":
			text = "";
			textclass = "fine"; 
			break;
		case "106":
			text = "";
			textclass = "fine"; 
			break;
		case "107":
			text = "";
			textclass = "warning"; 
			break;
		case "201":
			text = "Name in the game:<br />The name you want to use as a leader in the game. It can only used once per game-world.";
			break;
		case "202":
			text = "E-Mail address:<br />Enter a valid e-mail address, in order to confirm your player`s account. You have three days time, in which you may already play.";
			break;
		case "203":
			text = "203";
			break;
		case "204":
			text = "T&C:<br />Accept the Terms and Conditions (T&C), in order to play Ikariem";
			break;
		case "205":
			text ="Password:<br/>The password protects your player`s account from unauthorized access. Don`t hand on your password. The password needs to have between 8 and 15 characters!";
			break;
		default:
			text = code;
	}
	
	if (textclass != "") {
		text = "<span class='" + textclass + "'>" + text + "</span>";
	}
	document.getElementById(div).innerHTML = text;
}

function getServerNotice(serverName, div) {
	notices = Array();
	notices['s1.Ikariem.com']='';
	if(typeof(notices[serverName]) != 'undefined') { 
		document.getElementById(div).innerHTML =  notices[serverName];
	} else {
		document.getElementById(div).innerHTML = "";
	}
}