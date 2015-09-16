function initMap() {
  // How do we send this to all the react components
  var THEMAP = RESULTS_MAP;
  THEMAP.setGoogle(google.maps); // Load google maps API into result map

  jQuery(function($) {

  	var wrapper = $('#results-map-wrapper');
  	if (wrapper) {
    	var opts1 = [{val:"opt1",name:"OPT1"},{val:"opt2",name:"OPT3"},{val:"opt3",name:"OPT3"}];
		  var opts2 = [{val:"tt1",name:"TT1"},{val:"tt2",name:"TT2"},{val:"tt3",name:"TT3"}];
		  var srch  = [{"name":"NCC","lat":"41.103010","lng":"-73.451814"},{"name":"Magrath Park","lat":"41.103617","lng":"-73.450247"},{"name":"St.Mathew's Church","lat":"41.110606","lng":"-73.448215"}];
    //console.log(opts1);
		React.render(
			React.createElement(SearchableBusinessList,
				{
					'opts1': opts1,
					'opts2': opts2,
				  'srch' : srch,
          'map'  : THEMAP
				}
			),
			document.getElementById('results-map-wrapper')
		);

  	}
  });

}
