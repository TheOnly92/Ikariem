<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="language" content="de" />
        <meta name="author" content="Gameforge AG" />
        <meta name="publisher" content="Gameforge AG" />
        <meta name="copyright" content="Gameforge AG" />
        <meta name="page-type" content="Browsergame, Browser game" />
        <meta name="page-topic" content="Browser game, strategy game, online game" />

        <meta name="audience" content="all" />
        <meta name="Expires" content="never" />
        <meta name="Keywords" content="Ikariem, antique world, strategy game, play for free, online game, role play game, browser game, game"/>
        <meta name="Description" content="Ikariem is a free browser game. The mission is to lead a nation through the ancient world, building up towns, trade and conquer islands." />
        <meta name="robots" content="index,follow" />
        <meta name="Revisit" content="After 14 days" />
        <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
        <title>Ikariem</title>

		<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
        <link href="http://static.ikariem.org/css/ik_common_{$smarty.const.VERSION}.css" rel="stylesheet" type="text/css" media="screen" />
        {if isset($css)}
        <link href="http://static.ikariem.org/css/{$css}" rel="stylesheet" type="text/css" media="screen" />
        {/if}
        <script type="text/javascript" src="http://static.ikariem.org/js/complete-{$smarty.const.VERSION}.js"></script>
		<script type="text/javascript">
		/* <![CDATA[ */
		var Event = YAHOO.util.Event,
		Dom   = YAHOO.util.Dom,
		lang  = YAHOO.lang;
		{literal}
		var LocalizationStrings = {};
		LocalizationStrings['timeunits'] = {};
		LocalizationStrings['timeunits']['short'] = {};
		LocalizationStrings['timeunits']['short']['day'] = 'D';
		LocalizationStrings['timeunits']['short']['hour'] = 'h';
		LocalizationStrings['timeunits']['short']['minute'] = 'm';
		LocalizationStrings['timeunits']['short']['second'] = 's';
		LocalizationStrings['language']                     = 'en';
		LocalizationStrings['decimalPoint']               = '.';
		LocalizationStrings['thousandSeperator']     = ',';
		
		LocalizationStrings['resources'] = {};
		LocalizationStrings['resources']['wood'] = 'Building material';
		LocalizationStrings['resources']['wine'] = 'Wine';
		LocalizationStrings['resources']['marble'] = 'Marble';
		LocalizationStrings['resources']['crystal'] = 'Crystal Glass';
		LocalizationStrings['resources']['sulfur'] = 'Sulphur';
		LocalizationStrings['resources'][0] = LocalizationStrings['resources']['wood'];
		LocalizationStrings['resources'][1] = LocalizationStrings['resources']['wine'];
		LocalizationStrings['resources'][2] = LocalizationStrings['resources']['marble'];
		LocalizationStrings['resources'][3] = LocalizationStrings['resources']['crystal'];
		LocalizationStrings['resources'][4] = LocalizationStrings['resources']['sulfur'];
		
		LocalizationStrings['warnings'] = {};
		LocalizationStrings['warnings']['premiumTrader_lackingStorage'] = "Für folgende Rohstoffe fehlt dir Speicherplatz: $res";
		LocalizationStrings['warnings']['premiumTrader_negativeResource'] = "Du hast zuwenig $res für diesen Handel";
		LocalizationStrings['warnings']['tolargeText'] = 'Warning! Your text is longer than allowed!';
		
		Ikariem = {
				phpSet : {
						serverTime : "{/literal}{$smarty.now}",
						currentView : "{$body_id}{literal}"
						},
				currentCity : {
						resources : {{/literal}
								wood: {$layout.resources.wood},
								wine: {$layout.resources.wine},
								marble: {$layout.resources.marble},
								crystal: {$layout.resources.crystal},
								sulfur: {$layout.resources.sulfur}
						{literal}},
						maxCapacity : {{/literal}
								wood: {$layout.maxCapacity.wood},
								wine: {$layout.maxCapacity.wine},
								marble: {$layout.maxCapacity.marble},
								crystal: {$layout.maxCapacity.crystal},
								sulfur: {$layout.maxCapacity.sulfur}
						{literal}}
				},
				view : {
						get : function() {
								return Ikariem.phpSet.currentView;
								},
						is : function(viewName) {
								return (Ikariem.phpSet.currentView == viewName)? true : false;
								}
						}
				};
				Ikariem.time = {
						serverTimeDiff : Ikariem.phpSet.serverTime*1000-(new Date()).getTime()
				};
		
		
		
		/**
		* switches one item on and the other off.. but only if they share the same groupname.
		*/
		selectGroup = {
			groups:new Array(), //[groupname]=item
			getGroup:function(group) {
				if(typeof(this.groups[group]) == "undefined") {
					this.groups[group] = new Object();
					this.groups[group].activeItem = "undefined";
					this.groups[group].onActivate = function(obj) {};
					this.groups[group].onDeactivate = function(obj) {};
					}
				return this.groups[group];
			},
			activate:function(obj, group) {
				g = this.getGroup(group);
				if(typeof(g.activeItem) != "undefined") {
					g.onDeactivate(g.activeItem);
					}
				g.activeItem=obj;
				g.onActivate(obj);
			}
		};
		selectGroup.getGroup('cities').onActivate = function(obj) {
			YAHOO.util.Dom.addClass(obj.parentNode, "selected");
		}
		selectGroup.getGroup('cities').onDeactivate = function(obj) {
			YAHOO.util.Dom.removeClass(obj.parentNode, "selected");
		}

		/**
		 * - will COPY all child nodes of the source-node that are marked with a CSS class to be child nodes of the target.
		 * - will purge all children of the TARGET element that are marked the same special CSS class at each call, so previously copied will be deleted before copying new
		 * - expects either an Id or an object.
		 */
		function showInContainer(source, target, exchangeClass) {
			//objects or Id-strings, i don't care
			if(typeof source == "string") { source = Dom.get(source); }
			if(typeof target == "string") {target = Dom.get(target); }
			if(typeof exchangeClass != "string") { alert("Error: Ikariem.showInContainer -> Forgot to add an exchangeClass?"); }
			//removal
			for(i=0; i<target.childNodes.length; i++) {
				if(typeof(target.childNodes[i].className) != "undefined" && target.childNodes[i].className==exchangeClass) {
					target.removeChild(target.childNodes[i]);
				}
			}
			//clone new
			for(i=0; i<source.childNodes.length; i++) {
				if(typeof(source.childNodes[i].className) != "undefined" && source.childNodes[i].className==exchangeClass) {
					clone = source.childNodes[i].cloneNode(true);
					target.insertBefore(clone, target.firstChild.nextSibling);
				}
			}
		}

		selectedCity = -1;
		function selectCity(cityNum, cityId, viewAble) {
		    if(selectedCity == cityNum) {
		        if(viewAble) document.location.href="/city?id="+cityId;
		        else document.location.href="#";
		    } else {
		        selectedCity = cityNum;
		    }
			showInContainer("cityLocation"+cityNum,"information", "cityinfo");
			showInContainer("cityLocation"+cityNum,"actions", "cityactions");
			var container = document.getElementById("cities");
			var citySelectedClass = "selected";
		}
		function selectBarbarianVillage() {
		  showInContainer("barbarianVillage","information", "cityinfo");
          showInContainer("barbarianVillage","actions", "cityactions");
          selectedCity = 0;
		}

		//IE6 CSS Background-Flicker fix
		(function(){
			/*Use Object Detection to detect IE6*/
			var  m = document.uniqueID /*IE*/
			&& document.compatMode  /*>=IE6*/
			&& !window.XMLHttpRequest /*<=IE6*/
			&& document.execCommand ;
			try{
				if(!!m){
					m("BackgroundImageCache", false, true) /* = IE6 only */
				}
			}catch(oh){};
		})();
		/* ]]> */

		function myConfirm(message, target) {
		    bestaetigt = window.confirm (message);
		    if (bestaetigt == true)
              window.location.href = target;
		}{/literal}
		</script>
	</head>
	<body id="{$body_id}">
		<div id="container">
			<div id="container2">
							<div id="header">
					<h1>Ikariem</h1>
					<h2>Live the ancient world!</h2>
				</div>
				<div id="avatarNotes"></div>
				{if $body_id != 'worldmap_iso'}
				<div id="breadcrumbs">
					<h3>You are here:</h3>
					{breadcrumbs trail=$trail separator="<span>&nbsp;&gt;&nbsp;</span>"}
				</div>
				{/if}
				{layout section="content"}
