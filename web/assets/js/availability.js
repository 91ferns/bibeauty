jQuery(function($) {
  var availabilityPanel = $('#formAvailabilityPanel');

  $('#repeat-subform').hide();

  if (availabilityPanel) {
    $('input[name="RecurrenceType"]').change(function() {
      var val = $('input[name="RecurrenceType"]:checked').val();
      if (val === 'weekly') {
        $('#repeat-subform').show();
      } else {
        $('#repeat-subform').hide();
      }
    });

    $('.select2').select2({
      closeOnSelect: false,
      theme: "classic"
    });
  }
});
