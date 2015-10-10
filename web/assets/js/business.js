jQuery(function($) {
  $('#treatmentModal').on('show.bs.modal', function(event) {
    var button = $(event.relatedTarget); // Button that triggered the modal
    var treatment = button.data('treatment-id'); // Extract info from data-* attributes
    var business = button.data('business-id');

    var modal = $(this);
    var container = $(this).find('.form-wrapper')[0];

    React.render(React.createElement(AvailabilityForm, {business: business, treatment: treatment}), container);

  });

  $('[checked="checked"]').parent().addClass("active");
});