<!-- Navigational elements for changing the city or the view. May perform different actions on every screen. -->
<div id="cityNav">
<form id="changeCityForm" action="/city/changeCurrentCity?oldController={$layout.oldController}&oldAction={$layout.oldAction}{if isset($layout.oldId)}&id={$layout.oldId}{/if}{if isset($layout.oldPos)}&position={$layout.oldPos}{/if}" method="POST">

<!-- Navigation -->
<h3>Town navigation</h3>
<ul>
	<li><label for="citySelect">Current town:</label> <select id="citySelect" class="citySelect smallFont" name="cityId" tabindex="1" onchange="this.form.submit()">
		{if $usr_citySelectOptions == 0}
		{foreach from=$cities item=city}
			<option class="" value="{$city.town_id}" title=""{if $city.town_id == $layout.townId} selected="selected"{/if}>{$city.town_name}</option>
		{/foreach}
		{elseif $usr_citySelectOptions == 1}
		{foreach from=$cities item=city}
			<option class="coords" value="{$city.town_id}" title="Trade good: {$city.tradegood_name}"{if $city.town_id == $layout.townId} selected="selected"{/if}>[{$city.posx}:{$city.posy}]&nbsp;{$city.town_name}</option>
		{/foreach}
		{elseif $usr_citySelectOptions == 2}
		{foreach from=$cities item=city}
			<option class="tradegood{$city.tradegood}" value="{$city.town_id}" title="[{$city.posx}:{$city.posy}]"{if $city.town_id == $layout.townId} selected="selected"{/if}>{$city.town_name}</option>
		{/foreach}
		{/if}
		</select></li>
		<li class="previousCity"><a href="#changeCityPrevious" tabindex="2" title="Switch to the last town"><span class="textLabel">Previous Town</span></a></li>
		<li class="nextCity"><a href="#changeCityNext" tabindex="3" title="Switch to the following town"><span class="textLabel">Next Town</span></a></li>
		<li class="viewWorldmap"><a href="/worldmap" tabindex="4" title="Centre the selected town on the World Map"><span class="textLabel">Show World</span></a></li>
		<li class="viewIsland"><a href="/island?id={$layout.islandId}" tabindex="5" title="Switch to the island map of the selected town"><span class="textLabel">Show Island</span></a></li>
		<li class="viewCity"><a href="/city?id={$layout.townId}" tabindex="6" title="Inspect the selected town"><span class="textLabel">Show Town</span></a></li>
	</ul>
