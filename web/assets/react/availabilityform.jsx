var AvailabilityForm = React.createClass({
  componentDidMount: function() {
    var treatment = this.props.treatment;
    var business = this.props.business;

    jQuery.getJSON('/api/businesses/' + this.props.business + '/treatment/' + this.props.treatment + '/availability');
  },

  render: function() {
    return (
      <div className='result'>
        Awesomeu
      </div>
    );
  },
});
