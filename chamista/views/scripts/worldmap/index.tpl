<script type='text/javascript'>
{literal}
if(!window.console) {
    var console = function(){};
    console.log = function(){};
}

MAXSIZE = 9;
halfMaxSize = Math.floor(MAXSIZE/2);
{/literal}
start_x={$start_x};
start_y={$start_y};

center_x={$start_x};
center_y={$start_y};

center_x_begin={$start_x};
center_y_begin={$start_y};
{literal}
wonder_status=1;
tradegood_status=1;
city_status=1;

IE = (navigator.appName!='Microsoft Internet Explorer')?0:1;

tradegoodText = new Array();
tradegoodText[1] = 'Wine';
tradegoodText[2] = 'Marble';
tradegoodText[3] = 'Crystal Glass';
tradegoodText[4] = 'Sulfur';

wonderText = new Array();
wonderText[1] = 'Hephaistos` Forge';
wonderText[2] = 'Hades` Holy Grove';
wonderText[3] = 'Demeter`s gardens';
wonderText[4] = 'Temple of Athene';
wonderText[5] = 'Temple of Hermes';
wonderText[6] = 'Ares` Stronghold';
wonderText[7] = 'Temple of Poseidon';
wonderText[8] = 'Colossus';


var mapText = new Array();
mapText['markedislandlink'] = 'Center map to this island';

markCoordX = -1;
markCoordY = -1;


occupiedIslandJS = new Array();
allyIslandJS = new Array();
militaryIslandsJS = new Array();

ownIslandJS = new Array();
if (!ownIslandJS[{/literal}{$start_x}{literal}]) {
    ownIslandJS[{/literal}{$start_x}{literal}] = new Array();
}
if (ownIslandJS[{/literal}{$start_x}{literal}]) {
    ownIslandJS[{/literal}{$start_x}{literal}][{/literal}{$start_y}{literal}] = 1;
}

var shortcuts = new Array();


	if(shortcuts[{/literal}{$start_x}{literal}] == undefined) shortcuts[{/literal}{$start_x}{literal}] = new Array();
	shortcuts[{/literal}{$start_x}{literal}][{/literal}{$start_y}{literal}] = 2;