</form>
</div>

<!-- TODO Goldbalance... -->
<div id="globalResources">
<h3>Your empire`s ressources</h3>
<ul>
	<li class="transporters" title="Trade ships available/Total">
	<a href="/transport/merchantNavy"><span class="textLabel">Trade ships:</span><span>{$User->getFreeShips()} ({$User->usr_ships})</span></a></li>
	<li class="gold" title="{$User->usr_gold|number_format} Gold"><a href="/finances"><span class="textLabel">Gold:</span>
	<span id="value_gold">{if $User->usr_gold > 9999999}{$User->usr_gold/1000000|number_format} M{elseif $User->usr_gold > 999999}{$User->usr_gold/1000|number_format} k{else}{$User->usr_gold|number_format}{/if}</span></a></li>
</ul>

</div>

<!-- Resources of the city. Finished. Identical on every page. -->
<div id="cityResources"><h3>Town`s resources</h3>
<ul class="resources">
	<li class="population" title="Population"><span class="textLabel">Population: </span> <span id="value_inhabitants" style="display: block; width: 80px;">{$layout.freeCitizens|number_format} ({$layout.population|number_format})</span>
	</li>
	<li class="actions" title="Action Points">
	<span class="textLabel">Action Points: </span>
	<span id="value_maxActionPoints">{$layout.actionPoints}</span>
	</li>
	{assign var='wood' value=$layout.resources.wood}
	<li class="wood"><span class="textLabel">Building material:</span> <span id="value_wood" class="{if $wood == $layout.maxCapacity.wood}storage_full{elseif ($wood/$layout.maxCapacity.wood) > 0.75}storage_danger{/if}">{$layout.resources.wood|number_format}</span>
	<div class="tooltip">
		<span class="textLabel">Hourly production Building material: </span>{$layout.production.wood}<br />
		<span class="textLabel">Storage capacity Building material: </span>{$layout.maxCapacity.wood|number_format}
		{if $layout.tradingPost.wood > 0}
		<br />
		<span class="textLabel">Trading Post: </span>{$layout.tradingPost.wood|number_format}
		{/if}
	</div>
	</li>
	{assign var='wine' value=$layout.resources.wine}
	<li class="wine{if !$wealth} disabled{/if}">
	<span class="textLabel">Wine:</span> <span id="value_wine" class="{if $wine == $layout.maxCapacity.wine}storage_full{elseif ($wine/$layout.maxCapacity.wine) > 0.75}storage_danger{/if}">{$layout.resources.wine|number_format}</span>

	<div class="tooltip">
		{if $layout.resource == 'wine'}
		<span class="textLabel">Hourly production Wine: </span>{$layout.production.resource}<br />
		{/if}
		<span class="textLabel">Storage capacity Wine: </span>{$layout.maxCapacity.wine|number_format}
		{if $layout.tradingPost.wine > 0}
		<br />
		<span class="textLabel">Trading Post: </span>{$layout.tradingPost.wine|number_format}
		{/if}
	</div>
	</li>
	{assign var='marble' value=$layout.resources.marble}
	<li class="marble{if !$wealth} disabled{/if}"><span class="textLabel">Marble:</span> <span id="value_marble" class="{if $marble == $layout.maxCapacity.marble}storage_full{elseif ($marble/$layout.maxCapacity.marble) > 0.75}storage_danger{/if}">{$layout.resources.marble|number_format}</span>
	<div class="tooltip">
		{if $layout.resource == 'marble'}
		<span class="textLabel">Hourly production Marble: </span>{$layout.production.resource}<br />
		{/if}
		<span class="textLabel">Storage capacity Marble: </span>{$layout.maxCapacity.marble|number_format}
		{if $layout.tradingPost.marble > 0}
		<br />
		<span class="textLabel">Trading Post: </span>{$layout.tradingPost.marble|number_format}
		{/if}
	</div>
	</li>
	{assign var='glass' value=$layout.resources.crystal}
	<li class="glass{if !$wealth} disabled{/if}"><span class="textLabel">Crystal Glass:
	</span> <span id="value_crystal" class="{if $glass == $layout.maxCapacity.crystal}storage_full{elseif ($glass/$layout.maxCapacity.crystal) > 0.75}storage_danger{/if}">{$layout.resources.crystal|number_format}</span>
	<div class="tooltip">
		{if $layout.resource == 'crystal'}
		<span class="textLabel">Hourly production Crystal Glass: </span>{$layout.production.resource}<br />
		{/if}
		<span class="textLabel">Storage capacity Crystal Glass: </span>{$layout.maxCapacity.crystal|number_format}
		{if $layout.tradingPost.crystal > 0}
		<br />
		<span class="textLabel">Trading Post: </span>{$layout.tradingPost.crystal|number_format}
		{/if}
	</div>
	</li>
	{assign var='sulfur' value=$layout.resources.sulfur}
	<li class="sulfur{if !$wealth} disabled{/if}"><span class="textLabel">Sulphur:</span>
	<span id="value_sulfur" class="{if $sulfur == $layout.maxCapacity.sulfur}storage_full{elseif ($sulfur/$layout.maxCapacity.sulfur) > 0.75}storage_danger{/if}">{$layout.resources.sulfur|number_format}</span>
	<div class="tooltip">
		{if $layout.resource == 'sulfur'}
		<span class="textLabel">Hourly production Sulphur: </span>{$layout.production.resource}<br />
		{/if}
		<span class="textLabel">Storage capacity Sulphur: </span>{$layout.maxCapacity.sulfur|number_format}
		{if $layout.tradingPost.sulfur > 0}
		<br />
		<span class="textLabel">Trading Post: </span>{$layout.tradingPost.sulfur|number_format}
		{/if}
	</div>

	</li>
