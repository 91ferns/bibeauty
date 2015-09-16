var ResultMap = React.createClass({
    getInitialState: function(){
      return {
        HELPER: this.props.map,
        firstLoad: true
      };
    },
    componentDidMount: function() {
        this.state.HELPER.init();
		    //Update skipped in render (first load) because map not initted
        //run update now that map is setup
        this.state.HELPER.updateResults(this.props.mapResults);
        this.setState({firstLoad:false});
    },
    render: function() {
      if(!this.state.firstLoad) this.state.HELPER.updateResults(this.props.mapResults);
      return (
        <div id="results_map"></div>
      );
    }
});
