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
    });
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

  var Autoadd = function() {
    this.self = $('.autoadd');
    this.schemae = {};

    this.rows = [];
  };

  Autoadd.prototype.isDirty = function() {
    return false;
  };

  Autoadd.prototype.addSchema = function(label, schema) {
    this.schemae[label] = schema;
  };

  Autoadd.prototype.createInputFor = function(name, d) {
    var input = $('<input>');
    d = d || '';

    input.attr('name', name + '[]').addClass('form-control').attr('value', d)
      .attr('placeholder', name);

    return input;
  };

  Autoadd.prototype.pushRow = function(label, data) {
    var schema = this.schemae[label] || false;
    if (!schema) return false;

    var newObject = [];

    for (var x in schema) {
      if (data.hasOwnProperty(x)) {
        newObject.push(data[x]);
      } else {
        newObject.push(this.createInputFor(x));
      }
    }

    this.rows.push(newObject);

    return newObject;

  };

  Autoadd.prototype.sync = function() {

    var newHtml = [];
    for (var x in this.rows) {
      var theRow = this.rows[x];

      var newRow = $('<tr></tr>');

      for (var rt in theRow) {
        var tmp = $('<td></td>');

        tmp.html(theRow[rt]);
        newRow.append(tmp);
      }

      newHtml.push(newRow);
    }

    this.self.html('');
    for (var y in newHtml) {
      this.self.append(newHtml[y]);
    }
  };

  var theAutoadd = new Autoadd();

  theAutoadd.addSchema('treatment', {
    treatment: String,
    name: String,
    duration: Number,
    price: { type: Number, label: 'Full Price' },
  });

  function getAutoadd() {
    return theAutoadd;
  }

  $('.add-button-autoadd').click(function() {
    var $this = $(this);
    var a = getAutoadd();
    if (a.isDirty()) {
      return false;
    }

    if ($(this).hasClass('button-treatment')) {
      a.pushRow('treatment', {
        treatment: $this.data('add-label')
      });
    } else {

    }

    a.sync();


  });
});
