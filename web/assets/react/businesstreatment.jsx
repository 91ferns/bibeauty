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