function Map(x, y) {

    var thisObj = this;
    
    thisObj.scrollDiv = new Array();
    thisObj.scrollDiv[0] = document.getElementById('map1'); 
    thisObj.scrollDiv[1] = document.getElementById('linkMap'); 
    
    thisObj.maxX = 9;
    thisObj.maxY = 9;

    thisObj.currMapX = x;
    thisObj.currMapY = y;
    
    thisObj.waitingForData = false;
    thisObj.waitingForIslandData = false;
    
    thisObj.startDragY = 0;
    thisObj.startDragX = 0;
    thisObj.startPosX = 0;
    thisObj.startPosY = 0;
    thisObj.posX = 0;
    thisObj.posY = 0;
    
    thisObj.dx = 0;
    thisObj.dy = 0;
    
    thisObj.lastDiffX = 0;
    thisObj.lastDiffY = 0;
    
    thisObj.tilesToLoad = new Array();
    
    thisObj.tile = new Array(); // fuer html-obj der einzelteile
    
    thisObj.action = '';
    
    thisObj.dragHandleObj = document.getElementById('dragHandlerOverlay');
    
    // datenspeicher fuer alle geladenen map-daten
    thisObj.islands = new Array();
    
    this.initCoords = function() {
        
//        console.log('initCoords');
    
        thisObj.startDragY = 0;
        thisObj.startDragX = 0;
        thisObj.startPosX = 0;
        thisObj.startPosY = 0;
        thisObj.posX = 0;
        thisObj.posY = 0;
    

        thisObj.lastDiffX = 0;
        thisObj.lastDiffY = 0;
        thisObj.dx = 0;
        thisObj.dy = 0;

        thisObj.tilesToLoad = new Array();
        thisObj.tile = new Array();
        
        thisObj.action = '';


        thisObj.waitingForData = false;
        thisObj.waitingForIslandData = false;
        
        thisObj.scrollDiv[0].style.top = '0px';
        thisObj.scrollDiv[0].style.left = '0px';

    }    
    
    this.moveBy = function(deltaMapX, deltaMapY) {
        
        if (thisObj.action == '') {
            if (deltaMapX && deltaMapY) {
                deltaMapX /= 2;
                deltaMapY /= 2;
            }
            if (deltaMapX) {
                thisObj.startMovePosX = thisObj.posX;
                thisObj.targetMovePosX = thisObj.posX - 240*deltaMapX;
            } else {
                thisObj.startMovePosX = thisObj.posX;
                thisObj.targetMovePosX = thisObj.posX;
            }
            if (deltaMapY) {
                thisObj.startMovePosY = thisObj.posY;
                thisObj.targetMovePosY = thisObj.posY - 120*deltaMapY;
            } else {
                thisObj.startMovePosY = thisObj.posY;
                thisObj.targetMovePosY = thisObj.posY;
            }
            

            //thisObj.action = 'dragHandle';
            //thisObj.dragStop();
            thisObj.action = 'move';
            
            
//            console.log('deltaMapX:deltaMapY:'+ deltaMapX +':'+ deltaMapY);
            
            thisObj.moveInterval();
        }
    }
    
    
    this.moveInterval = function() {
            
            //console.log(thisObj.posX +':'+ thisObj.posY);
            
            if (!thisObj.scrollStartTime) {
                thisObj.scrollStartTime = new Date().getTime();
                var deltaTime = 0;
                
            } else {
                var currTime = new Date().getTime();
                var deltaTime =  currTime - thisObj.scrollStartTime;
            }
            
            thisObj.posX = thisObj.startMovePosX + (thisObj.targetMovePosX-thisObj.startMovePosX)*(deltaTime/500);
            thisObj.posY = thisObj.startMovePosY + (thisObj.targetMovePosY-thisObj.startMovePosY)*(deltaTime/500);
            
            
            
            thisObj.setPosition();

            if (deltaTime < 500) {
                setTimeout(thisObj.moveInterval, 50);
            } else {
                thisObj.scrollStartTime = 0;
                thisObj.action = '';
                
                thisObj.posX = thisObj.startMovePosX + (thisObj.targetMovePosX-thisObj.startMovePosX);
                thisObj.posY = thisObj.startMovePosY + (thisObj.targetMovePosY-thisObj.startMovePosY);
                thisObj.setPosition();
                
                               
                
                // randteile anfuegen
                var dx = Math.round((thisObj.targetMovePosX-thisObj.startMovePosX)/2-(thisObj.targetMovePosY-thisObj.startMovePosY))/120;
                var dy = Math.round((thisObj.targetMovePosY-thisObj.startMovePosY) + (thisObj.targetMovePosX-thisObj.startMovePosX)/2)/120;
                thisObj.drawBorder(0, dx);
                //console.log(dx, dy);
                if (Math.abs(dx)>1) {
                    thisObj.drawBorder(0, dx);
                }
                thisObj.drawBorder(dy, 0);
                if (Math.abs(dy)>1) {
                    thisObj.drawBorder(dy, 0);
                }
                //thisObj.lastDiffY-=dx*120;
                //thisObj.lastDiffX-=dy*120;
          
                
            } 
    }
    
    this.drawMap = function() {
        
        for (var x = 0; x <= thisObj.maxX; x++) {
            thisObj.tile[x] = new Array();
            for (var y = 0; y <= thisObj.maxY; y++) {
                obj = document.getElementById('tile_'+ x +'_'+ y); 
                thisObj.tile[x][y] = obj;
                obj.style.left = (x*120 - y*120) +'px';
                obj.style.top  = (x*60  + y*60)  +'px';
                thisObj.drawPiece(obj,  thisObj.currMapX + x - Math.floor(thisObj.maxX/2), thisObj.currMapY + y - Math.floor(thisObj.maxY/2));
            }
        }
    }
    
    this.drawPiece = function(obj, x, y) {
 
        mapX = x;
        mapY = y;

        wonder = obj.firstChild;
        tradegood = wonder.nextSibling;
        cities = tradegood.nextSibling;
        marking = cities.nextSibling;
        own  = marking.nextSibling;
        coords = marking.id.split('_');
        
        var linkId = 'link_'+obj.id;
        objLink = document.getElementById(linkId);
        obj.style.zIndex  = 100+mapX+mapY;
        obj.mapX = mapX;
        obj.mapY = mapY; 

        if (thisObj.islands && thisObj.islands[mapX] && thisObj.islands[mapX][mapY]) {
        
            if (thisObj.islands[mapX][mapY]!='ocean') {
            
                var isl = thisObj.islands[mapX][mapY];
                obj.className = 'island'+ isl[5]; // island_type_id
                obj.title = isl[1] +' ['+mapX+':'+mapY+']';//name
                obj.alt =isl[1];
                
                
                wonder.className='wonder wonder'+isl[3];
                
                tradegood.className='tradegood tradegood'+isl[2];
                //cities.innerHTML = isl[1] +' ['+ mapX +':'+ mapY +']';
                cities.innerHTML=isl[7];
                cities.className = 'cities';
                
                if (thisObj.currMapX == mapX && thisObj.currMapY == mapY) {
                    //marking.className = 'islandMarked'
                    thisObj.markIsland(obj.id);
                } else {
                    marking.className = '';
                }
                
                
                if (militaryIslandsJS[mapX] && militaryIslandsJS[mapX][mapY]) {
                    obj.className = 'island'+ isl[5]  + 'treaty';
                } else {
                    own.className = '';
                }
                
                
                if (occupiedIslandJS[mapX] && occupiedIslandJS[mapX][mapY]) {
                    obj.className = 'island'+ isl[5]  + 'own';
                } else {
                    //own.className = '';
                }
                
                
                if (allyIslandJS[mapX] && allyIslandJS[mapX][mapY]) {
                    obj.className = 'island'+ isl[5]  + 'ally';
                } else {
                   // own.className = '';
                }
                
                
                
                if (ownIslandJS[mapX] && ownIslandJS[mapX][mapY]) {
                     obj.className = 'island'+ isl[5]  + 'own';
                } else {
                    //own.className = '';
                }
                
                
                
                
                //console.log(linkId);
                objLink.innerHTML = '<a href="#'+ mapX +':'+ mapY +'" onclick="map.clickIsland(\''+ obj.id +'\');return false;" class="islandLink"></a>';
                objLink.style.left = obj.style.left;
                objLink.style.top  = obj.style.top;
                ;
                
            } else  {
    
                obj.className = thisObj.getOceanClass(mapX, mapY);
                obj.title = '';
                wonder.className='';
                tradegood.className='';
                cities.className = '';
                cities.innerHTML='';
                //cities.innerHTML=''+ mapX +':'+ mapY +':'+ thisObj.islands[mapX][mapY];
                objLink.innerHTML = '';
                own.className = '';
                marking.className = '';
            }
            obj.toLoad = false;;
        } else {
            obj.className = thisObj.getOceanClass(mapX, mapY);
            obj.title = '';
            wonder.className='';
            tradegood.className='';
            cities.className = '';
            objLink.innerHTML = '';
            own.className = '';
            marking.className = '';
            
            if (mapX < (100 + thisObj.maxX) && mapX>0 && mapY < (100 + thisObj.maxY) && mapY> 0) {
                obj.className = 'loading';
                cities.innerHTML = '';
                //cities.innerHTML = 'loading'+ mapX +':'+ mapY;
                obj.toLoad = true;
                thisObj.tilesToLoad[mapX+'_'+mapY] = obj;
                thisObj.getMapData(mapX, mapY);
            } else { 
                cities.innerHTML = ''; 
                obj.toLoad = false;
            }
        }
    }
    
    // bei Bedarf: teile an Rand in Scrollrichtung zufuegen
    this.drawBorder = function(deltaX, deltaY) {
        
       if (deltaX>0) {
            thisObj.lastDiffX += 120;
            thisObj.dx--;
//            console.log('b');
            for (y=0; y<=thisObj.maxY; y++) {
                dy =  thisObj.dy + y;
                //console.log('x:y' + thisObj.dx +':'+ dy);
                obj = thisObj.tile[thisObj.maxX][y];
                obj.style.left = (thisObj.dx*120 - dy*120) +'px';
                obj.style.top  = (thisObj.dx*60  + dy*60)  +'px';
                thisObj.drawPiece(obj, thisObj.currMapX + thisObj.dx - Math.floor(thisObj.maxX/2), thisObj.currMapY + dy - Math.floor(thisObj.maxY/2));
                
                var temp = thisObj.tile[thisObj.maxX][y];
                for (x=thisObj.maxX-1; x>=0; x--) {
                    thisObj.tile[x+1][y] = thisObj.tile[x][y]; 
                    //console.log('tile['+ (x+1) +']['+  y +'] = tile['+ (x) +']['+ y +']' );
                }
                thisObj.tile[0][y] = temp;
                //dxx++;
            }
        } else if(deltaX<0) {
            thisObj.lastDiffX -= 120;
            thisObj.dx++;
//            console.log('a');
            //dx = -Math.floor((diffX/2+diffY )/120);
            for (y=0; y<=thisObj.maxY; y++) {
                dy =  thisObj.dy + y;
                obj =thisObj.tile[0][y];
                obj.style.left = ((thisObj.dx+thisObj.maxX-1)*120 - dy*120) +'px';
                obj.style.top  = ((thisObj.dx+thisObj.maxX-1)*60  + dy*60)  +'px';
                thisObj.drawPiece(obj, thisObj.currMapX + thisObj.dx+thisObj.maxX-1 - Math.floor(thisObj.maxX/2), thisObj.currMapY + dy - Math.floor(thisObj.maxY/2));

                var temp = thisObj.tile[0][y];
                for (x=0; x<thisObj.maxX; x++) {
                    thisObj.tile[x][y] = thisObj.tile[x+1][y]; 
                    //console.log('tile['+ (x) +']['+  y +'] = tile['+ (x+1) +']['+ y +']' );
                }
                thisObj.tile[thisObj.maxX][y] = temp;
            }
        } else
        
        if (deltaY > 0) {
            thisObj.lastDiffY += 120;
            thisObj.dy++;
//            console.log('c');
            for (x=0; x<=thisObj.maxX; x++) {
                dx = thisObj.dx + x;
                obj = thisObj.tile[x][0];
                //console.log('tile_'+ x +'_'+ ((Math.abs(dy+800)%9)));
                obj.style.left = ((dx)*120 - (thisObj.dy+thisObj.maxY)*120) +'px';
                obj.style.top  = ((dx)*60  + (thisObj.dy+thisObj.maxY)*60)  +'px';
                thisObj.drawPiece(obj, thisObj.currMapX + dx - Math.floor(thisObj.maxX/2), thisObj.currMapY + thisObj.dy+thisObj.maxY - Math.floor(thisObj.maxY/2));

                var temp = thisObj.tile[x][0];
                for (y=0; y<thisObj.maxY; y++) {
                    thisObj.tile[x][y] = thisObj.tile[x][y+1]; 
                    //console.log('tile['+ (x) +']['+  (y+1) +'] = tile['+ (x) +']['+ y +']' );
                }
                thisObj.tile[x][thisObj.maxY] = temp;
                
            }
        } else if(deltaY < 0) {
            thisObj.lastDiffY -= 120;
            thisObj.dy--;
//            console.log('d');
            //dy = Math.floor((diffX/2-diffY)/120);
            for (x=0; x<=thisObj.maxX; x++) {
                dx = thisObj.dx + x;
                obj = thisObj.tile[x][thisObj.maxY];;
                //console.log('tile_'+ x +'_'+ ((Math.abs(dy+800)%9)));
                obj.style.left = ((dx)*120 - (thisObj.dy)*120) +'px';
                obj.style.top  = ((dx)*60  + (thisObj.dy)*60)  +'px';
                thisObj.drawPiece(obj, thisObj.currMapX + dx - Math.floor(thisObj.maxX/2), thisObj.currMapY + thisObj.dy - Math.floor(thisObj.maxY/2));

                var temp = thisObj.tile[x][thisObj.maxY];
                for (y=thisObj.maxY-1; y>=0; y--) {
                    thisObj.tile[x][y+1] = thisObj.tile[x][y]; 
                    //console.log('tile['+ (x+1) +']['+  y +'] = tile['+ (x) +']['+ y +']' );
                }
                thisObj.tile[x][0] = temp;
            }
        
        }
        
    }
    
    this.drawBorderPlusX = function() {
        thisObj.drawBorder(1, 0);
    }
    
    this.getMapData = function(x, y) {
        
        if (!thisObj.waitingForData) {
            thisObj.waitingForData = true;
            jsonUrl  = '/worldmap/getJSONArea?x_min='+ (x - thisObj.maxX -4);
            jsonUrl += '&x_max='+ (x + thisObj.maxX +4);
            jsonUrl += '&y_min='+ (y - thisObj.maxY -4);
            jsonUrl += '&y_max='+ (y + thisObj.maxY +4);
            ajaxRequest(jsonUrl, thisObj.handleMapData);    
        }
    }
    this.handleMapData = function(JSONResponse) {
        
//        console.log('handleMapData');
        
        if (!JSONResponse) return false;

        var responseData = JSON.parse(JSONResponse);
        var mapData = responseData['data'];
        var requestData = responseData['request'];
        
        for (x = requestData['x_min']; x<=requestData['x_max']; x++) {
            for (y = requestData['y_min']; y<=requestData['y_max']; y++) {
                if (!thisObj.islands[x]) {
                    thisObj.islands[x] = new Array();
                }
                if (mapData[x] && mapData[x][y]) {
                    thisObj.islands[x][y] = mapData[x][y];
                } else {
                    thisObj.islands[x][y] = 'ocean';
                }         
            }
        }
        for (var coords in thisObj.tilesToLoad) {
//            console.log('handleMapData'+coords+'#'+ thisObj.tilesToLoad[coords].mapX+':'+ thisObj.tilesToLoad[coords].mapY);
            var testCoords = thisObj.tilesToLoad[coords].mapX +'_'+ thisObj.tilesToLoad[coords].mapY;
            if (thisObj.tilesToLoad[coords].toLoad == true) {
                if (testCoords==coords) {
                    var xy = coords.split('_');
//                    console.log('coord');
                    thisObj.drawPiece(thisObj.tilesToLoad[coords], xy[0], xy[1]);
                } else {
//                    console.log('mapXY');
                    thisObj.drawPiece(thisObj.tilesToLoad[coords],  thisObj.tilesToLoad[coords].mapX,  thisObj.tilesToLoad[coords].mapY);
                }
            }
        }
        thisObj.tilesToLoad = new Array();
        
        thisObj.waitingForData = false;
        //thisObj.drawMap(); 
    }


    this.dragHandle = function(event) { 
        
        // init
        addListener(document, 'onclick', thisObj.dragStop)
        thisObj.dragHandleObj.style.cursor = 'crosshair';

        thisObj.startDragHandlePosX = document.all ? window.event.clientX : event.pageX;
        thisObj.startDragHandlePosY = document.all ? window.event.clientY : event.pageY;

        if (typeof(event)!="undefined"){
            if (event.preventDefault) {
               event.preventDefault();
            }
            event.returnValue = false;
        }


        // drag&drop
        document.onmousemove = function(ev) {
            
            // drag&drop init
            if (thisObj.action == '') {
            
                thisObj.startPosX = thisObj.startDragHandlePosX;
                thisObj.startPosY = thisObj.startDragHandlePosY;

                if (typeof(event)!="undefined"){
                    if (event.preventDefault) {
                       event.preventDefault();
                    }
                    event.returnValue = false;
                }
                
                thisObj.startDragY = (parseInt(thisObj.scrollDiv[0].style.top))?parseInt(thisObj.scrollDiv[0].style.top):0;
                thisObj.startDragX = (parseInt(thisObj.scrollDiv[0].style.left))?parseInt(thisObj.scrollDiv[0].style.left):0;
                
                //console.log('thisObj.startDragXY:' + thisObj.startDragX +':'+thisObj.startDragY);
                
                thisObj.startMapX = thisObj.currMapX;
                thisObj.startMapY = thisObj.currMapY;
            
                thisObj.action = 'dragHandle';
            }

            
            
            // verschiebung auslesen
            thisObj.posX = document.all ? window.event.clientX : ev.pageX;
            thisObj.posY = document.all ? window.event.clientY : ev.pageY;
            if (typeof(event)!="undefined"){
                if (event.preventDefault) {
                   event.preventDefault();
                }
                event.returnValue = false;
            }
            
            // verschieben
            thisObj.setPosition();
             
            /* */
            // neue Inseln an Rand anfuegen  
            var diffX = (thisObj.posX  - thisObj.startPosX + thisObj.startDragX); // verschiebung seit dem Draw
            var diffY = (thisObj.posY  - thisObj.startPosY + thisObj.startDragY); // verschiebung seit dem Draw
            //console.log('diffX:diffY:' + diffX +':'+ diffY);
            //dx = -Math.round( (diffX/2 + diffY)/120 );
            //dy =  Math.round( (diffX/2 - diffY)/120 );
            
            dx = thisObj.dx;
            dy = thisObj.dy;
                        
            dyy = dy;
            dxx = dx;
            
            //console.log( thisObj.currMapX - thisObj.lastDiffX/240 - thisObj.lastDiffY/120);


      
            if ( (diffX/2 + diffY) > thisObj.lastDiffX+120 ) {
                thisObj.drawBorder(1, 0);
            } 
            if ( (diffX/2 + diffY ) < thisObj.lastDiffX-120 ) {
                thisObj.drawBorder(-1, 0);
            }  
            if ( (diffX/2 - diffY) > thisObj.lastDiffY+120 ) {
                thisObj.drawBorder(0, 1);
            } 
            if ( (diffX/2 - diffY) < thisObj.lastDiffY-120 ) {
                thisObj.drawBorder(0, -1);
            }    


            if (typeof(obj) != 'undefined') {
                //console.log(obj.style.left);
            }
            
         };    
    }, 
    
    this.setPosition = function() {
            
            //console.log('### thisObj.posX, thisObj.startPosX, thisObj.startDragX', thisObj.posX, thisObj.startPosX, thisObj.startDragX,
            //    thisObj.posY, thisObj.startPosY, thisObj.startDragY)
             
            var dx = (thisObj.posX  - (thisObj.startPosX-thisObj.startDragX));
            var dy = (thisObj.posY - (thisObj.startPosY-thisObj.startDragY));
            
            document.navInputForm.xcoord.value = thisObj.currMapX - Math.round(dy/120+dx/240);
            document.navInputForm.ycoord.value = thisObj.currMapY - Math.round(dy/120-dx/240);
            
            //console.log(dy, '###', dy);
            
            thisObj.scrollDiv[0].style.left = dx + 'px';
            thisObj.scrollDiv[0].style.top =  dy + 'px';
            thisObj.scrollDiv[1].style.left = dx + 'px';
            thisObj.scrollDiv[1].style.top =  dy + 'px';
    }

    this.dragStop = function(ev) {
        document.onmousemove = '';
        
        if (thisObj.action == 'dragHandle') {
            setTimeout(function() {thisObj.action = ''}, 100);
        }
        thisObj.dragHandleObj.style.cursor = 'move';
        
    },
    
    // springen auf Karte ueber Eingabefeld
    this.jumpToCoord = function() {
        var x = document.navInputForm.xcoord.value;
        var y = document.navInputForm.ycoord.value;
        
        thisObj.jumpToXY(x, y);
    }
    
    this.jumpToShortcut = function() {
    	var text = document.navShortcutForm.homeCitySelect.value;  
    	var trennzeichenPos = text.indexOf(':');
    	var x = parseInt(text.substring(0, trennzeichenPos), 10);
    	var y = parseInt(text.substring(trennzeichenPos+1, text.length), 10);
    	
    	thisObj.jumpToXY(x, y);
    }
    
    this.jumpToXY = function(x, y) {
        thisObj.currMapX = parseInt(x);
        thisObj.currMapY = parseInt(y);

        thisObj.initCoords();
        thisObj.drawMap();
        thisObj.setPosition();
        //thisObj.markIsland(x, y);
    }
    
    this.getOceanClass = function(x, y) {
        var klasse = 'ocean1';
        if( (Math.abs((x+y*3)%4)) ==0) klasse = 'ocean2';
        if( (Math.abs((x+y*4)%5)) ==0) klasse = 'ocean3';
        if( (Math.abs((x+y*5)%12))==0) klasse = 'ocean_feature1';
        if( (Math.abs((x+y*6)%13))==0) klasse = 'ocean_feature2';
        if( (Math.abs((x+y*7)%12))==0) klasse = 'ocean_feature3';
        if( (Math.abs((x+y*8)%13))==0) klasse = 'ocean_feature4';
        return klasse;
    }
    
    this.getXYFromEvent = function(event) {
        posX = document.all ? window.event.clientX : event.pageX;
        posY = document.all ? window.event.clientY : event.pageY;
        return [posX, posY];
    }
    
    this.centerIsland = function(x, y) {
        thisObj.jumpToXY(x, y); 
    }
    
    this.markIsland = function(objId) {
        obj = document.getElementById(objId);
        var x = obj.mapX;
        var y = obj.mapY;
        
        var linkId = 'link_'+objId;
        objLink = document.getElementById(linkId);
        objLink.className = '';         


        //var id = 'magnify_'+ x +'_'+ y ;
        var id = objId.replace('tile', 'magnify');
        if (thisObj.magnifiedIslandObj) {
            if (thisObj.magnifiedIslandObj.id == id) { // klick 2 auf Objekt
            } else { // klick auf anderes Objekt
                thisObj.magnifiedIslandObj.className = '';
                thisObj.markedIslandObj.className = '';

                var oldLinkId = 'link_'+ thisObj.magnifiedIslandObj.id.replace('magnify', 'tile');
//                console.log(oldLinkId);
                objOldLink = document.getElementById(oldLinkId);
                objOldLink.className = '';         
            }
        }
        //console.log(x+'###'+y);
        idMarking = objId.replace('tile', 'marking');
        if (document.getElementById(id)) {
            thisObj.magnifiedIslandObj = document.getElementById(id);
            
            thisObj.markedIslandObj = document.getElementById(idMarking);
            thisObj.markedIslandObj.className = 'islandMarked';
            thisObj.markedIslandXY  = [x, y]; 
            
            objLink.className = 'islandLinkMarked';         
        }
        
        // Inseldaten in Seite schreiben
        // Breadcrumbs
        isl = thisObj.islands[x][y];
        var text = isl[1] +' ['+ x +':'+ y +']';
        
        document.getElementById('islandBread').innerHTML = '<a title="'+ mapText['markedislandlink'] +'" class="island" onclick="map.centerIsland('+ x +', '+ y +');" href="#">'+ text +'</a>';
        // Info
        document.getElementById('islandName').innerHTML =  thisObj.islands[x][y][1];

        //document.getElementById('islandActions').className = 'nohidden';
        document.getElementById('islandInfos').className = 'nohidden';
        document.getElementById('tradegoodLabel').innerHTML = '<a href="/index.php?view=informations&articleId=10013&mainId=10013">'+tradegoodText[isl[2]]+ '</a>';
        document.getElementById('wonderLabel').innerHTML = '<a href="/index.php?view=wonderDetail&wonderId='+isl[3]+'">'+wonderText[isl[3]]+'</a';
        var button1 = Dom.get('islandAddButton');
        var button2 = Dom.get('islandRemoveButton');
       
        if(shortcuts[x] != undefined && shortcuts[x][y] != 'undefined' && shortcuts[x][y] == 1) {
        	button1.style.display = 'none';
        	button2.href="?action=WorldMap&function=removeIslandFromShortcut&islandX="+x+"&islandY="+y+"&actionRequest=32f496a738c28fb6810ebc2f4ddb8f67";
        	button2.style.display = 'inline';
        } else if(shortcuts[x] != undefined && shortcuts[x][y] != 'undefined' && shortcuts[x][y] == 2) {
        	button1.style.display = 'none';
        	button2.style.display = 'none';
        } else {
        	button1.style.display = 'inline';
        	button1.onclick = function() { showIslandAddDialog(isl[0], x, y, thisObj.islands[x][y][1]); };
        	button2.href="#";
        	button2.style.display = 'none';
        }
    }
    
    this.clickIsland = function(objId) {
        if (thisObj.action == '') {
            obj = document.getElementById(objId);
            var x = obj.mapX;
            var y = obj.mapY;
            var id = objId.replace('tile', 'magnify');
            
//            console.log('clickIsland: '+ id);
            if (thisObj.magnifiedIslandObj) {
                if (thisObj.magnifiedIslandObj.id == id) { // klick 2 auf Objekt
                    window.location.href = '/island?id=' + thisObj.islands[x][y][0];
                    return true;
                } else { // klick auf anderes Objekt
                }
            } else {
                thisObj.magnifiedIslandObj = document.getElementById(id);
            }
            thisObj.markIsland(objId);
        }
    }
    
    // zeigt spieler der insel an (funktioniert, ist aber nicht eingebaut)
    this.getIslandData = function(x, y) {
        if (!thisObj.waitingForIslandData) {
            thisObj.waitingForIslandData = true;
            jsonUrl  = '?action=WorldMap&function=getJSONIsland&x='+ x;
            jsonUrl += '&y='+ y;
            ajaxRequest(jsonUrl, thisObj.handleIslandData);    
        }
    }
    // zeigt spieler der insel an  (funktioniert, ist aber nicht eingebaut)
    this.handleIslandData = function(JSONResponse) {
        var responseData = JSON.parse(JSONResponse);

        // TODO: schoen machen, wenns dann doch mal eingbaut wird
        document.getElementById('information').innerHTML = '';
        for (var i in responseData['data']) {
            document.getElementById('information').innerHTML += responseData['data'][i]['name'] + '('+ responseData['data'][i]['avatar_name']+')' +'<br />';
        }
        thisObj.waitingForIslandData = false;
    }
    
    this.getXYOffset = function(obj) {
        var xy = [0, 0];
        do {
            xy[0] += obj.offsetLeft;
            xy[1] += obj.offsetTop;
            obj = obj.parentNode;
        }   while(obj.parentNode) 
//        console.log('Left: '+ xy)
        return xy;
    }
    
    thisObj.cityStatus = 1;
    this.switchCities = function() {
        thisObj.cityStatus= (thisObj.cityStatus+1)%2;
        if(thisObj.cityStatus==0) document.getElementById('buttonCities').className='deactivated';
        else document.getElementById('buttonCities').className='';
        for (var j=0;j<=thisObj.maxX;j++){
            for (var i=0;i<=thisObj.maxY;i++){
                cities= document.getElementById('cities_'+i+'_'+j);
                cities.style.visibility = (thisObj.cityStatus) ? 'visible':'hidden';
            }
        }   
    }
    thisObj.tradegoodStatus = 1;
    this.switchTradegood = function() {
        thisObj.tradegoodStatus= (thisObj.tradegoodStatus+1)%2;
        if(thisObj.tradegoodStatus==0) document.getElementById('buttonTradegood').className='deactivated';
        else document.getElementById('buttonTradegood').className='';
        for (var j=0;j<=thisObj.maxX;j++){
            for (var i=0;i<=thisObj.maxY;i++){
                tradegood= document.getElementById('tradegood_'+i+'_'+j);
                tradegood.style.visibility = (thisObj.tradegoodStatus) ? 'visible':'hidden';
            }
        }   
    }

    
    // drag and drop
    addListener(thisObj.dragHandleObj, 'mousedown', thisObj.dragHandle);
    addListener(document.getElementById('linkMap'), 'mousedown', thisObj.dragHandle);
    //addListener(thisObj.dragHandleObj, 'mouseout', thisObj.dragStop);
    addListener(document, 'mouseup', thisObj.dragStop)
    thisObj.initCoords();
    
}


