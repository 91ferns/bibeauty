var ResultMap = React.createClass({
    getInitialState: function(){

      return {
        map: null,
        firstLoad: true,
        currentLocation: false,
        markers: [],
        options: {
          zoom: 13,
          scrollwheel: false,
          navigationControl: false,
          center: new google.maps.LatLng(41.110079,-73.421902),
          MapTypeId: google.maps.MapTypeId.ROADMAP,
        }
      };

    },//Map(document.getElementById(mapId), opts);
    componentDidMount: function() {
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {

          var lat = position.coords.latitude;
          var long = position.coords.longitude;
          var pos = new google.maps.LatLng(lat, long);

          this.setState({
            currentLocation: pos,
            options: $.extend(this.state.options, {
              center: pos
            })
          }); // Set state to new center
        }.bind(this));
      }
	    //Update skipped in render (first load) because map not initted
      //run update now that map is setup
        //this.state.HELPER.updateResults(this.props.mapResults);
      this.setMap();
    },
    setMap: function() {
      var map = new google.maps.Map(document.getElementById('results_map'), this.state.options);
      this.setState({
        map: map
      }, function() {
        this.updateResults(this.props.mapResults);
      }.bind(this));

    },
    checkParseMarkers: function(markers) {
      try{
        markers = JSON.parse(markers);
      } catch(e) {}
      return markers;
    },
    addMarkers: function(markers) {
      markers = this.checkParseMarkers(markers);
      for (var x in markers) {
        var marker = markers[x];
        this.addMarker(marker);
      }
    },
    addMarker: function(marker) {
      var pos = new google.maps.LatLng(marker.coordinates.latitude, marker.coordinates.longitude);
      var loc = new google.maps.Marker({
        position: pos,
        title: marker.name,
        map: this.state.map,
        animation: google.maps.Animation.DROP,
        icon: '/assets/images/map/pin.png',
      });
      this.setState({
        markers: this.state.markers.push(loc)
      });

      this.makeInfoWindow(marker, loc);
    },
    removeMarkers: function(){
      for (var x in this.state.markers) {
        var marker = this.state.markers[x];
        this.addMarker(marker);
      }
      this.setState({
        markers: [],
      });
    },
    updateResults: function(newMarkers){
        this.removeMarkers();
        this.addMarkers(newMarkers);
        this.resetCenter();
    },
    makeInfoWindow: function(data, marker){
      console.log(data);
        var iw = new google.maps.InfoWindow({
            content: '<div class="loc-info">'+data.name+'</div>',
            maxWidth: 200
        });
        marker.addListener('click', function() {
            iw.open(this.state.map, marker);
        }.bind(this));
    },
    getCurrentCenter: function(){
        var bounds = new google.maps.LatLngBounds();
        for (var x in this.state.markers) {
          var marker = this.state.markers[x];
          bounds.extend(marker.position);
        }
        return bounds.getCenter();
    },
    resetCenter: function(newpos){
      newpos = (!!newpos) ? newpos : this.getCurrentCenter();
      this.state.options.center = newpos;
      this.state.map.setOptions({center: newpos});
    },
    render: function() {
      var warning;
      if (this.state.currentLocation === false) {
        warning = <div className="alert alert-warning top-warning">
          <div className="container">
            <center><strong>We could not get access to your location.</strong></center>
          </div>
        </div>
      }
      return (
        <div className="results_map_wrapper">
          {warning}
          <div className="results-map-wrapper-outer">
            <div id="results_map"></div>
          </div>
        </div>
      );
    }
});
