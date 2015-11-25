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

    this.row = -1;
  };

  Autoadd.prototype.getRowCounter = function() {
    return this.row;
  };

  Autoadd.prototype.incrementRow = function() {
    return this.row++;
  };

  Autoadd.prototype.resetRow = function() {
    this.row = -1;
  };

  Autoadd.prototype.serialize = function() {
    return this.self.parents('form').serializeArray();
  };

  Autoadd.prototype.isDirty = function() {
    var $formData = this.serialize();

    var isValid = $formData.filter(function(v) {
      return v.value.toString().length < 1;
    });

    isValid = isValid.length < 1;

    if (!isValid) {
      alert('Please fill out the previous fields first');
    }

    return !isValid;
  };

  Autoadd.prototype.addSchema = function(label, schema) {
    this.schemae[label] = schema;
  };

  Autoadd.prototype.createInputFor = function(schemaData, schemaName, obj) {


    /*
    treatmentCategory: { type: String, label: 'Treatment' },
    name: { type: String, label: 'Name' },
    duration: { type: Number, label: 'Duration' },
    originalPrice: { type: Number, label: 'Full Price', default: 0.0 },
    */

    var input;
    var type;

    if (schemaData.enum) {
      input = $('<select></select>');
      input.attr('multiple', true);

      for (var x in schemaData.enum) {
        var option = schemaData.enum[x];

        var val = Object.keys(option)[0];

        var theOption = $('<option></option>').text(option[val]).attr('value', val);
        input.append(theOption);

      }

    } else {

      input = $('<input>');

      switch (schemaData.type || schemaData) {
        case Number:
          type = 'text';
          break;
        default:
          type = 'text';
      }

      input.attr('type', type);
    }

    input.attr('name', 'autoadd[' + this.getRowCounter() + '][' + schemaName + ']')
      .addClass('form-control').attr('value', schemaData.default || '')
      .attr('placeholder', schemaData.label || schemaName)
      .attr('required', true);

    if (schemaData.disabled || false) {
      input.attr('disabled', true);
    }

    var finalElement;

    if (typeof(obj) === 'object') {
      var key = Object.keys(obj)[0];
      var value = obj[key];

      input.attr('type', 'hidden');
      input.val(key);

      var newElement = $('<span></span>');
      newElement.append(input);
      newElement.append($('<span></span>').text(value));

      finalElement = newElement;
    } else {

      if (obj) {
        input.val(obj);
      }

      if (schemaData.type === Date) {

        var wrapper = $('<div></div>').addClass('input-group');
        var addon = $('<span class="input-group-addon"><i class="fa fa-calendar fa-lg"></i></span>');

        input.attr('id', 'DayField');
        wrapper.append(input).append(addon);

        input.datepicker();

        finalElement = wrapper;
      } else {
        finalElement = input;
      }

    }

    return finalElement;
  };

  Autoadd.prototype.pushRow = function(label, data) {
    var schema = this.schemae[label] || false;
    if (!schema) return false;

    this.incrementRow();

    var newObject = [];

    for (var x in schema) {
      var theData = data[x] || '';
      newObject.push(this.createInputFor(schema[x], x, theData));
    }

    this.rows.push(newObject);

    return newObject;

  };

  Autoadd.prototype.sync = function() {

    this.resetRow();

    var newHtml = [];
    for (var x in this.rows) {

      this.incrementRow();

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
    treatmentCategory: { type: String, label: 'Treatment' },
    name: { type: String, label: 'Name' },
    duration: { type: Number, label: 'Duration' },
    originalPrice: { type: Number, label: 'Full Price', default: 0.0 },
  });

  var enumPopulated = [];
  for (var h = 7; h <= 21; h++) {
    for (var m = 0; m < 60; m = m + 15) {
      var mFormatted;
      if (m === 0) {
        mFormatted = '00';
      } else {
        mFormatted = m;
      }

      var nicehour;

      if (h === 0 || h === 12) {
        nicehour = h;
      } else {
        nicehour = h % 12;
      }

      var meridian = h >= 12 ? 'PM' : 'AM';
      var string = nicehour + ':' + mFormatted + ' ' + meridian;

      var obj = {};
      obj[h + ':' + mFormatted] = string;

      enumPopulated.push(obj);
    }
  }

  theAutoadd.addSchema('offer', {
    treatmentCategory: { type: String, label: 'Treatment' },
    startDate: { type: Date, label: 'Start Date' },
    times: { type: String, label: 'Times', enum: enumPopulated },
    originalPrice: { type: Number, label: 'Original Price', disabled: true },
    discountPrice: { type: Number, label: 'Discount Price', default: 0.0 },
  });

  function getAutoadd() {
    return theAutoadd;
  }

  $('.ul-filter').on('keyup', function() {
    var $this = $(this);
    var filters;

    if ($this.data('filters')) {
      filters = $this.data('filters');
    } else {
      return false;
    }

    var grep = $(filters);

    if (!grep) {
      return false;
    }

    var value = $this.val().toLowerCase();

    grep.children('li').each(function() {
      var parentKey = $(this).data('parent');
      console.log(parentKey);
      if (
        ($(this).text().toLowerCase().search(value) > -1) ||
        (parentKey && parentKey.search(value) > -1)
      ) {
        $(this).show();
      } else {
        $(this).hide();
      }
    });

  });

  $('.add-button-autoadd').click(function() {
    var $this = $(this);
    var a = getAutoadd();

    if (a.isDirty()) {
      return false;
    }

    var id = $this.data('add-id');
    var obj = {};
    obj[id] = $this.data('add-label');

    if ($(this).hasClass('button-treatment')) {
      a.pushRow('treatment', {
        treatmentCategory: obj
      });
    } else {
      a.pushRow('offer', {
        treatmentCategory: obj,
        originalPrice: $this.data('original-price')
      });
    }

    a.sync();


  });
});
