jQuery(function($) {
  $('.fake-checkbox').on('click', function() {
    var par = $(this).parents('.brand');
    par.toggleClass('checked');
    var checkbox = par.find('input[type="checkbox"]');
    checkbox.trigger('click');
  })
});
