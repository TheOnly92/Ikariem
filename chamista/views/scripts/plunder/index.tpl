<script type="text/javascript" src="/js/transportController.js"></script>

<script type="text/javascript">{literal}

function getAllChildNodesWithClassName(obj, childNodesWithClassName) {
    if (!childNodesWithClassName) {
        var childNodesWithClassName = new Array;
    }
    var i = 0;
    if (obj.childNodes) {
        for (i in obj.childNodes) {
            if (obj.childNodes[i].className) {
                childNodesWithClassName.push(obj.childNodes[i]);
            }
            if (obj.childNodes[i].firstChild) {
                childNodesWithClassName.concat(getAllChildNodesWithClassName(obj.childNodes[i], childNodesWithClassName));
            }
        }
    }
    return childNodesWithClassName;
}

function getChildNodesWithClassName(obj, className, childNodesWithClassName) {
    if (!childNodesWithClassName) {
        var childNodesWithClassName = new Array;
    }
    var i = 0;
    if (obj.childNodes) {
        for (i in obj.childNodes) {
            if (obj.childNodes[i] &&
                obj.childNodes[i].className &&
                hasClassName(obj.childNodes[i], className)) {
                childNodesWithClassName.push(obj.childNodes[i]);
            }
            if (obj.childNodes[i] && obj.childNodes[i].firstChild) {
                childNodesWithClassName.concat(getChildNodesWithClassName(obj.childNodes[i], className, childNodesWithClassName));
            }
        }
    }
    return childNodesWithClassName;
}

function getChildNodeWithClassName(obj, className) {
    if (obj.childNodes) {
        for (i in obj.childNodes) {
            if (hasClassName(obj.childNodes[i], className)) {
                return obj.childNodes[i];
            } else if (obj.childNodes[i].firstChild) {
                var node = getChildNodeWithClassName(obj.childNodes[i], className);
                if (node) {
                    return node;
                }
            }
        }
    }
    return false;
}

function getChildNodesWithTagName(obj, tagName, childNodesWithTagName) {
    if (!childNodesWithTagName) {
        var childNodesWithTagName = new Array;
    }
    var i = 0;
    if (obj.childNodes) {
        for (i in obj.childNodes) {
            if (obj.childNodes[i].tagName &&
                obj.childNodes[i].tagName == tagName.toUpperCase()) {
                childNodesWithTagName.push(obj.childNodes[i]);
            }
            if (obj.childNodes[i].firstChild) {
                childNodesWithTagName.concat(getChildNodesWithTagName(obj.childNodes[i], tagName, childNodesWithTagName));
            }
        }
    }
    return childNodesWithTagName;
}

function hasClassName(obj, needle) {
    if (obj.className && needle) {
        var haystack = obj.className;
        return haystack == needle ||
            haystack.indexOf(" " + needle + " ") >= 0 ||
            haystack.indexOf(needle + " ") == 0 ||
            haystack.indexOf(" " + needle) > 0 &&
            haystack.indexOf(" " + needle) == haystack.length - (" " + needle).length;
    } else {
        return false;
    }
}


function splitParameterStringToArray(str) {
    var arr = new Object;
    var vars = str.split(" ");
    for (var i in vars) {
        var pair = vars[i].split("=");
        if (pair[0]) {
            arr[pair[0]] = pair[1];
        }
    }
    return arr;
}


