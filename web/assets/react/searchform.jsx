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