</ul>
	</div>

<!-----------------------------------------------------
  ////////////////////// ADVISORS /////////////////////
  ----------------------------------------------------->
<div id="advisors">
<h3>Overviews</h3>
<ul>
	<li id="advCities">
		<a href="/tradeAdvisor" title="Overview of towns and finances" class="normal{if $User->newEvent()}active{/if}"><span class="textLabel">Towns</span></a> 
	</li>

	<li id="advMilitary"><a href="/militaryAdvisor" title="Military overview" class="normal">
		<span class="textLabel">Military</span>
		</a> 
	</li>
	<li id="advResearch"><a href="/researchAdvisor" title="Research overview" class="normal"> <span
		class="textLabel">Research</span> </a> 
	</li>

	<li id="advDiplomacy"><a href="/diplomacyAdvisor"
		title="Overview of messages and diplomacy"
		class="normal{if $User->newMesg() && $body_id != 'diplomacyAdvisor'}active{/if}"> <span
		class="textLabel">Diplomacy</span> </a> 
	</li>
</ul>
</div>
<!-- ADVISORS END -->


<!-- Page footer  -->
<div id="footer"><span class="copyright">Rewaz Labs</span>
<a target="_blank" href="http://ikariem.org/rules.php"
	title="Rules">Rules</a> <a target="_blank"
	href="http://agb.gameforge.de/index.php?lang=en&art=tac&special=&&f_text=000000&f_text_hover=804000&f_text_h=9ebde4&f_text_hr=DED3B9&f_text_hrbg=DED3B9&f_text_hrborder=804000&f_text_font=verdana%2C+arial%2C+helvetica%2C+sans-serif&f_bg=DED3B9"
	title="T&Cs">T&Cs</a>
