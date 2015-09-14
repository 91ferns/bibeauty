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