function plusMinus(obj) {
    
    var thisObj = this;
    
    // Plusminus-Box
    thisObj.plusMinusObj = obj;
    // Plus-Button
    thisObj.plusMinusObj.plusButtonObj = getChildNodeWithClassName(obj, 'plus');
    // Minus-Button
    thisObj.plusMinusObj.minusButtonObj = getChildNodeWithClassName(obj, 'minus');
    thisObj.plusMinusObj.inputObj = getChildNodeWithClassName(obj, 'value');
    
    // Parameter min iund max stehen als StyleClass im Quelltext
    var arr = splitParameterStringToArray(thisObj.plusMinusObj.inputObj.className);
    var min = (Number(arr['min']))?parseInt(arr['min']):0;
    var max = (Number(arr['max']) || arr['max']==0)?parseInt(arr['max']):999;
    
    thisObj.action = '';
    //alert(thisObj.plusMinusObj.innerHTML);
    
    this.setMax = function(val) {
    	max = val;
    }
    
    this.setValue = function(val, avoidFireAction) {
        thisObj.plusMinusObj.inputObj.value = val;
        if (!avoidFireAction) {
            thisObj.setValueAction(val);
        }
    }
    this.setValueAction = function(val) {// Dummy, bitte ableiten
    }
    this.getValue = function () {
        return (thisObj.plusMinusObj.inputObj.value);
    }
    
    this.minus = function() {
        //alert(thisObj.plusMinusObj.inputObj.value);
        if (thisObj.plusMinusObj.inputObj.value>min) {
            thisObj.setValue(parseInt(thisObj.getValue())-1);
        } else {
            thisObj.setValue(min);
        }
        thisObj.minusAction(thisObj.plusMinusObj.inputObj.value);    
    }
    this.minusAction = function(value) { // Dummy, bitte ableiten
    }
    this.plus = function() {
        if (thisObj.plusMinusObj.inputObj.value<max) {
            thisObj.setValue(parseInt(thisObj.getValue())+1);
        } else {
            thisObj.setValue(max);
        }    
        thisObj.plusAction(thisObj.plusMinusObj.inputObj.value);
    }
    
    this.plusAction = function(value) { // Dummy, bitte ableiten
    }
    
    this.testValue = function() {
        if (thisObj.plusMinusObj.inputObj.value<min) {
            thisObj.setValue(min);
            return false;
        } else if (thisObj.plusMinusObj.inputObj.value>max) {
            thisObj.setValue(max);
            return false;
        }
        return true;
    }
    this.testValueAction = function() { // Dummy, bitte ableiten
    }
    
    this.minusInterval = function() {
        if (thisObj.action == 'minus') {
            thisObj.minus();
            setTimeout(thisObj.minusInterval, 200);
        }
    }
    this.plusInterval = function() {
        if (thisObj.action == 'plus') {
            thisObj.plus();
            setTimeout(thisObj.plusInterval, 200);
        }
    }
    addListener(thisObj.plusMinusObj.minusButtonObj, 'mousedown', function() {
        thisObj.action = 'minus';
        thisObj.minusInterval();  
    });
    addListener(thisObj.plusMinusObj.plusButtonObj, 'mousedown', function() {
        thisObj.action = 'plus';
        thisObj.plusInterval();  
    });
    addListener(thisObj.plusMinusObj.minusButtonObj, 'mouseup', function() {
        thisObj.action = '';
    });
    addListener(thisObj.plusMinusObj.plusButtonObj, 'mouseup', function() {
        thisObj.action = '';
    });
    addListener(thisObj.plusMinusObj.minusButtonObj, 'mouseout', function() {
        thisObj.action = '';
    });
    addListener(thisObj.plusMinusObj.plusButtonObj, 'mouseout', function() {
        thisObj.action = '';
    });
    addListener(thisObj.plusMinusObj.plusButtonObj, 'huhu', function() {
        alert('hier fehlen jetzt die Tests und Nachziehen von Warenkorb');
    });
    addListener(thisObj.plusMinusObj.inputObj, 'change', function() {
        thisObj.testValue();
    });
    addListener(thisObj.plusMinusObj.inputObj, 'keyup', function() {
        thisObj.testValue();
        thisObj.setValueAction(thisObj.getValue());    
    });
}
{/literal}</script>

