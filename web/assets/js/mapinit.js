function initMap() {
  // How do we send this to all the react components
  jQuery(function($) {

  	var wrapper = document.getElementById('results-map-wrapper');
  	if (wrapper) {

      var result = React.render(
  			React.createElement(ResultMap),
  			wrapper
  		);

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
