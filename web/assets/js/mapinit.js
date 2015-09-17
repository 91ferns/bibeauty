function initMap() {
  // How do we send this to all the react components
  jQuery(function($) {

  	var wrapper = document.getElementById('results-map-wrapper');
  	if (wrapper) {

      $.getJSON('/api/businesses', function(response) {
        if (response.status !== 'ok') {
          alert('Something went wrong with our system. Try refreshing');
          return;
        }

        React.render(
    			React.createElement(ResultMap,
    				{
    				  mapResults : response.data,
    				}
    			),
    			wrapper
    		);

      });

  	} // wrapper
  });

}