<a target="_blank"
	href="http://agb.gameforge.de/index.php?lang=en&art=impressum&special=&&f_text=000000&f_text_hover=804000&f_text_h=9ebde4&f_text_hr=DED3B9&f_text_hrbg=DED3B9&f_text_hrborder=804000&f_text_font=verdana%2C+arial%2C+helvetica%2C+sans-serif&f_bg=DED3B9"
	title="Imprint">Imprint</a></div>
<!-- END page footer -->

<!-- Generic Divs for styling purposes. -->

<div id="conExtraDiv1"><span></span></div>
<div id="conExtraDiv2"><span></span></div>
<div id="conExtraDiv3"><span></span></div>
<div id="conExtraDiv4"><span></span></div>
<div id="conExtraDiv5"><span></span></div>
<div id="conExtraDiv6"><span></span></div>
<!-- END generic Divs -->

</div>
</div>

<!-- Top-toolbar with extragame options. -->
<div id="GF_toolbar">
<h3>Other game options</h3>
<ul>
	<li class="help"><a
		href="/index.php?view=informations&articleId=10000&mainId=10000"
		title="Help"><span class="textLabel">Help</span></a></li>

	<li class="highscore"><a href="/highscore?showMe=1" title="Highscore"><span class="textLabel">Highscore</span></a></li>
	<li class="options"><a href="/options" title="Settings"><span class="textLabel">Options</span></a></li>
	<li class="notes"><a href="javascript:switchNoteDisplay()" title="Notes"><span class="textLabel">Notes</span></a></li>
	<li class="forum"><a href="http://board.Ikariem.org" title="Message Board" target="_blank"><span class="textLabel">Board</span></a></li>
	<li class="logout"><a href="/index/logout" title="End game session"><span class="textLabel">Logout</span></a></li>

			<li class="version"><a href="/version" title="Version"><span
		class="textLabel">v.{$VERSION}</span></a></li>
	<li class="serverTime"><a><span class="textLabel" id="servertime">{$smarty.now|date_format:'%d.%m.%Y %H:%M:%S'}</span></a></li>
</ul>
</div>
<!-- END Top-toolbar -->

		
<!-- Even more generic Divs for styling purposes. -->
<div id="extraDiv1"><span></span></div>
<div id="extraDiv2"><span></span></div>
<div id="extraDiv3"><span></span></div>
<div id="extraDiv4"><span></span></div>
<div id="extraDiv5"><span></span></div>

<div id="extraDiv6"><span></span></div>
<!-- END even more generic Divs -->


<!-----------------------------------------------------
  /////////////// JAVASCRIPT (obviously) ////////////// 
  ----------------------------------------------------->
