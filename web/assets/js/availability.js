jQuery(function($) {
  var availabilityPanel = $('#formAvailabilityPanel');

  $('#repeat-subform').hide();

  if (availabilityPanel) {
    $('#showRepeat').click(function() {
      var elem = $(this);
      if (elem.is(':checked')) {
        $('#repeat-subform').show();
      } else {
        $('#repeat-subform').hide();
      }
    });

    $('select[name="Time"]').select2({
      closeOnSelect: false
    });
  }
});