<script type="text/javascript">
{literal}
	var transporterDisplay;
	var tempUnitTime = 0;
	
	var textOk = "Pillage town!";
    var textNoTransporters = "No trade ships selected. Your plundering troops will not take home any loot.";
	var textNoTroops = "You haven`t selected any units"; 
	
    var jsClassOk = 'ok';
    var jsClassNoTransporters = 'warning';
    var jsClassNoTroops = 'warning';
    	
	Event.onDOMReady(function() {
		transporterDisplay = new armyTransportController(
			{/literal}{$plunder.transporters}{literal},
			500,
			Dom.get("transporterCount"),
			Dom.get("extraTransporter"),
			Dom.get("sumTransporter"),
			Dom.get("totalFreight"),
			600,
			Dom.get('journeyTime'),
			Dom.get('returnTime'),
			Dom.get('upkeepPerHour'),
			Dom.get('estimatedTotalCosts'),
			Dom.get("totalWeight"),
			null,
			Dom.get('plunderbutton')
			);
		});
{/literal}
</script>

<!---------------------------------------------------------------------------------------
	 ////////////////////////////////////////////////////////////////////////////////////
	 ///////////////////////////// dynamic side-boxes. //////////////////////////////////
	 //////////////////////////////////////////////////////////////////////////////////// -->
