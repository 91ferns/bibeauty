jQuery(function($) {
  if ($('#BusinessesSelect')) {
    $('#BusinessesSelect').select2({
      theme: "bootstrap",
      minimumResultsForSearch: 6,
      placeholder: 'My Businesses',
    });
    $('#BusinessesSelect').on('change', function() {
      var val = $(this).val();
      window.location.href = val;
    })
  }
});
