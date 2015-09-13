/**<script type="text/jsx">
    var opts1 = [{val:"opt1",name:"OPT1"},{val:"opt2",name:"OPT3"},{val:"opt3",name:"OPT3"}];
var opts2 = [{val:"tt1",name:"TT1"},{val:"tt2",name:"TT2"},{val:"tt3",name:"TT3"}];
var srch  = [{"name":"NCC","lat":"41.103010","lng":"-73.451814"},{"name":"Magrath Park","lat":"41.103617","lng":"-73.450247"},{"name":"St.Mathew's Church","lat":"41.110606","lng":"-73.448215"}];
React.render(<SearchableBusinessList opts1={opts1} opts2={opts2} srch={srch} />, document.body);
</script>**/
var SearchForm = React.createClass({
    handleChange: function() {
          this.props.onUserInput(
              this.refs.dayInput.getDOMNode().value,
              this.refs.timeInput.getDOMNode().value,
              this.refs.locationInput.getDOMNode().value,
              this.refs.treatmentInput.getDOMNode().value,
              this.refs.treatmentTypeInput.getDOMNode().value
          );
    },
    render: function() {
      var opts1 = this.props.tx_opts.map(function (opt) {
         return (<option value={opt.val}>{opt.name} </option>);
       });
       var opts2 = this.props.txtype_opts.map(function (opt) {
          return (<option value={opt.val}>{opt.name} </option>);
        });
        return (
          <form>
            <div className="form-group">
              <h4>Availability</h4>
              <div className="input-group">
                <input ref="dayInput" onChange={this.handleChange} type="text" id="day" name="day" className="form-control" placeholder="Day" value ={this.props.day}/>
                <span className="input-group-addon" id="basic-addon1"><i className="fa fa-chevron-circle-down fa-lg"></i></span>
              </div>
              <div className="input-group">
                <input ref="timeInput" onChange={this.handleChange} type="text" id="time" name="time" className="form-control" placeholder="Time" value ={this.props.time}/>
                <span className="input-group-addon" id="basic-addon1"><i className="fa fa-chevron-circle-down fa-lg"></i></span>
              </div>
            </div>
            <hr/>
            <div className="form-group">
              <h4>Location</h4>
              <input ref="locationInput" onChange={this.handleChange} type="text" id="location" name="location" className="form-control" placeholder="Enter Area or postcode" value ={this.props.location}/>
            </div>
            <hr/>
            <div className="form-group">
              <h4>Treatment Type / Category</h4>
              <div className="dropdown-group">
                <select ref="treatmentTypeInput" onChange={this.handleChange} id="treatmentType" name="treatmentType" className="form-control" value={this.props.treatmentType}>
                  <option>Treatment Type</option>
                  {opts2}
                </select>
              </div>
              <div className="dropdown-group">
                <select ref="treatmentInput" onChange={this.handleChange} id="treatment" name="treatment" className="form-control" value={this.props.treatment}>
                  <option>Treatment</option>
                  {opts1}
                </select>
              </div>
            </div>
            <hr/>
            <div className="form-group">
              <h4>Price Range</h4>
              <div className="range">
    	           <input type="text" id="amount_left" readOnly styles="" />
                 <input type="text" id="amount_right" readOnly styles="" />
                 <div id="slider-range"></div>
              </div>
            </div>
            <hr/>
            <p className="notify-btn" ><a href="#">SEARCH</a></p>
        </form>
        );
    }
});

var Business = React.createClass({
    render: function() {
        return (
          <div className="result">
            <div className="row location">
              <div className="col-md-9">
                <h3 className="business-name">{this.props.info.name}</h3>
                <div className="reviews">
                  <div className="reviews-number col-md-3">
                    5 Reviews
                  </div>
                  <a className="col-md-9" href="#">
                    <i className="glyphicon glyphicon-map-marker"></i>Show on Map
                  </a>
                  <div className="location">
                    {this.props.info.address.city},{this.props.info.address.state}
                  </div>
                  <div className="description>">
                    {this.props.info.description}
                  </div>
                </div>
              </div>
              <div className="col-md-3">
                <img className="business-logo" src={this.props.info.logo}/>
              </div>
            </div>
            <div className="row treatments">
                  <BusinessTreatmentsList treatments={this.props.info.treatments} />
            </div>
          </div>
        );
    }
});
var BusinessTreatmentsList = React.createClass({
    render: function() {
      var rows = [];
      var btn  = <div className="notify-btn {cls}"><a href="#">Show  More</a></div>
      if(this.props.treatments){
        this.props.treatments.forEach(function(treatment,i){
          var cls = '';
          if(i<=3){
            cls = 'hidden';
            btn="";
          }
          rows.push(<BusinessTreatment cls={cls} treatment={treatment} />);
        });
      }else{
        return <div className="alert alert-danger">This Provider has no treatments yet</div>
      }
      return (
        <span>
          <ul className="col-md-12 list-group">
            {rows}
          </ul>
          {btn}
        </span>
      );
    }
});
var BusinessTreatment = React.createClass({
    render: function() {
      return (
        <li className="list-group-item row {this.props.cls}">
          <a href="treatment/detail/{this.props.treatment.id}">
            <div className="col-md-8 treatment-name">
                  {this.props.treatment.name}
            </div>
            <div className="col-md-4">
              <div className="col-md-6 savings">
                    Save <span className="blue">{this.props.treatment.percent_discount}%</span>
              </div>
              <div className="notify-btn">
                  <a href="#">From ${this.props.treatment.start_dollars}</a>
              </div>
            </div>
            </a>
        </li>
      );
    }
});

var BusinessesList = React.createClass({
    render: function() {
        var rows = [];
        if(this.props.businesses){
          this.props.businesses.forEach(function(business) {
              rows.push(<Business info={business} />);
          }.bind(this));
        }else{
          row.push(<div className='alert alert-danger'>No Results.</div>);
        }
        return (
            <div id="search_results" className="col-md-8">
              {rows}
            </div>
        );
    }
});
var ResultMap = React.createClass({
    getInitialState: function(){
      return {
        HELPER:RESULTS_MAP
      }
    },
    componentDidMount: function() {
        this.state.HELPER.init(google.maps);
        //var marks = '[{"name":"NCC","lat":"41.103010","lng":"-73.451814"},{"name":"Magrath Park","lat":"41.103617","lng":"-73.450247"},{"name":"St.Mathew\'s Church","lat":"41.110606","lng":"-73.448215"}]';

        //var marks2 = '[{"name":"Darinor Shopping Plaza","lat":"41.098768","lng":" -73.444656"},{"name":"Post Road Diner","lat":"41.102385","lng":"-73.437096"},{"name":"Silver Star Diner","lat":"41.104152","lng":"-73.432964"}]';
    },
    render: function() {
               this.state.HELPER.updateResults(this.props.markers);
       return (
            <div  id="results_map"></div>
        );
    }
});
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