function initMap() {
  // How do we send this to all the react components
  var THEMAP = RESULTS_MAP;
  THEMAP.setGoogle(google.maps); // Load google maps API into result map

  jQuery(function($) {

  	var wrapper = $('#results-map-wrapper');
  	if (wrapper) {
    	var opts1 = [{val:"opt1",name:"OPT1"},{val:"opt2",name:"OPT3"},{val:"opt3",name:"OPT3"}];
		  var opts2 = [{val:"tt1",name:"TT1"},{val:"tt2",name:"TT2"},{val:"tt3",name:"TT3"}];
      $.getJSON('/api/businesses', function(response) {
        if (response.status !== 'ok') {
          alert('Something went wrong with our system. Try refreshing');
          return;
        }

        React.render(
    			React.createElement(SearchableBusinessList,
    				{
    					'opts1': opts1,
    					'opts2': opts2,
    				  'srch' : response.data,
    				}
    			),
    			document.getElementById('results-map-wrapper')
    		);

      });

  	}
  });

}
