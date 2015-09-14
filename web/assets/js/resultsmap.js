var RESULTS_MAP = (function(){
    var themap          = {},
        gMap            = {},
        currentMarkers  = [],
        center          = [41.110079,-73.421902],
        mapId           = 'results_map',
        currentCoords   = [],
        opts            = {
            zoom: 13

        },
        setGoogle = function(g) {
          gMap = g;
          gMap.MapTypeId.ROADMAP;
        }
        init    = function(g,resultsmarkers){
           //to do make opts, center, mapId updatable in constructor
           console.log('Initing');
           console.trace();
           initCenter();
           createTheMap();
           if(typeof resultsmarkers !== "undefined"){
                addMarkers(resultsmarkers);
           }
        },
        createTheMap = function(){
           themap = new gMap.Map(document.getElementById(mapId), opts);
        },
        updateResults = function(newMarkers){
            removeCurrentMarkers();
            addMarkers(newMarkers);
            resetCenter();
        },
        addMarkers = function(markers){
            checkParseMarkers(markers);
            markers = checkParseMarkers(markers);
            markers.forEach(function(marker){
              addMarker(marker);
            });
        },
        addMarker = function(marker){
            var pos = new gMap.LatLng(marker.lat,marker.lng);
            currentCoords.push(pos);
            var loc = new gMap.Marker({
                        position: pos,
                        title:marker.name,
                        map:themap
                      });
            currentMarkers.push(loc);
            makeInfoWindow(marker.name,loc);
        },
        removeCurrentMarkers = function(){
            currentMarkers.forEach(function(marker){
               marker.setMap(null);
            });
            currentMarkers = [];
            currentCoords  = [];
        },
        getCurrentCenter = function(){
            var bounds = new gMap.LatLngBounds();
            for (var i = 0; i < currentCoords.length; i++) {
                bounds.extend(currentCoords[i]);
            }
            return bounds.getCenter();
        },
        resetCenter = function(newpos){
          console.trace();
          newpos = (typeof newpos === 'undefined') ? getCurrentCenter() : newpos;
          themap.setOptions({center: newpos});
        },
        makeInfoWindow = function(data,marker){
            var iw = new gMap.InfoWindow({
                content: "<div className='loc-info'>" + data +"</div>",
                maxWidth:200
            });
            marker.addListener('click', function() {
                iw.open(themap, marker);
            });
        },
        initCenter = function(){
          center =  new gMap.LatLng(center[0],center[1]);
          opts.center = center;
        },
        checkParseMarkers = function(markers){
          try{
            markers = JSON.parse(markers);
          }catch(e){}
          return markers;
        };
    return {
      init:init,
      setGoogle: setGoogle,
      updateResults:updateResults
    };
}());
