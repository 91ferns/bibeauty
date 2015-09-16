var BusinessesList = React.createClass({
    render: function() {
        var rows = [];
        if(this.props.businesses){
          this.props.businesses.forEach(function(business) {
              rows.push(<Business info={business} />);
          }.bind(this));
        }else{
          rows.push(<div className='alert alert-danger'>No Results.</div>);
        }
        return (
            <div id="search_results" className="col-md-8">
              {rows}
            </div>
        );
    }
});