<script type="text/javascript">
{literal}
// Adds a "down" css-class to a supplied element.
function makeButton(ele) {
    var Event = YAHOO.util.Event;
    var Dom = YAHOO.util.Dom;
    Event.addListener(ele, "mousedown", function() {
        YAHOO.util.Dom.addClass(ele, "down");
    });
    Event.addListener(ele, "mouseup", function() {
        YAHOO.util.Dom.removeClass(ele, "down");
    });
    Event.addListener(ele, "mouseout", function() {
        YAHOO.util.Dom.removeClass(ele, "down");
    });
}
//removed "childTooltip"-code. Don't duplicate code, just nest normal tooltips!
function ToolTips() {
    var tooltips = Dom.getElementsByClassName ( "tooltip" , "div" , document , function() {
        Dom.setStyle(this, "display", "none");
    })
    for(i=0;i<tooltips.length;i++) {
        Event.addListener ( tooltips[i].parentNode , "mouseover" , function() {
            Dom.getElementsByClassName ( "tooltip" , "div" , this , function() {
                Dom.setStyle(this, "display", "block");
            });
        });
        Event.addListener ( tooltips[i].parentNode , "mouseout" , function() {
            Dom.getElementsByClassName ( "tooltip" , "div" , this , function() {
                Dom.setStyle(this, "display", "none");
            });
        });
    }
}
Event.onDOMReady( function() {
    var links = document.getElementsByTagName("a");
    for(i=0; i<links.length; i++) {
        makeButton(links[i]);
    }
    ToolTips();
    replaceSelect(Dom.get("citySelect"));
});
/* One for the wood... */
var woodCounter = getResourceCounter({{/literal}
	startdate: {$smarty.now},
	interval: 2000,
	available: {$layout.resources.wood},
	limit: [0, {$layout.maxCapacity.wood}],
	production: {$layout.production.wood/3600},
	valueElem: "value_wood"
	{literal}});
if(woodCounter) {
	woodCounter.subscribe("update", function() {
		Ikariem.currentCity.resources.wood = woodCounter.currentRes;
		});
	}
/* ...one for the tradegood... */
var tradegoodCounter = getResourceCounter({
	startdate: {/literal}{$smarty.now},
	interval: 2000,
	available: {$layout.resources.resource},
	limit: [0, {$layout.maxCapacity.resource}],
	production: {$layout.production.resource/3600},
		valueElem: "value_1"{literal}
	});
if(tradegoodCounter) {
	tradegoodCounter.subscribe("update", function() {
		Ikariem.currentCity.resources.{/literal}{$layout.resource}{literal} = tradegoodCounter.currentRes;
		});
	}

var {/literal}{$layout.resource}{literal}Counter = getResourceCounter({{/literal}
	startdate: {$smarty.now},
	interval: 2000,
	available: {$layout.resources.resource},
	limit: [0, {$layout.maxCapacity.resource}],
	production: {$layout.production.resource/3600},
	spendings: [{literal}{{/literal}amount: 0, tickInterval: 1200{literal}}{/literal}],
	valueElem: "value_{$layout.resource}"
	{literal}});
if({/literal}{$layout.resource}{literal}Counter) {
	{/literal}{$layout.resource}{literal}Counter.subscribe("update", function() {
		Ikariem.currentCity.resources.{/literal}{$layout.resource}{literal} = {/literal}{$layout.resource}{literal}Counter.currentRes;
		});
	}

var localTime = new Date();
var startServerTime = localTime.getTime() - (7200000) - localTime.getTimezoneOffset()*60*1000; // GMT+1+Sommerzeit - offset

var obj_ServerTime = 0;
Event.onDOMReady(function() {
    var ev_updateServerTime = setInterval("updateServerTime()", 500);
    obj_ServerTime = document.getElementById('servertime');
});
function updateServerTime() {{/literal}
    var currTime = new Date();
    currTime.setTime(({$smarty.now}000-startServerTime)+ currTime.getTime()) ;
    str = getFormattedDate(currTime.getTime(), 'd.m.Y G:i:s');
    obj_ServerTime.innerHTML = str;
{literal}}

function jsTitleTag(nextETA) {
    this.nextETA = nextETA;
    var thisObj = this;
    
    var cnt = new Timer(nextETA, {/literal}{$smarty.now}{literal}, 1);
    //cnt.currentdate *= 1000; <- obsolete?
    
    //top.document.title = "Ikariem";
    
    cnt.subscribe("update", function() {
        var timeargs = this.enddate - Math.floor(this.currenttime/1000) *1000;
        var title = "Ikariem - ";
        
        if (timeargs != "")
            title += getTimestring(timeargs, 3, undefined, undefined, undefined, true) + "";

        title += "";

        top.document.title = title;
    })
    
    cnt.subscribe("finished", function() {
        top.document.title = "Ikariem";
    });
    
    cnt.startTimer();
    return cnt;
}

