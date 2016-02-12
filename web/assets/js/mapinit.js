function initMap() {

  jQuery(function($) {

    function BusinessMap(element) {

      this.$ = element;
      this.markers = [];
      this.map = null;
      this.options = {
        zoom: 13,
        scrollwheel: false,
        navigationControl: false,
        center: new google.maps.LatLng(34.0500,-118.2500),
        MapTypeId: google.maps.MapTypeId.ROADMAP,
      };

      this.templates = {
        infoWindow: Handlebars.compile($("#info-window-template").html())
      };

      this.getSearchFormFields = function() {
        return {};
      };

      this.search = function(cb) {
        $.getJSON('/api/businesses/search', this.getSearchFormFields(), function(response) {
          if (response.status !== 'ok') {
            cb('Something went wrong with our system. Try refreshing');
          }

          cb(null, response.data);
        });
      };

      this.addMarker = function(marker) {
        var pos = new google.maps.LatLng(marker.coordinates.latitude, marker.coordinates.longitude);
        var loc = new google.maps.Marker({
          position: pos,
          title: marker.name,
          map: this.map,
          animation: google.maps.Animation.DROP,
          icon: '/assets/images/map/pin.png',
        });
        loc.meta = { id: marker.id };

        this.markers.push(loc);

        this.makeInfoWindow(marker, loc);
      };

      this.updateResults = function(markers) {
        console.log(markers);
        for (var x in markers) {
          var marker = markers[x];
          console.log(marker);
          this.addMarker(marker);
        }
      };

      this.map = new google.maps.Map(this.$, this.options);
      // this.requestLocation();

    }

    BusinessMap.prototype.requestLocation = function() {
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
          var lat = position.coords.latitude;
          var long = position.coords.longitude;
          var pos = new google.maps.LatLng(lat, long);

          this.options = $.extend(this.options, {
            center: pos
          });

        }.bind(this)); //.bind(this);
      }
    };

    BusinessMap.prototype.getCurrentCenter = function(){
        var bounds = new google.maps.LatLngBounds();
        for (var x in this.markers) {
          var marker = this.markers[x];
          bounds.extend(marker.position);
        }
        return bounds.getCenter();
    };

    BusinessMap.prototype.makeInfoWindow = function(data, marker){

      var iw = new google.maps.InfoWindow({
          content: this.templates.infoWindow(data),
          maxWidth: 200
      });
      marker.addListener('click', function() {
          iw.open(this.map, marker);
      }.bind(this));
    };

    BusinessMap.prototype.resetCenter = function(newpos){
      newpos = (!!newpos) ? newpos : false;
      if (newpos) {
        this.options.center = newpos;
        this.map.setOptions({center: newpos});
      }
    };

    BusinessMap.prototype.focusBusiness = function(id) {
      var markers = this.markers;
      var theMarker = null;
      for (var x in markers) {
        var marker = markers[x];

        if (marker.meta.id === id) {
          theMarker = marker;
        }
      }

      if (!theMarker) {
        return false;
      }

      google.maps.event.trigger( theMarker, 'click' );

      return theMarker;

    };

  	var wrapper = document.getElementById('results_map');
  	if (wrapper) {

      var map = new BusinessMap(wrapper);
      map.search(function(err, data) {
        this.updateResults(data);
      }.bind(map));

      $('.map-link').attr('href', '#').click(function() {
        var businessId = $(this).data('business-id');
        var exists = result.focusBusiness(businessId);
        if (!exists) {
          alert('Could not find the map window');
        }
        return false;
      });

  	} // wrapper
  });

}
