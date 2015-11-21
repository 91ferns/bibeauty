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

  /*
  $(document).on('change', '.btn-file :file', function() {
    var input = $(this),
        numFiles = input.get(0).files ? input.get(0).files.length : 1,
        label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
    input.trigger('fileselect', [numFiles, label]);
  });

  $(document).ready( function() {
    $('.btn-file :file').on('fileselect', function(event, numFiles, label) {
        console.log(numFiles);
        console.log(label);
    });
  });
  */
});