{/literal}
{if $newETA > 0}titleTag = new jsTitleTag({$newETA});{/if}
{literal}

/*
    Notizzettel
*/

var avatarNotes = null;

function switchNoteDisplay() {
    document.cookie = 'notes=0; expires=Thu, 01-Jan-70 00:00:01 GMT;';
    var noteLayer = Dom.get("avatarNotes");
    if (noteLayer.style.display == "block") {
        avatarNotes.save();
        noteLayer.style.display = "none";
    } else {
        if (noteLayer.innerHTML == "") { // nur AjaxRequest starten, wenn Notizen noch nicht geladen sind
            ajaxRequest('/avatarNotes', updateNoteLayer);
            document.cookie = 'notes=1;';
        }
        noteLayer.style.display = "block";
   }   
}

// Notizzettel automatisch einblenden bei reload...
if (getCookie('notes') == 1) {
    switchNoteDisplay(); 
}

function updateNoteLayer(responseText) {
    var noteLayer = Dom.get("avatarNotes");
    noteLayer.innerHTML = responseText;
  
    // Create a panel Instance, from the 'resizablepanel' DIV standard module markup
            var panel = new YAHOO.widget.Panel("resizablepanel", {
                draggable: true,
                width: getCookie("Ikariem_notes_width", "470px"), 
                height: getCookie("Ikariem_notes_height", "320px"), 
                autofillheight: "body", // default value, specified here to highlight its use in the example
                constraintoviewport:true,
                context: ["tl", "bl"]
            });
            panel.render();

            // Create Resize instance, binding it to the 'resizablepanel' DIV 
            var resize = new YAHOO.util.Resize("resizablepanel", {
                handles: ["br"],
                autoRatio: false,
                minWidth: 220,
                minHeight: 110,
                status: false 
            });

            // Setup startResize handler, to constrain the resize width/height
            // if the constraintoviewport configuration property is enabled.
            resize.on("startResize", function(args) {

                if (this.cfg.getProperty("constraintoviewport")) {
                    var D = YAHOO.util.Dom;

                    var clientRegion = D.getClientRegion();
                    var elRegion = D.getRegion(this.element);

                    resize.set("maxWidth", clientRegion.right - elRegion.left - YAHOO.widget.Overlay.VIEWPORT_OFFSET);
                    resize.set("maxHeight", clientRegion.bottom - elRegion.top - YAHOO.widget.Overlay.VIEWPORT_OFFSET);
                } else {
                    resize.set("maxWidth", null);
                    resize.set("maxHeight", null);
                }

            }, panel, true);

            // Setup resize handler to update the Panel's 'height' configuration property 
            // whenever the size of the 'resizablepanel' DIV changes.

            // Setting the height configuration property will result in the 
            // body of the Panel being resized to fill the new height (based on the
            // autofillheight property introduced in 2.6.0) and the iframe shim and 
            // shadow being resized also if required (for IE6 and IE7 quirks mode).
            resize.on("resize", function(args) {
                
                var panelHeight = args.height;
                this.cfg.setProperty("height", panelHeight + "px");
                Dom.get("message").style.height = (panelHeight-75) + "px";
            }, panel, true);
            
        
            avatarNotes = new Notes();
            avatarNotes.setMaxChars(200);
            avatarNotes.init(Dom.get("message"), Dom.get("chars"));
            
            Dom.get("resizablepanel_c").style.top = getCookie("Ikariem_notes_y", "80px");
            Dom.get("resizablepanel_c").style.left = getCookie("Ikariem_notes_x", "375px");
            Dom.get("message").style.height = (parseInt(getCookie("Ikariem_notes_height", "320px")) - 75 ) + "px";            
}

window.onunload = function() { 
    if (avatarNotes instanceof Notes) {
        setCookie( 'Ikariem_notes_x', Dom.get("resizablepanel_c").style.left, '9999', '', '', '' );
        setCookie( 'Ikariem_notes_y', Dom.get("resizablepanel_c").style.top, '9999', '', '', '' );
        setCookie( 'Ikariem_notes_width', Dom.get("resizablepanel").style.width, '9999', '', '', '' );
        setCookie( 'Ikariem_notes_height', Dom.get("resizablepanel").style.height, '9999', '', '', '' );
        avatarNotes.save();
    }
}

