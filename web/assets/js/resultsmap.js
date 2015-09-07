/** Usage:
    <div id="results_map"></div>
    <a id="update-results" href="#">Update results</a>
    <script>
    $(document).ready(function(){
        RESULTS_MAP.init();
        var marks = '[{"name":"NCC","lat":"41.103010","lng":"-73.451814"},{"name":"Magrath Park","lat":"41.103617","lng":"-73.450247"},{"name":"St.Mathew\'s Church","lat":"41.110606","lng":"-73.448215"}]';
        var marks2 = '[{"name":"Darinor Shopping Plaza","lat":"41.098768","lng":" -73.444656"},{"name":"Post Road Diner","lat":"41.102385","lng":"-73.437096"},{"name":"Silver Star Diner","lat":"41.104152","lng":"-73.432964"}]';
        RESULTS_MAP.updateResults(marks);
        $('#update-results').on('click',function(e){
            e.preventDefault();
            RESULTS_MAP.updateResults(marks2);
        });
    });
    </script>
*/
var RESULTS_MAP = function(){
  var gMap = google.maps;
    var themap          = {},
        currentMarkers  = [],
        center          = [41.110079,-73.421902],
        mapId           = 'results_map',
        currentCoords   = [],
        opts            = {
            zoom: 13,
            mapTypeId: gMap.MapTypeId.ROADMAP
        },
        init    = function(resultsmarkers){
           //to do make opts, center, mapId updatable in constructor
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
          newpos = (typeof newpos === 'undefined') ? getCurrentCenter() : newpos;
          themap.setOptions({center: newpos});
        },
        makeInfoWindow = function(data,marker){
            var iw = new gMap.InfoWindow({
                content: "<div class='loc-info'>" + data +"</div>",
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
      updateResults:updateResults
    };
};
