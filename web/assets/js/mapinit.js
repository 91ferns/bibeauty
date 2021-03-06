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
      this.iw = new google.maps.InfoWindow({
          maxWidth: 450
      });
      this.geocoder = new google.maps.Geocoder();

      this.templates = {
        infoWindow: Handlebars.compile($("#info-window-template").html())
      };

      this.getSearchFormFields = function() {
        var pairs = window.location.search.substring(1).split("&"),
          obj = {},
          pair,
          i;

        for ( i in pairs ) {
          if ( pairs[i] === "" ) continue;

          pair = pairs[i].split("=");
          obj[ decodeURIComponent( pair[0] ) ] = decodeURIComponent( pair[1] );
        }

        return obj;
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

        this.bindEvents(marker, loc);
      };

      this.updateResults = function(markers) {
        for (var x in markers) {
          var marker = markers[x];
          this.addMarker(marker);
        }
      };

      this.map = new google.maps.Map(this.$, this.options);
      this.requestLocation();

    }

    BusinessMap.prototype.requestLocation = function() {
      var searchFields = this.getSearchFormFields();

      if (!searchFields.location && navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
          var lat = position.coords.latitude;
          var long = position.coords.longitude;
          var pos = new google.maps.LatLng(lat, long);

          this.options = $.extend(this.options, {
            center: pos
          });

          this.map.setCenter(pos);
          this.addCenterIcon(pos);
          this.geocoder.geocode({'location': pos}, function(results, status) {
            if (status === google.maps.GeocoderStatus.OK) {
              if (results[0]) {
                var address = results[0].address_components;
                var postalObject = $.grep(address, function(n, i) {
                    if (n.types[0] == "postal_code") {
                      return n;
                    } else {
                      return null;
                    }
                });
                var zipcode = postalObject[0].long_name;
                $('#LocationField').val(zipcode);
                $('.search-form').submit();

              }
            } else {
              window.alert('Geocoder failed due to: ' + status);
            }
          });

        }.bind(this)); //.bind(this);
      } else if (searchFields.location) {
        this.geocoder.geocode( { 'address': searchFields.location}, function(results, status) {
          if (status == google.maps.GeocoderStatus.OK) {
            //Got result, center the map and put it out there
            this.map.setCenter(results[0].geometry.location);
            this.addCenterIcon(results[0].geometry.location);
          }
        }.bind(this));
      }
    };

    BusinessMap.prototype.addCenterIcon = function(pos) {
      var marker = new google.maps.Marker({
        position: pos,
        icon: {
          path: google.maps.SymbolPath.CIRCLE,
          scale: 4
        },
        draggable: false,
        map: this.map
      });
    };

    BusinessMap.prototype.getCurrentCenter = function(){
        var bounds = new google.maps.LatLngBounds();
        for (var x in this.markers) {
          var marker = this.markers[x];
          bounds.extend(marker.position);
        }
        return bounds.getCenter();
    };

    BusinessMap.prototype.bindEvents = function(data, marker){

      marker.addListener('click', function() {
        this.iw.setContent(this.templates.infoWindow(data));
        this.iw.open(this.map, marker);
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

      this.map.setCenter(theMarker.position);

      google.maps.event.trigger( theMarker, 'click' );

      return theMarker;

    };

  	var wrapper = document.getElementById('results_map');
  	if (wrapper) {

      var map = new BusinessMap(wrapper);
      map.search(function(err, data) {
        map.updateResults(data);
      });

      $('.map-link').attr('href', '#').click(function() {
        var businessId = $(this).data('business-id');
        var exists = map.focusBusiness(businessId);
        if (!exists) {
          alert('Could not find the map window');
        }
        return false;
      });

  	} // wrapper
  });

}