<div id="backTo" class="dynamic">
	<h3 class="header">Back</h3>
	<div class="content">
	<a href="/island?id={$plunder.islandId}" title="Back to the island">
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
		<h1>Pillage</h1>
		<p>Other towns often have treasures that are better kept in our warehouses. But remember that units cost more upkeep if they are on a mission. So think twice before you send your units out for pillaging.</p>
	</div>
	<div id="notices">
	</div>
	<form  action="/transport/{if $plunder.barbarianVillage == 1}attackBarbarianVillage{/if}?id={$plunder.townId}&destinationCityId={$plunder.destinationCityId}" id="plunderForm"  method="POST">
	<div id="selectArmy" class="contentBox01h">
		<h3 class="header">Send army</h3>
		<div class="content">
			<p>You send your troops from {$plunder.townName} in order to pillage <strong>{if $plunder.barbarianVillage == 1}Barbarian Village{/if}</strong>.</p>
			<ul class="assignUnits">
				{foreach from=$plunder.units item=unit name=units}
				<li class="{$unit.unit_id}{if $smarty.foreach.units.index % 2 == 0} alt{/if}">
					<label for="cargo_army_{$unit.id}">{$unit.unit_name} send along:</label>
					<div class="amount"><span class="textLabel">Amount available: </span>{$unit.maxValue}</div>
					<div class="weight"><span class="textLabel">Cargo space </span>{$unit.weight}</div>
					<div class="sliderinput">
						<div class="sliderbg" id="sliderbg_{$unit.id}">
							<div class="actualValue" id="actualValue_{$unit.id}"></div>
							<div class="sliderthumb" id="sliderthumb_{$unit.id}"></div>
						</div>
						<script type="text/javascript">{literal}
						create_slider({{/literal}
							dir : 'ltr',
							id: "slider_{$unit.id}",
							maxValue: {$unit.maxValue},
							overcharge: 0,
							iniValue: 0,
							bg: "sliderbg_{$unit.id}",
							thumb: "sliderthumb_{$unit.id}",
							topConstraint: -10,
							bottomConstraint: 326,
							bg_value: "actualValue_{$unit.id}",
							textfield: "cargo_army_{$unit.id}"
						{literal}});
						Event.onDOMReady(function() {{/literal}
							var s=sliders["slider_{$unit.id}"];
							s.upkeep=1;
							s.weight=0;
							s.unitJourneyTime=600;
							//upkeepDisplay.registerSlider(s);
							transporterDisplay.registerSlider(s);
						{literal}});
						{/literal}</script>
						<a class="setMin" href="#reset" onClick="sliders['slider_{$unit.id}'].setActualValue(0); return false;" title="Reset entry"><span class="textLabel">min</span></a>
						<a class="setMax" href="#max" onClick="sliders['slider_{$unit.id}'].setActualValue(sliders['slider_{$unit.id}'].maxValue); return false;" title="Send everything"><span class="textLabel">max</span></a>
					</div>
					<input class="textfield" id="cargo_army_{$unit.id}" type="text" name="cargo_army_{$unit.id}"  value="0" size="4" maxlength="9" />
					<input type="hidden" id="cargo_army_{$unit.id}_upkeep" name="cargo_army_{$unit.id}_upkeep" value="{$unit.upkeep}" />
				</li>
				{/foreach}
			</ul>
			<hr />
			<div id="missionSummary">
				<div class="plunderInfo">
					<div class="targetName"><span class="textLabel">Target: </span>{if $plunder.barbarianVillage == 1}Barbarian Village{/if}</div>
					<div class="upkeep"><span class="textLabel">Additional upkeep: </span><span id="upkeepPerHour">0</span> per Hour</div>		
				</div>
				<div class="newSummary">
					<div class="transporter">
						<span class="textLabel">Transporter: </span>
						<div class="neededTransporter"><span id="transporterCount">0</span></div>
						<div id="plusminus" class="plusminus">
							<input class="value text min=0 max={$plunder.transporters}" type="text" size="3" maxlength="4" value="0" name="transporter" id="extraTransporter"/>
							<a href="javascript:;" class="plus"></a>
							<a href="javascript:;" class="minus"></a>
						</div>
						<div class="sumTransporter" id="sumTransporter">0</div> 
					</div>
					<div class="freight">
						<div id="totalWeight">0</div>
						<div id="totalFreight">0</div>
					</div>
					<div class="travelTime">
						<div id="journeyTime">-</div>
						<div id="returnTime">-</div>
					</div>
					<script type="text/javascript">{literal}
					var temp = new plusMinus(document.getElementById('plusminus'), 10, 40);
					var transporterCountElem = Dom.get('transporterCount');
					var totalFreightElem = Dom.get('totalFreight');
					
					temp.setValueAction = function(val) {
						val = Number(val);
						if(isNaN(val)) {
							val = 0;
							temp.setValue(0);
							return;
						}
						tempMath = (val+ parseInt(transporterCountElem.innerHTML));
						Dom.get('sumTransporter').innerHTML = tempMath;
						//alert(tempUnitTime);
						if(tempMath > 0 && tempUnitTime < 600) {
							Dom.get('journeyTime').innerHTML = getTimestring(600*1000, 3);
							Dom.get('returnTime').innerHTML = getTimestring(1200*1000, 3);
						} else {
							if(tempUnitTime > 0) {
								Dom.get('journeyTime').innerHTML = getTimestring(tempUnitTime*1000, 3);
								Dom.get('returnTime').innerHTML = getTimestring(tempUnitTime*2000, 3);
							} else {
								Dom.get('journeyTime').innerHTML = "-";
								Dom.get('returnTime').innerHTML = "-";
							}
						}
						temp.setMax({/literal}{$plunder.transporters}{literal}-parseInt(transporterCountElem.innerHTML));
						totalFreightElem.innerHTML = (val+ parseInt(transporterCountElem.innerHTML))*500;  
						if(tempUnitTime > 0 && tempMath > 0) {
							Dom.get('plunderbutton').className = "ok";
							Dom.get('plunderbutton').title = textOk;
						} else if (tempUnitTime > 0 && tempMath <= 0) {
							Dom.get('plunderbutton').className = "warning";
							Dom.get('plunderbutton').title = textNoTransporters;
						} else {
							Dom.get('plunderbutton').className = "warning";
							Dom.get('plunderbutton').title = textNoTroops;
						}
					}
					{/literal}</script>
					<div class="submit">
					<a href="#" id="plunderbutton" class="warning" onclick="Dom.get('plunderForm').submit();" title="You haven`t selected any units" value=""></a>
					</div>
				</div>
			</div>
		</div><!--end .content -->
		<div class="footer"></div>
	</div><!-- end .contentBox01 -->
	</form>
</div><!-- mainview -->