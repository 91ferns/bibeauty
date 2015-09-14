var ResultMap = React.createClass({
    getInitialState: function(){
      return {
        HELPER: RESULTS_MAP
      }
    },
    componentDidMount: function() {
        //var marks = '[{"name":"NCC","lat":"41.103010","lng":"-73.451814"},{"name":"Magrath Park","lat":"41.103617","lng":"-73.450247"},{"name":"St.Mathew\'s Church","lat":"41.110606","lng":"-73.448215"}]';

        //var marks2 = '[{"name":"Darinor Shopping Plaza","lat":"41.098768","lng":" -73.444656"},{"name":"Post Road Diner","lat":"41.102385","lng":"-73.437096"},{"name":"Silver Star Diner","lat":"41.104152","lng":"-73.432964"}]';
        // initing
        this.state.HELPER.init();
        this.state.HELPER.updateResults(this.props.markers);
    },
    render: function() {
      return (
        <div id="results_map"></div>
      );
    }
});