function showIslandAddDialog(islandId, islandX, islandY, islandName) {
	var box = Dom.get('annotationBox');
	box.style.display='block';
	var closeButton = box.firstChild;
	var header = Dom.get('annotationHeader');
	header.innerHTML = "Add island to shortcut list: "+islandName;
	var content = Dom.get('annotationText');
	//content.innerHTML = "Insel Anfügen und so";
	document.addIslandForm.islandX.value = islandX;
	document.addIslandForm.islandY.value = islandY;
	document.addIslandForm.label.value = islandName;
}

{/literal}
</script>
<!--<script type="text/javascript" src="js/worldmap/worldmap.js"></script>-->

<script type="text/javascript">
{literal}
<!--
Event.onDOMReady( function() {
    replaceSelect(Dom.get("homeCitySelect"));
});

//-->
{/literal}
</script>

<style type="text/css">
{literal}
#worldmap_iso #container #mapShortcutInput {
	background-image:url(/img/layout/bg_mapnav_coord.jpg);
	background-repeat:repeat;
	height:28px;
	padding-top:3px;
	position:relative;
	padding-left:18px;
}

.citySpecialSelect .dropbutton {          background-position: 0px -25px; padding-left:8px; height:25px; line-height:25px; cursor:default;}
{/literal}
</style>


