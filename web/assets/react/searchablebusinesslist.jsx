/**<script type="text/jsx">
    var opts1 = [{val:"opt1",name:"OPT1"},{val:"opt2",name:"OPT3"},{val:"opt3",name:"OPT3"}];
var opts2 = [{val:"tt1",name:"TT1"},{val:"tt2",name:"TT2"},{val:"tt3",name:"TT3"}];
var srch  = [{"name":"NCC","lat":"41.103010","lng":"-73.451814"},{"name":"Magrath Park","lat":"41.103617","lng":"-73.450247"},{"name":"St.Mathew's Church","lat":"41.110606","lng":"-73.448215"}];
React.render(<SearchableBusinessList opts1={opts1} opts2={opts2} srch={srch} />, document.body);
</script>**/
var SearchableBusinessList = React.createClass({
    getInitialState: function() {
        return {
            day          : '',
            time         : '',
            location     : '',
            treatment    : '',
            treatmentType: '',
            tx_opts      :[],
            txtype_opts  :[],
            businesses   :[],
            map_results  :[]
        };
    },
    componentDidMount: function() {
      this.setState({
         tx_opts : opts1,
         txtype_opts: opts2,
         map_results: srch
       });
   },
   handleUserInput: function(dayInput, timeInput, locationInput, treatmentInput,treatmentTypeInput) {
       var data ={
         day          : dayInput,
         time         : timeInput,
         location     : locationInput,
         treatment    : treatmentInput,
         treatmentType: treatmentTypeInput
       };
       var results = this.runSearch(data);

       data.businesses = results.text;
       data.map_results = results.map;
       this.setState(data);
   },
   runSearch:function(data){
     /** TODO: PROCESS INPUTS AND RUN SEARCH **/
     return {
            text:[
                {name:'Salon1', address:{city:'norwalk',state:'CT'},treatments:[{name:'waxing',percent_discount:'8',start_dollars:'50'}],description:'Some Fancy Salon',logo:'https://facebook.github.io/react/img/logo.svg'},
                {name:'Salon2', address:{city:'STAMFORD',state:'ct'},treatments:[{name:'waxing',percent_discount:'5',start_dollars:'20'}],description:'Some OTHER Fancy Salon',logo:'https://facebook.github.io/react/img/logo.svg'}
              ],
            map:'[{"name":"Darinor Shopping Plaza","lat":"41.098768","lng":" -73.444656"},{"name":"Post Road Diner","lat":"41.102385","lng":"-73.437096"},{"name":"Silver Star Diner","lat":"41.104152","lng":"-73.432964"}]'
     };
   },
    render: function() {
        return (
          <div className="componentBody">
            <section id="top-map" className="nav-pad">
              <ResultMap markers={this.state.map_results} />
            </section>
            <section id="below-map" className="nav-pad">
              <div className="container">
                <div className="col-lg-4">
                  <SearchForm
                      day           = {this.state.day}
                      time          = {this.state.time}
                      location      = {this.state.location}
                      treatment     = {this.state.treatment}
                      treatmentType = {this.state.treatmentType}
                      tx_opts       = {this.state.tx_opts}
                      txtype_opts   = {this.state.txtype_opts}
                      onUserInput   = {this.handleUserInput}
                  />
                </div>
                <BusinessesList businesses={this.state.businesses} />
              </div>
            </section>
          </div>
        );
    }
});