function setCookie(name, value, expires, path, domain, secure)
{
	var today = new Date();
	today.setTime( today.getTime() );

    if ( expires ) {
        expires = expires * 1000 * 60 * 60 * 24;
    }
    var expires_date = new Date( today.getTime() + (expires) );
    document.cookie = name + "=" +escape( value ) + ((expires) ? ";expires=" + expires_date.toGMTString() : "" ) + ((path) ? ";path=" + path : "" ) + ((domain) ? ";domain=" + domain : "" ) + ((secure) ? ";secure" : "" );
}

function getCookie ( check_name, def_val ) {
    var a_all_cookies = document.cookie.split( ';' );
    var a_temp_cookie = '';
    var cookie_name = '';
    var cookie_value = '';
    var b_cookie_found = false;

    for (i=0; i<a_all_cookies.length; i++) {
        a_temp_cookie = a_all_cookies[i].split( '=' );
        cookie_name = a_temp_cookie[0].replace(/^\s+|\s+$/g, '');
        if ( cookie_name == check_name ) {
            b_cookie_found = true;
            if ( a_temp_cookie.length > 1 ) {
                cookie_value = unescape( a_temp_cookie[1].replace(/^\s+|\s+$/g, '') );
            }
            return cookie_value;
            break;
        }
        a_temp_cookie = null;
        cookie_name = '';
    }
    if (!b_cookie_found ) {
        return def_val;
    }
}
{/literal}
</script>
{if $body_id == 'barracks' || $body_id == 'shipyard'}
<script type="text/javascript"> 
{literal}
    // Wenn Seite geladen ist...
    Event.onDOMReady(function() {
    	
        // neuen Controller erzeugen
        var cCC = new constructionCostController(   {   availableResourcesAtCity: availableResourcesAtCity,
                                                        localData: localData,
                                                        unitIconsDiv:'unitCountIcons',
                                                        unitCostsDiv:'accumulatedUnitCosts',
                                                        noUnitsSelectedText:'No units have been selected',
                                                        rowLength: 14,
                                                        button_recruit : 'button_recruit',
                                                        unitCategory : '{/literal}{if $body_id == 'barracks'}222{else}111{/if}{literal}'
                                                    });
        {/literal}{foreach from=$barracks.units item=unit}{if $unit.action === true}{literal}
        cCC.registerInput(sliders['slider_{/literal}{$unit.unit_id}{literal}'],{{/literal}
            citizens:{$unit.citizen},
            wood:{$unit.cost.wood},
            {if isset($unit.cost.sulfur)}sulfur:{$unit.cost.sulfur},{/if}
            upkeep:{$unit.upkeep},
            completiontime:{$unit.timeRaw}{literal}}{/literal},'{$unit.sid}','{$unit.name}','{$unit.unit_id}')
        {/if}{/foreach}{literal}

        cCC.displayNoUnitsHTML();
        cCC.sumTotalCosts();
        cCC.updateTotalCostsHTML();
 
});
 
availableResourcesAtCity = {{/literal}'citizens':{$barracks.availableResources.citizens},
                            'wood':{$barracks.availableResources.wood},
                            'wine':{$barracks.availableResources.wine},
                            'marble':{$barracks.availableResources.marble},
                            'crystal':{$barracks.availableResources.crystal},
                            'sulfur':{$barracks.availableResources.sulfur}{literal}};
 
localData = {
                'citizens': { 'langKey': 'Citizens', 'className': 'citizens' },
                'wood': { 'langKey': 'Building material', 'className': 'wood' },
                'wine': { 'langKey': 'Wine', 'className': 'wine' },
                /*'marble': { 'langKey': 'Marble', 'className': 'marble' },*/
                'crystal': { 'langKey': 'Crystal Glass', 'className': 'crystal' },
                'sulfur': { 'langKey': 'Sulfur', 'className': 'sulfur' },
                'upkeep': { 'langKey': 'Upkeep per hour', 'className': 'upkeep' },
                'completiontime': { 'langKey': 'Building time', 'className': 'time' } };
{/literal}
</script> 
{/if}
</body>
</html>