<div id="breadcrumbs">
    <h3>You are here:</h3>
    <span id="worldBread" class="world">World</span>
    <div id="islandBread" class="island"></div>
</div>

<!-- all items going to the left side get class dynamic -->
	<div id="navigation" class="dynamic" style="z-index:10000">
		<h3 class="header">Navigation</h3>
		<div class="content">
		
			<form name="navInputForm" action="javaScript:void(null);" onsubmit="map.jumpToCoord();">
			<div id="mapCoordInput"  style="position:relative;">
				<label for="inputXCoord" class="x">X:</label>
				<input class="x" id="inputXCoord" type="text" name="xcoord"  maxlength=4 value="{$start_x}" />

				<label for="inputYCoord" class="y">Y:</label>
				<input class="y" id="inputYCoord" type="text" name="ycoord"  maxlength=4 value="{$start_y}" />
				<input class="submitButton" type="image" src="http://static.ikariem.org/img/img/blank.gif" name="submit" />
			</div>
			</form>
			
			
			
			<div id="mapControls"  style="position:relative;">
				<ul class="visibility">
					<li><a href="#" onClick='map.switchTradegood();' id="buttonTradegood"></a></li>

					<li><a href="#" onClick='map.switchCities();' id="buttonCities"></a></li>
				</ul>
				<ul class="scrolling">
                    <li class="nw"><a href="#" onclick="map.moveBy(-1,-1); return false;"></a></li>
					<li class="n"><a href="#" onclick="map.moveBy(0,-1); return false;"></a></li>
					<li class="ne"><a href="#" onclick="map.moveBy(1,-1); return false;"></a></li>
					<li class="w"><a href="#" onclick="map.moveBy(-1,0); return false;"></a></li>
					<li class="e"><a href="#" onclick="map.moveBy(1,0); return false;"></a> </li>

					<li class="sw"><a href="#" onclick="map.moveBy(-1,1); return false;"></a></li>
					<li class="s"><a href="#" onclick="map.moveBy(0,1); return false;"></a></li>
					<li class="se"><a href="#" onclick="map.moveBy(1,1); return false;"></a></li>
				</ul>
			</div>
			
			
			<form name="navShortcutForm" action="javaScript:void(null);" onsubmit="map.jumpToShortcut();">
			<div id="mapShortcutInput"  style="position:relative;" title="Island shortcuts">
				
				<select id="homeCitySelect"
                               class="citySpecialSelect smallFont"
                               name="newHomeCity" tabindex="1" onchange="map.jumpToShortcut();">
							{foreach from=$cities item=city}
								<option class="coords" value="{$city.posx}:{$city.posy}" title="Trade good: {$city.tradegood_name}"{if $city.town_id == $town_id} selected="selected"{/if}>[{$city.posx}:{$city.posy}]&nbsp;{$city.town_name}</option>
							{/foreach}
				</select>

			
				
			</div>
			</form>
			
			
		</div>
		<div class="footer"></div>
	</div>
    
	<div id="information" class="dynamic">
		<h3 id="islandName" class="header">Info</h3>
		<div class="content">

            <table id="islandInfos">
                <tr><th>Trade good:</th><td id="tradegoodLabel" class=label></td></tr>
                <tr><th>Wonder:</th><td id="wonderLabel"  class="label"></td></tr>
            </table>
            
            <div class="centerButton"><p><a class="button" id="islandAddButton" href="#" title="Add this island to the shortcut list">Add shortcut</a>
            <a class="button" id="islandRemoveButton" href="#"  title="Remove this island from the shortcut list">Delete shortcut</a></p>
            </div>

           
		</div>
		<div class="footer"></div>
	</div>
    
