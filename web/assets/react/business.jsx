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