<!-- the main view. take care that it stretches. -->
<div id="mainview" style="overflow:visible;z-index:30">
	
	<div id="annotationBox" style="display:none;">
        <div class="close" onclick="this.parentNode.style.display='none'"></div>
        <h3 class="header" id="annotationHeader"></h3>
        <div class="content" style="padding:10px;" id="annotationText">

            <form name="addIslandForm" action="index.php" method="POST">
				<input type="hidden" name="action" value="WorldMap" /> 
				<input type="hidden" name="function" value="addIslandToShortcut" /> 
				<input type="hidden" name="actionRequest" value="32f496a738c28fb6810ebc2f4ddb8f67" />
			    <input type="hidden" name="islandX" value="1" />
			    <input type="hidden" name="islandY" value="1" />
			<p>A text containing 15 characters can be entered on every island on the shortcut list.</p>
			<div class="centerButton">Text: <input type="text" name="label" value="inselname"  maxlength="15"></div> 
			<div class="centerButton"><a class="button" onclick="document.addIslandForm.submit();" href="#">Add island</a></div>

			</form>
        </div>
        <div class="footer"></div>
    </div>

    <div id="scrollcover" style="overflow: hidden ;background-image:url(/img/world/bg_ocean01.gif);z-index:35">
        <div id="worldmap" style="overflow:visible;position:absolute;z-index:40;left:240px;top:-300px;">

            <div id="map1" style="position:absolute;z-index:50;cursor:move;">
            
                <div align='center' alt=''  valign='middle' id='tile_0_0' 
                    class = "ocean1"
                    style='z-index:100;position:absolute; width:240px; height:120px; left:0px; top:0px;'
                    ><div id='wonder_0_0' ></div
                    ><div id='tradegood_0_0' ></div
                    ><div id='cities_0_0'></div
                    ><div id='marking_0_0'></div
                    ><div></div
                    ><div id='magnify_0_0'></div
                ></div>

                <div align='center' alt=''  valign='middle' id='tile_1_0' 
                    class = "ocean1"
                    style='z-index:101;position:absolute; width:240px; height:120px; left:120px; top:60px;'
                    ><div id='wonder_1_0' ></div
                    ><div id='tradegood_1_0' ></div
                    ><div id='cities_1_0'></div
                    ><div id='marking_1_0'></div
                    ><div></div
                    ><div id='magnify_1_0'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_2_0' 
                    class = "ocean1"
                    style='z-index:102;position:absolute; width:240px; height:120px; left:240px; top:120px;'
                    ><div id='wonder_2_0' ></div
                    ><div id='tradegood_2_0' ></div
                    ><div id='cities_2_0'></div
                    ><div id='marking_2_0'></div
                    ><div></div
                    ><div id='magnify_2_0'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_3_0' 
                    class = "ocean1"
                    style='z-index:103;position:absolute; width:240px; height:120px; left:360px; top:180px;'
                    ><div id='wonder_3_0' ></div
                    ><div id='tradegood_3_0' ></div
                    ><div id='cities_3_0'></div
                    ><div id='marking_3_0'></div
                    ><div></div
                    ><div id='magnify_3_0'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_4_0' 
                    class = "ocean1"
                    style='z-index:104;position:absolute; width:240px; height:120px; left:480px; top:240px;'
                    ><div id='wonder_4_0' ></div
                    ><div id='tradegood_4_0' ></div
                    ><div id='cities_4_0'></div
                    ><div id='marking_4_0'></div
                    ><div></div
                    ><div id='magnify_4_0'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_5_0' 
                    class = "ocean1"
                    style='z-index:105;position:absolute; width:240px; height:120px; left:600px; top:300px;'
                    ><div id='wonder_5_0' ></div
                    ><div id='tradegood_5_0' ></div
                    ><div id='cities_5_0'></div
                    ><div id='marking_5_0'></div
                    ><div></div
                    ><div id='magnify_5_0'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_6_0' 
                    class = "ocean1"
                    style='z-index:106;position:absolute; width:240px; height:120px; left:720px; top:360px;'
                    ><div id='wonder_6_0' ></div
                    ><div id='tradegood_6_0' ></div
                    ><div id='cities_6_0'></div
                    ><div id='marking_6_0'></div
                    ><div></div
                    ><div id='magnify_6_0'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_7_0' 
                    class = "ocean1"
                    style='z-index:107;position:absolute; width:240px; height:120px; left:840px; top:420px;'
                    ><div id='wonder_7_0' ></div
                    ><div id='tradegood_7_0' ></div
                    ><div id='cities_7_0'></div
                    ><div id='marking_7_0'></div
                    ><div></div
                    ><div id='magnify_7_0'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_8_0' 
                    class = "ocean1"
                    style='z-index:108;position:absolute; width:240px; height:120px; left:960px; top:480px;'
                    ><div id='wonder_8_0' ></div
                    ><div id='tradegood_8_0' ></div
                    ><div id='cities_8_0'></div
                    ><div id='marking_8_0'></div
                    ><div></div
                    ><div id='magnify_8_0'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_9_0' 
                    class = "ocean1"
                    style='z-index:109;position:absolute; width:240px; height:120px; left:1080px; top:540px;'
                    ><div id='wonder_9_0' ></div
                    ><div id='tradegood_9_0' ></div
                    ><div id='cities_9_0'></div
                    ><div id='marking_9_0'></div
                    ><div></div
                    ><div id='magnify_9_0'></div
                ></div>

                <div align='center' alt=''  valign='middle' id='tile_0_1' 
                    class = "ocean1"
                    style='z-index:110;position:absolute; width:240px; height:120px; left:-120px; top:60px;'
                    ><div id='wonder_0_1' ></div
                    ><div id='tradegood_0_1' ></div
                    ><div id='cities_0_1'></div
                    ><div id='marking_0_1'></div
                    ><div></div
                    ><div id='magnify_0_1'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_1_1' 
                    class = "ocean1"
                    style='z-index:111;position:absolute; width:240px; height:120px; left:0px; top:120px;'
                    ><div id='wonder_1_1' ></div
                    ><div id='tradegood_1_1' ></div
                    ><div id='cities_1_1'></div
                    ><div id='marking_1_1'></div
                    ><div></div
                    ><div id='magnify_1_1'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_2_1' 
                    class = "ocean1"
                    style='z-index:112;position:absolute; width:240px; height:120px; left:120px; top:180px;'
                    ><div id='wonder_2_1' ></div
                    ><div id='tradegood_2_1' ></div
                    ><div id='cities_2_1'></div
                    ><div id='marking_2_1'></div
                    ><div></div
                    ><div id='magnify_2_1'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_3_1' 
                    class = "ocean1"
                    style='z-index:113;position:absolute; width:240px; height:120px; left:240px; top:240px;'
                    ><div id='wonder_3_1' ></div
                    ><div id='tradegood_3_1' ></div
                    ><div id='cities_3_1'></div
                    ><div id='marking_3_1'></div
                    ><div></div
                    ><div id='magnify_3_1'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_4_1' 
                    class = "ocean1"
                    style='z-index:114;position:absolute; width:240px; height:120px; left:360px; top:300px;'
                    ><div id='wonder_4_1' ></div
                    ><div id='tradegood_4_1' ></div
                    ><div id='cities_4_1'></div
                    ><div id='marking_4_1'></div
                    ><div></div
                    ><div id='magnify_4_1'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_5_1' 
                    class = "ocean1"
                    style='z-index:115;position:absolute; width:240px; height:120px; left:480px; top:360px;'
                    ><div id='wonder_5_1' ></div
                    ><div id='tradegood_5_1' ></div
                    ><div id='cities_5_1'></div
                    ><div id='marking_5_1'></div
                    ><div></div
                    ><div id='magnify_5_1'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_6_1' 
                    class = "ocean1"
                    style='z-index:116;position:absolute; width:240px; height:120px; left:600px; top:420px;'
                    ><div id='wonder_6_1' ></div
                    ><div id='tradegood_6_1' ></div
                    ><div id='cities_6_1'></div
                    ><div id='marking_6_1'></div
                    ><div></div
                    ><div id='magnify_6_1'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_7_1' 
                    class = "ocean1"
                    style='z-index:117;position:absolute; width:240px; height:120px; left:720px; top:480px;'
                    ><div id='wonder_7_1' ></div
                    ><div id='tradegood_7_1' ></div
                    ><div id='cities_7_1'></div
                    ><div id='marking_7_1'></div
                    ><div></div
                    ><div id='magnify_7_1'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_8_1' 
                    class = "ocean1"
                    style='z-index:118;position:absolute; width:240px; height:120px; left:840px; top:540px;'
                    ><div id='wonder_8_1' ></div
                    ><div id='tradegood_8_1' ></div
                    ><div id='cities_8_1'></div
                    ><div id='marking_8_1'></div
                    ><div></div
                    ><div id='magnify_8_1'></div
                ></div>

                <div align='center' alt=''  valign='middle' id='tile_9_1' 
                    class = "ocean1"
                    style='z-index:119;position:absolute; width:240px; height:120px; left:960px; top:600px;'
                    ><div id='wonder_9_1' ></div
                    ><div id='tradegood_9_1' ></div
                    ><div id='cities_9_1'></div
                    ><div id='marking_9_1'></div
                    ><div></div
                    ><div id='magnify_9_1'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_0_2' 
                    class = "ocean1"
                    style='z-index:120;position:absolute; width:240px; height:120px; left:-240px; top:120px;'
                    ><div id='wonder_0_2' ></div
                    ><div id='tradegood_0_2' ></div
                    ><div id='cities_0_2'></div
                    ><div id='marking_0_2'></div
                    ><div></div
                    ><div id='magnify_0_2'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_1_2' 
                    class = "ocean1"
                    style='z-index:121;position:absolute; width:240px; height:120px; left:-120px; top:180px;'
                    ><div id='wonder_1_2' ></div
                    ><div id='tradegood_1_2' ></div
                    ><div id='cities_1_2'></div
                    ><div id='marking_1_2'></div
                    ><div></div
                    ><div id='magnify_1_2'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_2_2' 
                    class = "ocean1"
                    style='z-index:122;position:absolute; width:240px; height:120px; left:0px; top:240px;'
                    ><div id='wonder_2_2' ></div
                    ><div id='tradegood_2_2' ></div
                    ><div id='cities_2_2'></div
                    ><div id='marking_2_2'></div
                    ><div></div
                    ><div id='magnify_2_2'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_3_2' 
                    class = "ocean1"
                    style='z-index:123;position:absolute; width:240px; height:120px; left:120px; top:300px;'
                    ><div id='wonder_3_2' ></div
                    ><div id='tradegood_3_2' ></div
                    ><div id='cities_3_2'></div
                    ><div id='marking_3_2'></div
                    ><div></div
                    ><div id='magnify_3_2'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_4_2' 
                    class = "ocean1"
                    style='z-index:124;position:absolute; width:240px; height:120px; left:240px; top:360px;'
                    ><div id='wonder_4_2' ></div
                    ><div id='tradegood_4_2' ></div
                    ><div id='cities_4_2'></div
                    ><div id='marking_4_2'></div
                    ><div></div
                    ><div id='magnify_4_2'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_5_2' 
                    class = "ocean1"
                    style='z-index:125;position:absolute; width:240px; height:120px; left:360px; top:420px;'
                    ><div id='wonder_5_2' ></div
                    ><div id='tradegood_5_2' ></div
                    ><div id='cities_5_2'></div
                    ><div id='marking_5_2'></div
                    ><div></div
                    ><div id='magnify_5_2'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_6_2' 
                    class = "ocean1"
                    style='z-index:126;position:absolute; width:240px; height:120px; left:480px; top:480px;'
                    ><div id='wonder_6_2' ></div
                    ><div id='tradegood_6_2' ></div
                    ><div id='cities_6_2'></div
                    ><div id='marking_6_2'></div
                    ><div></div
                    ><div id='magnify_6_2'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_7_2' 
                    class = "ocean1"
                    style='z-index:127;position:absolute; width:240px; height:120px; left:600px; top:540px;'
                    ><div id='wonder_7_2' ></div
                    ><div id='tradegood_7_2' ></div
                    ><div id='cities_7_2'></div
                    ><div id='marking_7_2'></div
                    ><div></div
                    ><div id='magnify_7_2'></div
                ></div>

                <div align='center' alt=''  valign='middle' id='tile_8_2' 
                    class = "ocean1"
                    style='z-index:128;position:absolute; width:240px; height:120px; left:720px; top:600px;'
                    ><div id='wonder_8_2' ></div
                    ><div id='tradegood_8_2' ></div
                    ><div id='cities_8_2'></div
                    ><div id='marking_8_2'></div
                    ><div></div
                    ><div id='magnify_8_2'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_9_2' 
                    class = "ocean1"
                    style='z-index:129;position:absolute; width:240px; height:120px; left:840px; top:660px;'
                    ><div id='wonder_9_2' ></div
                    ><div id='tradegood_9_2' ></div
                    ><div id='cities_9_2'></div
                    ><div id='marking_9_2'></div
                    ><div></div
                    ><div id='magnify_9_2'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_0_3' 
                    class = "ocean1"
                    style='z-index:130;position:absolute; width:240px; height:120px; left:-360px; top:180px;'
                    ><div id='wonder_0_3' ></div
                    ><div id='tradegood_0_3' ></div
                    ><div id='cities_0_3'></div
                    ><div id='marking_0_3'></div
                    ><div></div
                    ><div id='magnify_0_3'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_1_3' 
                    class = "ocean1"
                    style='z-index:131;position:absolute; width:240px; height:120px; left:-240px; top:240px;'
                    ><div id='wonder_1_3' ></div
                    ><div id='tradegood_1_3' ></div
                    ><div id='cities_1_3'></div
                    ><div id='marking_1_3'></div
                    ><div></div
                    ><div id='magnify_1_3'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_2_3' 
                    class = "ocean1"
                    style='z-index:132;position:absolute; width:240px; height:120px; left:-120px; top:300px;'
                    ><div id='wonder_2_3' ></div
                    ><div id='tradegood_2_3' ></div
                    ><div id='cities_2_3'></div
                    ><div id='marking_2_3'></div
                    ><div></div
                    ><div id='magnify_2_3'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_3_3' 
                    class = "ocean1"
                    style='z-index:133;position:absolute; width:240px; height:120px; left:0px; top:360px;'
                    ><div id='wonder_3_3' ></div
                    ><div id='tradegood_3_3' ></div
                    ><div id='cities_3_3'></div
                    ><div id='marking_3_3'></div
                    ><div></div
                    ><div id='magnify_3_3'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_4_3' 
                    class = "ocean1"
                    style='z-index:134;position:absolute; width:240px; height:120px; left:120px; top:420px;'
                    ><div id='wonder_4_3' ></div
                    ><div id='tradegood_4_3' ></div
                    ><div id='cities_4_3'></div
                    ><div id='marking_4_3'></div
                    ><div></div
                    ><div id='magnify_4_3'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_5_3' 
                    class = "ocean1"
                    style='z-index:135;position:absolute; width:240px; height:120px; left:240px; top:480px;'
                    ><div id='wonder_5_3' ></div
                    ><div id='tradegood_5_3' ></div
                    ><div id='cities_5_3'></div
                    ><div id='marking_5_3'></div
                    ><div></div
                    ><div id='magnify_5_3'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_6_3' 
                    class = "ocean1"
                    style='z-index:136;position:absolute; width:240px; height:120px; left:360px; top:540px;'
                    ><div id='wonder_6_3' ></div
                    ><div id='tradegood_6_3' ></div
                    ><div id='cities_6_3'></div
                    ><div id='marking_6_3'></div
                    ><div></div
                    ><div id='magnify_6_3'></div
                ></div>

                <div align='center' alt=''  valign='middle' id='tile_7_3' 
                    class = "ocean1"
                    style='z-index:137;position:absolute; width:240px; height:120px; left:480px; top:600px;'
                    ><div id='wonder_7_3' ></div
                    ><div id='tradegood_7_3' ></div
                    ><div id='cities_7_3'></div
                    ><div id='marking_7_3'></div
                    ><div></div
                    ><div id='magnify_7_3'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_8_3' 
                    class = "ocean1"
                    style='z-index:138;position:absolute; width:240px; height:120px; left:600px; top:660px;'
                    ><div id='wonder_8_3' ></div
                    ><div id='tradegood_8_3' ></div
                    ><div id='cities_8_3'></div
                    ><div id='marking_8_3'></div
                    ><div></div
                    ><div id='magnify_8_3'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_9_3' 
                    class = "ocean1"
                    style='z-index:139;position:absolute; width:240px; height:120px; left:720px; top:720px;'
                    ><div id='wonder_9_3' ></div
                    ><div id='tradegood_9_3' ></div
                    ><div id='cities_9_3'></div
                    ><div id='marking_9_3'></div
                    ><div></div
                    ><div id='magnify_9_3'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_0_4' 
                    class = "ocean1"
                    style='z-index:140;position:absolute; width:240px; height:120px; left:-480px; top:240px;'
                    ><div id='wonder_0_4' ></div
                    ><div id='tradegood_0_4' ></div
                    ><div id='cities_0_4'></div
                    ><div id='marking_0_4'></div
                    ><div></div
                    ><div id='magnify_0_4'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_1_4' 
                    class = "ocean1"
                    style='z-index:141;position:absolute; width:240px; height:120px; left:-360px; top:300px;'
                    ><div id='wonder_1_4' ></div
                    ><div id='tradegood_1_4' ></div
                    ><div id='cities_1_4'></div
                    ><div id='marking_1_4'></div
                    ><div></div
                    ><div id='magnify_1_4'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_2_4' 
                    class = "ocean1"
                    style='z-index:142;position:absolute; width:240px; height:120px; left:-240px; top:360px;'
                    ><div id='wonder_2_4' ></div
                    ><div id='tradegood_2_4' ></div
                    ><div id='cities_2_4'></div
                    ><div id='marking_2_4'></div
                    ><div></div
                    ><div id='magnify_2_4'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_3_4' 
                    class = "ocean1"
                    style='z-index:143;position:absolute; width:240px; height:120px; left:-120px; top:420px;'
                    ><div id='wonder_3_4' ></div
                    ><div id='tradegood_3_4' ></div
                    ><div id='cities_3_4'></div
                    ><div id='marking_3_4'></div
                    ><div></div
                    ><div id='magnify_3_4'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_4_4' 
                    class = "ocean1"
                    style='z-index:144;position:absolute; width:240px; height:120px; left:0px; top:480px;'
                    ><div id='wonder_4_4' ></div
                    ><div id='tradegood_4_4' ></div
                    ><div id='cities_4_4'></div
                    ><div id='marking_4_4'></div
                    ><div></div
                    ><div id='magnify_4_4'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_5_4' 
                    class = "ocean1"
                    style='z-index:145;position:absolute; width:240px; height:120px; left:120px; top:540px;'
                    ><div id='wonder_5_4' ></div
                    ><div id='tradegood_5_4' ></div
                    ><div id='cities_5_4'></div
                    ><div id='marking_5_4'></div
                    ><div></div
                    ><div id='magnify_5_4'></div
                ></div>

                <div align='center' alt=''  valign='middle' id='tile_6_4' 
                    class = "ocean1"
                    style='z-index:146;position:absolute; width:240px; height:120px; left:240px; top:600px;'
                    ><div id='wonder_6_4' ></div
                    ><div id='tradegood_6_4' ></div
                    ><div id='cities_6_4'></div
                    ><div id='marking_6_4'></div
                    ><div></div
                    ><div id='magnify_6_4'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_7_4' 
                    class = "ocean1"
                    style='z-index:147;position:absolute; width:240px; height:120px; left:360px; top:660px;'
                    ><div id='wonder_7_4' ></div
                    ><div id='tradegood_7_4' ></div
                    ><div id='cities_7_4'></div
                    ><div id='marking_7_4'></div
                    ><div></div
                    ><div id='magnify_7_4'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_8_4' 
                    class = "ocean1"
                    style='z-index:148;position:absolute; width:240px; height:120px; left:480px; top:720px;'
                    ><div id='wonder_8_4' ></div
                    ><div id='tradegood_8_4' ></div
                    ><div id='cities_8_4'></div
                    ><div id='marking_8_4'></div
                    ><div></div
                    ><div id='magnify_8_4'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_9_4' 
                    class = "ocean1"
                    style='z-index:149;position:absolute; width:240px; height:120px; left:600px; top:780px;'
                    ><div id='wonder_9_4' ></div
                    ><div id='tradegood_9_4' ></div
                    ><div id='cities_9_4'></div
                    ><div id='marking_9_4'></div
                    ><div></div
                    ><div id='magnify_9_4'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_0_5' 
                    class = "ocean1"
                    style='z-index:150;position:absolute; width:240px; height:120px; left:-600px; top:300px;'
                    ><div id='wonder_0_5' ></div
                    ><div id='tradegood_0_5' ></div
                    ><div id='cities_0_5'></div
                    ><div id='marking_0_5'></div
                    ><div></div
                    ><div id='magnify_0_5'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_1_5' 
                    class = "ocean1"
                    style='z-index:151;position:absolute; width:240px; height:120px; left:-480px; top:360px;'
                    ><div id='wonder_1_5' ></div
                    ><div id='tradegood_1_5' ></div
                    ><div id='cities_1_5'></div
                    ><div id='marking_1_5'></div
                    ><div></div
                    ><div id='magnify_1_5'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_2_5' 
                    class = "ocean1"
                    style='z-index:152;position:absolute; width:240px; height:120px; left:-360px; top:420px;'
                    ><div id='wonder_2_5' ></div
                    ><div id='tradegood_2_5' ></div
                    ><div id='cities_2_5'></div
                    ><div id='marking_2_5'></div
                    ><div></div
                    ><div id='magnify_2_5'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_3_5' 
                    class = "ocean1"
                    style='z-index:153;position:absolute; width:240px; height:120px; left:-240px; top:480px;'
                    ><div id='wonder_3_5' ></div
                    ><div id='tradegood_3_5' ></div
                    ><div id='cities_3_5'></div
                    ><div id='marking_3_5'></div
                    ><div></div
                    ><div id='magnify_3_5'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_4_5' 
                    class = "ocean1"
                    style='z-index:154;position:absolute; width:240px; height:120px; left:-120px; top:540px;'
                    ><div id='wonder_4_5' ></div
                    ><div id='tradegood_4_5' ></div
                    ><div id='cities_4_5'></div
                    ><div id='marking_4_5'></div
                    ><div></div
                    ><div id='magnify_4_5'></div
                ></div>

                <div align='center' alt=''  valign='middle' id='tile_5_5' 
                    class = "ocean1"
                    style='z-index:155;position:absolute; width:240px; height:120px; left:0px; top:600px;'
                    ><div id='wonder_5_5' ></div
                    ><div id='tradegood_5_5' ></div
                    ><div id='cities_5_5'></div
                    ><div id='marking_5_5'></div
                    ><div></div
                    ><div id='magnify_5_5'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_6_5' 
                    class = "ocean1"
                    style='z-index:156;position:absolute; width:240px; height:120px; left:120px; top:660px;'
                    ><div id='wonder_6_5' ></div
                    ><div id='tradegood_6_5' ></div
                    ><div id='cities_6_5'></div
                    ><div id='marking_6_5'></div
                    ><div></div
                    ><div id='magnify_6_5'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_7_5' 
                    class = "ocean1"
                    style='z-index:157;position:absolute; width:240px; height:120px; left:240px; top:720px;'
                    ><div id='wonder_7_5' ></div
                    ><div id='tradegood_7_5' ></div
                    ><div id='cities_7_5'></div
                    ><div id='marking_7_5'></div
                    ><div></div
                    ><div id='magnify_7_5'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_8_5' 
                    class = "ocean1"
                    style='z-index:158;position:absolute; width:240px; height:120px; left:360px; top:780px;'
                    ><div id='wonder_8_5' ></div
                    ><div id='tradegood_8_5' ></div
                    ><div id='cities_8_5'></div
                    ><div id='marking_8_5'></div
                    ><div></div
                    ><div id='magnify_8_5'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_9_5' 
                    class = "ocean1"
                    style='z-index:159;position:absolute; width:240px; height:120px; left:480px; top:840px;'
                    ><div id='wonder_9_5' ></div
                    ><div id='tradegood_9_5' ></div
                    ><div id='cities_9_5'></div
                    ><div id='marking_9_5'></div
                    ><div></div
                    ><div id='magnify_9_5'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_0_6' 
                    class = "ocean1"
                    style='z-index:160;position:absolute; width:240px; height:120px; left:-720px; top:360px;'
                    ><div id='wonder_0_6' ></div
                    ><div id='tradegood_0_6' ></div
                    ><div id='cities_0_6'></div
                    ><div id='marking_0_6'></div
                    ><div></div
                    ><div id='magnify_0_6'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_1_6' 
                    class = "ocean1"
                    style='z-index:161;position:absolute; width:240px; height:120px; left:-600px; top:420px;'
                    ><div id='wonder_1_6' ></div
                    ><div id='tradegood_1_6' ></div
                    ><div id='cities_1_6'></div
                    ><div id='marking_1_6'></div
                    ><div></div
                    ><div id='magnify_1_6'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_2_6' 
                    class = "ocean1"
                    style='z-index:162;position:absolute; width:240px; height:120px; left:-480px; top:480px;'
                    ><div id='wonder_2_6' ></div
                    ><div id='tradegood_2_6' ></div
                    ><div id='cities_2_6'></div
                    ><div id='marking_2_6'></div
                    ><div></div
                    ><div id='magnify_2_6'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_3_6' 
                    class = "ocean1"
                    style='z-index:163;position:absolute; width:240px; height:120px; left:-360px; top:540px;'
                    ><div id='wonder_3_6' ></div
                    ><div id='tradegood_3_6' ></div
                    ><div id='cities_3_6'></div
                    ><div id='marking_3_6'></div
                    ><div></div
                    ><div id='magnify_3_6'></div
                ></div>

                <div align='center' alt=''  valign='middle' id='tile_4_6' 
                    class = "ocean1"
                    style='z-index:164;position:absolute; width:240px; height:120px; left:-240px; top:600px;'
                    ><div id='wonder_4_6' ></div
                    ><div id='tradegood_4_6' ></div
                    ><div id='cities_4_6'></div
                    ><div id='marking_4_6'></div
                    ><div></div
                    ><div id='magnify_4_6'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_5_6' 
                    class = "ocean1"
                    style='z-index:165;position:absolute; width:240px; height:120px; left:-120px; top:660px;'
                    ><div id='wonder_5_6' ></div
                    ><div id='tradegood_5_6' ></div
                    ><div id='cities_5_6'></div
                    ><div id='marking_5_6'></div
                    ><div></div
                    ><div id='magnify_5_6'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_6_6' 
                    class = "ocean1"
                    style='z-index:166;position:absolute; width:240px; height:120px; left:0px; top:720px;'
                    ><div id='wonder_6_6' ></div
                    ><div id='tradegood_6_6' ></div
                    ><div id='cities_6_6'></div
                    ><div id='marking_6_6'></div
                    ><div></div
                    ><div id='magnify_6_6'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_7_6' 
                    class = "ocean1"
                    style='z-index:167;position:absolute; width:240px; height:120px; left:120px; top:780px;'
                    ><div id='wonder_7_6' ></div
                    ><div id='tradegood_7_6' ></div
                    ><div id='cities_7_6'></div
                    ><div id='marking_7_6'></div
                    ><div></div
                    ><div id='magnify_7_6'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_8_6' 
                    class = "ocean1"
                    style='z-index:168;position:absolute; width:240px; height:120px; left:240px; top:840px;'
                    ><div id='wonder_8_6' ></div
                    ><div id='tradegood_8_6' ></div
                    ><div id='cities_8_6'></div
                    ><div id='marking_8_6'></div
                    ><div></div
                    ><div id='magnify_8_6'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_9_6' 
                    class = "ocean1"
                    style='z-index:169;position:absolute; width:240px; height:120px; left:360px; top:900px;'
                    ><div id='wonder_9_6' ></div
                    ><div id='tradegood_9_6' ></div
                    ><div id='cities_9_6'></div
                    ><div id='marking_9_6'></div
                    ><div></div
                    ><div id='magnify_9_6'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_0_7' 
                    class = "ocean1"
                    style='z-index:170;position:absolute; width:240px; height:120px; left:-840px; top:420px;'
                    ><div id='wonder_0_7' ></div
                    ><div id='tradegood_0_7' ></div
                    ><div id='cities_0_7'></div
                    ><div id='marking_0_7'></div
                    ><div></div
                    ><div id='magnify_0_7'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_1_7' 
                    class = "ocean1"
                    style='z-index:171;position:absolute; width:240px; height:120px; left:-720px; top:480px;'
                    ><div id='wonder_1_7' ></div
                    ><div id='tradegood_1_7' ></div
                    ><div id='cities_1_7'></div
                    ><div id='marking_1_7'></div
                    ><div></div
                    ><div id='magnify_1_7'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_2_7' 
                    class = "ocean1"
                    style='z-index:172;position:absolute; width:240px; height:120px; left:-600px; top:540px;'
                    ><div id='wonder_2_7' ></div
                    ><div id='tradegood_2_7' ></div
                    ><div id='cities_2_7'></div
                    ><div id='marking_2_7'></div
                    ><div></div
                    ><div id='magnify_2_7'></div
                ></div>

                <div align='center' alt=''  valign='middle' id='tile_3_7' 
                    class = "ocean1"
                    style='z-index:173;position:absolute; width:240px; height:120px; left:-480px; top:600px;'
                    ><div id='wonder_3_7' ></div
                    ><div id='tradegood_3_7' ></div
                    ><div id='cities_3_7'></div
                    ><div id='marking_3_7'></div
                    ><div></div
                    ><div id='magnify_3_7'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_4_7' 
                    class = "ocean1"
                    style='z-index:174;position:absolute; width:240px; height:120px; left:-360px; top:660px;'
                    ><div id='wonder_4_7' ></div
                    ><div id='tradegood_4_7' ></div
                    ><div id='cities_4_7'></div
                    ><div id='marking_4_7'></div
                    ><div></div
                    ><div id='magnify_4_7'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_5_7' 
                    class = "ocean1"
                    style='z-index:175;position:absolute; width:240px; height:120px; left:-240px; top:720px;'
                    ><div id='wonder_5_7' ></div
                    ><div id='tradegood_5_7' ></div
                    ><div id='cities_5_7'></div
                    ><div id='marking_5_7'></div
                    ><div></div
                    ><div id='magnify_5_7'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_6_7' 
                    class = "ocean1"
                    style='z-index:176;position:absolute; width:240px; height:120px; left:-120px; top:780px;'
                    ><div id='wonder_6_7' ></div
                    ><div id='tradegood_6_7' ></div
                    ><div id='cities_6_7'></div
                    ><div id='marking_6_7'></div
                    ><div></div
                    ><div id='magnify_6_7'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_7_7' 
                    class = "ocean1"
                    style='z-index:177;position:absolute; width:240px; height:120px; left:0px; top:840px;'
                    ><div id='wonder_7_7' ></div
                    ><div id='tradegood_7_7' ></div
                    ><div id='cities_7_7'></div
                    ><div id='marking_7_7'></div
                    ><div></div
                    ><div id='magnify_7_7'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_8_7' 
                    class = "ocean1"
                    style='z-index:178;position:absolute; width:240px; height:120px; left:120px; top:900px;'
                    ><div id='wonder_8_7' ></div
                    ><div id='tradegood_8_7' ></div
                    ><div id='cities_8_7'></div
                    ><div id='marking_8_7'></div
                    ><div></div
                    ><div id='magnify_8_7'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_9_7' 
                    class = "ocean1"
                    style='z-index:179;position:absolute; width:240px; height:120px; left:240px; top:960px;'
                    ><div id='wonder_9_7' ></div
                    ><div id='tradegood_9_7' ></div
                    ><div id='cities_9_7'></div
                    ><div id='marking_9_7'></div
                    ><div></div
                    ><div id='magnify_9_7'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_0_8' 
                    class = "ocean1"
                    style='z-index:180;position:absolute; width:240px; height:120px; left:-960px; top:480px;'
                    ><div id='wonder_0_8' ></div
                    ><div id='tradegood_0_8' ></div
                    ><div id='cities_0_8'></div
                    ><div id='marking_0_8'></div
                    ><div></div
                    ><div id='magnify_0_8'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_1_8' 
                    class = "ocean1"
                    style='z-index:181;position:absolute; width:240px; height:120px; left:-840px; top:540px;'
                    ><div id='wonder_1_8' ></div
                    ><div id='tradegood_1_8' ></div
                    ><div id='cities_1_8'></div
                    ><div id='marking_1_8'></div
                    ><div></div
                    ><div id='magnify_1_8'></div
                ></div>

                <div align='center' alt=''  valign='middle' id='tile_2_8' 
                    class = "ocean1"
                    style='z-index:182;position:absolute; width:240px; height:120px; left:-720px; top:600px;'
                    ><div id='wonder_2_8' ></div
                    ><div id='tradegood_2_8' ></div
                    ><div id='cities_2_8'></div
                    ><div id='marking_2_8'></div
                    ><div></div
                    ><div id='magnify_2_8'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_3_8' 
                    class = "ocean1"
                    style='z-index:183;position:absolute; width:240px; height:120px; left:-600px; top:660px;'
                    ><div id='wonder_3_8' ></div
                    ><div id='tradegood_3_8' ></div
                    ><div id='cities_3_8'></div
                    ><div id='marking_3_8'></div
                    ><div></div
                    ><div id='magnify_3_8'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_4_8' 
                    class = "ocean1"
                    style='z-index:184;position:absolute; width:240px; height:120px; left:-480px; top:720px;'
                    ><div id='wonder_4_8' ></div
                    ><div id='tradegood_4_8' ></div
                    ><div id='cities_4_8'></div
                    ><div id='marking_4_8'></div
                    ><div></div
                    ><div id='magnify_4_8'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_5_8' 
                    class = "ocean1"
                    style='z-index:185;position:absolute; width:240px; height:120px; left:-360px; top:780px;'
                    ><div id='wonder_5_8' ></div
                    ><div id='tradegood_5_8' ></div
                    ><div id='cities_5_8'></div
                    ><div id='marking_5_8'></div
                    ><div></div
                    ><div id='magnify_5_8'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_6_8' 
                    class = "ocean1"
                    style='z-index:186;position:absolute; width:240px; height:120px; left:-240px; top:840px;'
                    ><div id='wonder_6_8' ></div
                    ><div id='tradegood_6_8' ></div
                    ><div id='cities_6_8'></div
                    ><div id='marking_6_8'></div
                    ><div></div
                    ><div id='magnify_6_8'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_7_8' 
                    class = "ocean1"
                    style='z-index:187;position:absolute; width:240px; height:120px; left:-120px; top:900px;'
                    ><div id='wonder_7_8' ></div
                    ><div id='tradegood_7_8' ></div
                    ><div id='cities_7_8'></div
                    ><div id='marking_7_8'></div
                    ><div></div
                    ><div id='magnify_7_8'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_8_8' 
                    class = "ocean1"
                    style='z-index:188;position:absolute; width:240px; height:120px; left:0px; top:960px;'
                    ><div id='wonder_8_8' ></div
                    ><div id='tradegood_8_8' ></div
                    ><div id='cities_8_8'></div
                    ><div id='marking_8_8'></div
                    ><div></div
                    ><div id='magnify_8_8'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_9_8' 
                    class = "ocean1"
                    style='z-index:189;position:absolute; width:240px; height:120px; left:120px; top:1020px;'
                    ><div id='wonder_9_8' ></div
                    ><div id='tradegood_9_8' ></div
                    ><div id='cities_9_8'></div
                    ><div id='marking_9_8'></div
                    ><div></div
                    ><div id='magnify_9_8'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_0_9' 
                    class = "ocean1"
                    style='z-index:190;position:absolute; width:240px; height:120px; left:-1080px; top:540px;'
                    ><div id='wonder_0_9' ></div
                    ><div id='tradegood_0_9' ></div
                    ><div id='cities_0_9'></div
                    ><div id='marking_0_9'></div
                    ><div></div
                    ><div id='magnify_0_9'></div
                ></div>

                <div align='center' alt=''  valign='middle' id='tile_1_9' 
                    class = "ocean1"
                    style='z-index:191;position:absolute; width:240px; height:120px; left:-960px; top:600px;'
                    ><div id='wonder_1_9' ></div
                    ><div id='tradegood_1_9' ></div
                    ><div id='cities_1_9'></div
                    ><div id='marking_1_9'></div
                    ><div></div
                    ><div id='magnify_1_9'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_2_9' 
                    class = "ocean1"
                    style='z-index:192;position:absolute; width:240px; height:120px; left:-840px; top:660px;'
                    ><div id='wonder_2_9' ></div
                    ><div id='tradegood_2_9' ></div
                    ><div id='cities_2_9'></div
                    ><div id='marking_2_9'></div
                    ><div></div
                    ><div id='magnify_2_9'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_3_9' 
                    class = "ocean1"
                    style='z-index:193;position:absolute; width:240px; height:120px; left:-720px; top:720px;'
                    ><div id='wonder_3_9' ></div
                    ><div id='tradegood_3_9' ></div
                    ><div id='cities_3_9'></div
                    ><div id='marking_3_9'></div
                    ><div></div
                    ><div id='magnify_3_9'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_4_9' 
                    class = "ocean1"
                    style='z-index:194;position:absolute; width:240px; height:120px; left:-600px; top:780px;'
                    ><div id='wonder_4_9' ></div
                    ><div id='tradegood_4_9' ></div
                    ><div id='cities_4_9'></div
                    ><div id='marking_4_9'></div
                    ><div></div
                    ><div id='magnify_4_9'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_5_9' 
                    class = "ocean1"
                    style='z-index:195;position:absolute; width:240px; height:120px; left:-480px; top:840px;'
                    ><div id='wonder_5_9' ></div
                    ><div id='tradegood_5_9' ></div
                    ><div id='cities_5_9'></div
                    ><div id='marking_5_9'></div
                    ><div></div
                    ><div id='magnify_5_9'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_6_9' 
                    class = "ocean1"
                    style='z-index:196;position:absolute; width:240px; height:120px; left:-360px; top:900px;'
                    ><div id='wonder_6_9' ></div
                    ><div id='tradegood_6_9' ></div
                    ><div id='cities_6_9'></div
                    ><div id='marking_6_9'></div
                    ><div></div
                    ><div id='magnify_6_9'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_7_9' 
                    class = "ocean1"
                    style='z-index:197;position:absolute; width:240px; height:120px; left:-240px; top:960px;'
                    ><div id='wonder_7_9' ></div
                    ><div id='tradegood_7_9' ></div
                    ><div id='cities_7_9'></div
                    ><div id='marking_7_9'></div
                    ><div></div
                    ><div id='magnify_7_9'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_8_9' 
                    class = "ocean1"
                    style='z-index:198;position:absolute; width:240px; height:120px; left:-120px; top:1020px;'
                    ><div id='wonder_8_9' ></div
                    ><div id='tradegood_8_9' ></div
                    ><div id='cities_8_9'></div
                    ><div id='marking_8_9'></div
                    ><div></div
                    ><div id='magnify_8_9'></div
                ></div>
                <div align='center' alt=''  valign='middle' id='tile_9_9' 
                    class = "ocean1"
                    style='z-index:199;position:absolute; width:240px; height:120px; left:0px; top:1080px;'
                    ><div id='wonder_9_9' ></div
                    ><div id='tradegood_9_9' ></div
                    ><div id='cities_9_9'></div
                    ><div id='marking_9_9'></div
                    ><div></div
                    ><div id='magnify_9_9'></div
                ></div>

            
            </div>
            <div class="linkMapContainer" style="z-index:10000;position:absolute;">
                <div id="linkMap" style="position:absolute;z-index:10000;"><div id="link_tile_0_0" style="z-index:10000;position:absolute;left:0px;top:0px;"></div><div id="link_tile_1_0" style="z-index:10000;position:absolute;left:120px;top:60px;"></div><div id="link_tile_2_0" style="z-index:10000;position:absolute;left:240px;top:120px;"></div><div id="link_tile_3_0" style="z-index:10000;position:absolute;left:360px;top:180px;"></div><div id="link_tile_4_0" style="z-index:10000;position:absolute;left:480px;top:240px;"></div><div id="link_tile_5_0" style="z-index:10000;position:absolute;left:600px;top:300px;"></div><div id="link_tile_6_0" style="z-index:10000;position:absolute;left:720px;top:360px;"></div><div id="link_tile_7_0" style="z-index:10000;position:absolute;left:840px;top:420px;"></div><div id="link_tile_8_0" style="z-index:10000;position:absolute;left:960px;top:480px;"></div><div id="link_tile_9_0" style="z-index:10000;position:absolute;left:1080px;top:540px;"></div><div id="link_tile_0_1" style="z-index:10000;position:absolute;left:-120px;top:60px;"></div><div id="link_tile_1_1" style="z-index:10000;position:absolute;left:0px;top:120px;"></div><div id="link_tile_2_1" style="z-index:10000;position:absolute;left:120px;top:180px;"></div><div id="link_tile_3_1" style="z-index:10000;position:absolute;left:240px;top:240px;"></div><div id="link_tile_4_1" style="z-index:10000;position:absolute;left:360px;top:300px;"></div><div id="link_tile_5_1" style="z-index:10000;position:absolute;left:480px;top:360px;"></div><div id="link_tile_6_1" style="z-index:10000;position:absolute;left:600px;top:420px;"></div><div id="link_tile_7_1" style="z-index:10000;position:absolute;left:720px;top:480px;"></div><div id="link_tile_8_1" style="z-index:10000;position:absolute;left:840px;top:540px;"></div><div id="link_tile_9_1" style="z-index:10000;position:absolute;left:960px;top:600px;"></div><div id="link_tile_0_2" style="z-index:10000;position:absolute;left:-240px;top:120px;"></div><div id="link_tile_1_2" style="z-index:10000;position:absolute;left:-120px;top:180px;"></div><div id="link_tile_2_2" style="z-index:10000;position:absolute;left:0px;top:240px;"></div><div id="link_tile_3_2" style="z-index:10000;position:absolute;left:120px;top:300px;"></div><div id="link_tile_4_2" style="z-index:10000;position:absolute;left:240px;top:360px;"></div><div id="link_tile_5_2" style="z-index:10000;position:absolute;left:360px;top:420px;"></div><div id="link_tile_6_2" style="z-index:10000;position:absolute;left:480px;top:480px;"></div><div id="link_tile_7_2" style="z-index:10000;position:absolute;left:600px;top:540px;"></div><div id="link_tile_8_2" style="z-index:10000;position:absolute;left:720px;top:600px;"></div><div id="link_tile_9_2" style="z-index:10000;position:absolute;left:840px;top:660px;"></div><div id="link_tile_0_3" style="z-index:10000;position:absolute;left:-360px;top:180px;"></div><div id="link_tile_1_3" style="z-index:10000;position:absolute;left:-240px;top:240px;"></div><div id="link_tile_2_3" style="z-index:10000;position:absolute;left:-120px;top:300px;"></div><div id="link_tile_3_3" style="z-index:10000;position:absolute;left:0px;top:360px;"></div><div id="link_tile_4_3" style="z-index:10000;position:absolute;left:120px;top:420px;"></div><div id="link_tile_5_3" style="z-index:10000;position:absolute;left:240px;top:480px;"></div><div id="link_tile_6_3" style="z-index:10000;position:absolute;left:360px;top:540px;"></div><div id="link_tile_7_3" style="z-index:10000;position:absolute;left:480px;top:600px;"></div><div id="link_tile_8_3" style="z-index:10000;position:absolute;left:600px;top:660px;"></div><div id="link_tile_9_3" style="z-index:10000;position:absolute;left:720px;top:720px;"></div><div id="link_tile_0_4" style="z-index:10000;position:absolute;left:-480px;top:240px;"></div><div id="link_tile_1_4" style="z-index:10000;position:absolute;left:-360px;top:300px;"></div><div id="link_tile_2_4" style="z-index:10000;position:absolute;left:-240px;top:360px;"></div><div id="link_tile_3_4" style="z-index:10000;position:absolute;left:-120px;top:420px;"></div><div id="link_tile_4_4" style="z-index:10000;position:absolute;left:0px;top:480px;"></div><div id="link_tile_5_4" style="z-index:10000;position:absolute;left:120px;top:540px;"></div><div id="link_tile_6_4" style="z-index:10000;position:absolute;left:240px;top:600px;"></div><div id="link_tile_7_4" style="z-index:10000;position:absolute;left:360px;top:660px;"></div><div id="link_tile_8_4" style="z-index:10000;position:absolute;left:480px;top:720px;"></div><div id="link_tile_9_4" style="z-index:10000;position:absolute;left:600px;top:780px;"></div><div id="link_tile_0_5" style="z-index:10000;position:absolute;left:-600px;top:300px;"></div><div id="link_tile_1_5" style="z-index:10000;position:absolute;left:-480px;top:360px;"></div><div id="link_tile_2_5" style="z-index:10000;position:absolute;left:-360px;top:420px;"></div><div id="link_tile_3_5" style="z-index:10000;position:absolute;left:-240px;top:480px;"></div><div id="link_tile_4_5" style="z-index:10000;position:absolute;left:-120px;top:540px;"></div><div id="link_tile_5_5" style="z-index:10000;position:absolute;left:0px;top:600px;"></div><div id="link_tile_6_5" style="z-index:10000;position:absolute;left:120px;top:660px;"></div><div id="link_tile_7_5" style="z-index:10000;position:absolute;left:240px;top:720px;"></div><div id="link_tile_8_5" style="z-index:10000;position:absolute;left:360px;top:780px;"></div><div id="link_tile_9_5" style="z-index:10000;position:absolute;left:480px;top:840px;"></div><div id="link_tile_0_6" style="z-index:10000;position:absolute;left:-720px;top:360px;"></div><div id="link_tile_1_6" style="z-index:10000;position:absolute;left:-600px;top:420px;"></div><div id="link_tile_2_6" style="z-index:10000;position:absolute;left:-480px;top:480px;"></div><div id="link_tile_3_6" style="z-index:10000;position:absolute;left:-360px;top:540px;"></div><div id="link_tile_4_6" style="z-index:10000;position:absolute;left:-240px;top:600px;"></div><div id="link_tile_5_6" style="z-index:10000;position:absolute;left:-120px;top:660px;"></div><div id="link_tile_6_6" style="z-index:10000;position:absolute;left:0px;top:720px;"></div><div id="link_tile_7_6" style="z-index:10000;position:absolute;left:120px;top:780px;"></div><div id="link_tile_8_6" style="z-index:10000;position:absolute;left:240px;top:840px;"></div><div id="link_tile_9_6" style="z-index:10000;position:absolute;left:360px;top:900px;"></div><div id="link_tile_0_7" style="z-index:10000;position:absolute;left:-840px;top:420px;"></div><div id="link_tile_1_7" style="z-index:10000;position:absolute;left:-720px;top:480px;"></div><div id="link_tile_2_7" style="z-index:10000;position:absolute;left:-600px;top:540px;"></div><div id="link_tile_3_7" style="z-index:10000;position:absolute;left:-480px;top:600px;"></div><div id="link_tile_4_7" style="z-index:10000;position:absolute;left:-360px;top:660px;"></div><div id="link_tile_5_7" style="z-index:10000;position:absolute;left:-240px;top:720px;"></div><div id="link_tile_6_7" style="z-index:10000;position:absolute;left:-120px;top:780px;"></div><div id="link_tile_7_7" style="z-index:10000;position:absolute;left:0px;top:840px;"></div><div id="link_tile_8_7" style="z-index:10000;position:absolute;left:120px;top:900px;"></div><div id="link_tile_9_7" style="z-index:10000;position:absolute;left:240px;top:960px;"></div><div id="link_tile_0_8" style="z-index:10000;position:absolute;left:-960px;top:480px;"></div><div id="link_tile_1_8" style="z-index:10000;position:absolute;left:-840px;top:540px;"></div><div id="link_tile_2_8" style="z-index:10000;position:absolute;left:-720px;top:600px;"></div><div id="link_tile_3_8" style="z-index:10000;position:absolute;left:-600px;top:660px;"></div><div id="link_tile_4_8" style="z-index:10000;position:absolute;left:-480px;top:720px;"></div><div id="link_tile_5_8" style="z-index:10000;position:absolute;left:-360px;top:780px;"></div><div id="link_tile_6_8" style="z-index:10000;position:absolute;left:-240px;top:840px;"></div><div id="link_tile_7_8" style="z-index:10000;position:absolute;left:-120px;top:900px;"></div><div id="link_tile_8_8" style="z-index:10000;position:absolute;left:0px;top:960px;"></div><div id="link_tile_9_8" style="z-index:10000;position:absolute;left:120px;top:1020px;"></div><div id="link_tile_0_9" style="z-index:10000;position:absolute;left:-1080px;top:540px;"></div><div id="link_tile_1_9" style="z-index:10000;position:absolute;left:-960px;top:600px;"></div><div id="link_tile_2_9" style="z-index:10000;position:absolute;left:-840px;top:660px;"></div><div id="link_tile_3_9" style="z-index:10000;position:absolute;left:-720px;top:720px;"></div><div id="link_tile_4_9" style="z-index:10000;position:absolute;left:-600px;top:780px;"></div><div id="link_tile_5_9" style="z-index:10000;position:absolute;left:-480px;top:840px;"></div><div id="link_tile_6_9" style="z-index:10000;position:absolute;left:-360px;top:900px;"></div><div id="link_tile_7_9" style="z-index:10000;position:absolute;left:-240px;top:960px;"></div><div id="link_tile_8_9" style="z-index:10000;position:absolute;left:-120px;top:1020px;"></div><div id="link_tile_9_9" style="z-index:10000;position:absolute;left:0px;top:1080px;"></div></div>
            </div>
            <div id="dragHandlerOverlay" style="cursor:move;position:absolute;z-index:1000;margin-left:-300px;margin-top:240px;width:800px;height:600px;background-image:url(/img/img/blank.gif);">
            </div>
            
                                

        </div>    
    </div>
</div><!-- END mainview -->

<script>
{literal}
// init

    
var map = new Map({/literal}{$start_x}, {$start_y}{literal});
map.handleMapData('{/literal}{$json}{literal}');
//map.initCoords();
map.drawMap();
//map.setPosition();
 
//map.markIsland(75, 51);

//var map = new Map(50, 57);
{/literal}
</script>
