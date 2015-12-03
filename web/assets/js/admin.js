jQuery(function($) {

  var timesSource   = $("#times-template").html();
  var timesTeplate = Handlebars.compile(timesSource);

  function selectize(element) {

    if (element.prop("tagName") !== 'SELECT') {
      return;
    }

    element.hide();

    var theEnum = [];

    $.each(element.find('option'), function() {
      var key = $(this).val();
      var val = $(this).text();

      theEnum.push({ key: key, value: val });

    });

    var HTML = timesTeplate({ enum: theEnum });
    var newElement = $(HTML);

    newElement.find('li > a').not('.select-replacement-all,.select-replacement-all,.select-replacement-close').on('click', function(e) {
      e.stopPropagation();
      var isActive = $(this).parent().hasClass('active');
      $(this).parent().toggleClass('active');

      var opt = element.find('option[value="' + $(this).data('key') + '"]');

      if (isActive) {
        opt.prop('selected', false);
      } else {
        opt.prop('selected', true);
      }

    });

    newElement.find('.select-replacement-all').on('click', function() {
      $(this).parent().parent().find('li').addClass('active'); // ('.dropdown-menu')
      element.find('option').prop('selected', true);
    });

    newElement.find('.select-replacement-all').on('click', function() {
      $(this).parent().parent().find('li').removeClass('active'); // ('.dropdown-menu')
      element.find('option').prop('selected', false);
    });

    newElement.find('.select-replacement-close').on('click', function() {
      $(this).parents('.btn-group').removeClass('open');
    });

    element.after(newElement);


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
    this.form = null;

    this.postExecutionHooks = {};
    this.executionCache = {};

  };

  Autoadd.prototype.addToExecutionCache = function(index, html) {
    this.executionCache[index] = html;
  };

  Autoadd.prototype.getFromExecutionCache = function(index) {
    return this.executionCache[index] || false;
  };

  Autoadd.prototype.getRowCounter = function() {
    return this.row;
  };

  Autoadd.prototype.incrementRow = function() {
    return this.row++;
  };

  Autoadd.prototype.resetRow = function() {
    var $this = this;
    this.row = -1;

    $('.hook-generated').each(function() {
      var index = $(this).data('index');

      $this.addToExecutionCache(index, $(this).html());
    });
  };

  Autoadd.prototype.serialize = function() {
    var form = this.form || this.self.parents('form');
    return form.serializeArray();
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

  Autoadd.prototype.createInputFor = function(label, schemaData, schemaName, obj) {

    var input;
    var type;

    if (schemaData.type === undefined) {
      this.postExecutionHooks[this.getRowCounter()] = {
        type: label,
        data: schemaData,
        callback: schemaData.callback
      };

      return;
    } else if (schemaData.enum) {
      input = $('<select></select>');
      input.attr('multiple', true).attr('data-placeholder', 'Times');

      for (var x in schemaData.enum) {
        var option = schemaData.enum[x];

        var val = Object.keys(option)[0];

        var theOption = $('<option></option>').text(option[val]).attr('value', val);
        input.append(theOption);

      }

    } else if (schemaData.type === Text) {
      input = $('<textarea></textarea>');
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

    input.attr('name', label + '[' + this.getRowCounter() + '][' + schemaName + ']')
      .addClass('form-control').attr('value', schemaData.default || '')
      .attr('placeholder', schemaData.label || schemaName)
      .attr('required', true).addClass(label + '-form-input');

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

    var labelString = schemaData.label || schemaName;

    var td = $('<td></td>');
    td.attr('title', labelString.toUpperCase());
    td.html(finalElement);

    return td;
  };

  Autoadd.prototype.pushRow = function(label, data) {
    var schema = this.schemae[label] || false;
    if (!schema) return false;

    this.incrementRow();

    var newObject = [];

    var first = true;

    for (var x in schema) {
      var theData = data[x] || '';
      var input = this.createInputFor(label, schema[x], x, theData);

      if (input) {
        if (first) {
          input.append(' <a href="javascript: void(0);" class="autoadd-row-remove"><i class="fa fa-times"></i></a>');
        }
        newObject.push(input);
      }
      first = false;
    }

    this.rows.push(newObject);

    return newObject;

  };

  Autoadd.prototype.adjustPECacheForRemoval = function(number) {
    delete this.postExecutionHooks[number]; // deletes it

    var newPECache = {};
    var newHooks = {};
    var newIndex;

    for (var x in this.postExecutionHooks) {
      if (x > number) {
        newIndex = x - 1;
      } else {
        newIndex = x;
      }

      newHooks[newIndex] = this.postExecutionHooks[x];
    }

    for (var y in this.executionCache) {
      if (y > number) {
        newIndex = y - 1;
      } else {
        newIndex = y;
      }

      newPECache[newIndex] = this.executionCache[y];
    }

    this.postExecutionHooks = newHooks;
    this.executionCache = newPECache;

  };

  Autoadd.prototype.postExecutionHookFor = function(number, newRow) {
    var hook = this.postExecutionHooks[number] || false;

    if (!hook) {
      return;
    }

    var tr = $('<tr></tr>');
    tr.addClass('hook-generated').attr('data-index', number);

    var cached = this.getFromExecutionCache(number);

    if (cached) {
      tr.html(cached);

      return tr;
    }

    this.postExecutionHooks[number].executed = true;

    hook.callback(hook.type, hook.data, number, function(data) {
      tr.html(data);
      selectize(newRow.find('select'));

      newRow.find('.offer-form-input').on('change', function() {

        var val = $(this).val();
        if (val.indexOf('all') > -1) {
          // If all is in the array
          $('option', this).prop('selected', true);
          $('option[value="all"]', this).prop('selected', false);
          $(this).trigger('change');
          $(this).select2('close');
        }

      });
      newRow.after(tr);
    });

    return false;

  };

  Autoadd.prototype.popRow = function(index) {
    this.adjustPECacheForRemoval(index);

    var rows = this.rows;
    var newRows = [];

    for (var x in rows) {
      if (x == index) {
        continue;
      }
      newRows.push(rows[x]);
    }


    this.rows = newRows;
    return this;
  };

  Autoadd.prototype.rebindEvents = function() {
    var rowRemove = this.self.find('.autoadd-row-remove');
    var $this = this;

    rowRemove.off('click').on('click', function() {

      var thisRow = $(this).parents('tr');
      var index = thisRow.data('index');

      $this.popRow(index);

      $this.sync();
    });

  };

  Autoadd.prototype.sync = function() {

    this.resetRow();

    var newHtml = [];
    for (var x in this.rows) {

      this.incrementRow();

      var theRow = this.rows[x];

      var newRow = $('<tr></tr>');
      newRow.attr('data-index', x);
      newRow.addClass('autoadd-added');

      for (var rt in theRow) {
        newRow.append(theRow[rt]);
      }

      newHtml.push(newRow);

      var maybeHtml = this.postExecutionHookFor(x, newRow);
      if (maybeHtml) {
        newHtml.push(maybeHtml);
      }

    }

    this.self.html('');
    for (var y in newHtml) {
      this.self.append(newHtml[y]);
    }

    this.rebindEvents();

  };

  Autoadd.prototype.bindTo = function(form) {
    this.form = $(form);

    this.form.on('submit', function(e) {

      this.form.find('.alert').remove();

      // Need to iterate through all the rows and validate them against their schema
      var data = this.form.serializeArray();
      var errors = [];

      for (var itemNumber in data) {
        var theItem = data[itemNumber];

        var key = theItem.name;
        var value = theItem.value;

        // Need to parse the key
        // first is gonna be autoadd since we're using the autoadd form
        var found = key.match(/([^[]+)\[([0-9]+)\]\[(.+)\]/);

        var schemaType = found[1] || false;
        var theNumber = found[2] || false;
        var itemCategory = found[3] || false;

        if (!this.schemae.hasOwnProperty(schemaType) || !itemNumber ||!itemCategory) {
          continue;
        }

        var theSchema = this.schemae[schemaType];
        var relatedSchema = theSchema[itemCategory] || false;

        if (!relatedSchema) {
          continue;
        }

        if (relatedSchema.validation) {
          var valid = relatedSchema.validation(value);

          if (valid === true) {
            continue;
          } else {
            errors.push(valid);
          }
        }

      }

      if (errors.length > 0) {
        var ul = $('<div class="alert alert-danger"><ul></ul></div>');

        for (var x in errors) {
          var li = $('<li></li>').text(errors[x]);
          ul.append(li);
        }

        this.form.prepend(ul);
        return false;
      } else {
        return true;
      }


    }.bind(this));

  };

  var theAutoadd = new Autoadd();

  theAutoadd.addSchema('treatment', {
    treatmentCategory: { type: String, label: 'Treatment' },
    name: { type: String, label: 'What is it?' },
    description: { type: Text, label: 'What do they get?', },
    duration: { type: Number, label: 'Duration (in minutes)', validation: function(val) {
      var intVal = parseInt(val);

      if (intVal >= 520) {
        return 'Duration must be shorter than 520 minutes';
      }

      if (intVal >= 15) {
        return true;
      }

      return 'Duration must be longer than 15 minutes';

    } },
    originalPrice: { type: Number, label: 'Full Price', default: 0.0,
      validation: function(val) {
        var floatVal = parseFloat(val);

        if (floatVal > 0.00) {
          return true;
        }

        return 'Price must be greater than $0.00';

      }
    },
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
    recurrence: { type: undefined, callback: function(schemaType, schemaData, num, callback) {
      var promise = $.ajax({
        type: 'GET',
        url: '/ajax/offers/recurrenceform',
        data: {
          prefix: schemaType,
          index: num,
        },
        dataType: 'html',
      }).done(function(data) {
        callback(data);
      }).error(function(err) {
        alert(err);
      });

    }}
  });

  theAutoadd.bindTo('#AutoaddForm');

  function getAutoadd() {
    return theAutoadd;
  }

  $('.canvas').click(function() {
    // close form-focused
    $('.form-focused').removeClass('form-focused');
  });

  $('.panel-sidebar').click(function(e) {
    e.stopPropagation();
  });

  $('.ul-filter')
    .on('focus', function() {
      $(this).parents('.panel').first().addClass('form-focused');
    });

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

    /*
    if (a.isDirty()) {
      return false;
    }
    */

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

  $('.checkbox-button').on('change', function() {
    var $this = $(this);
    if ($this.is(':checked')) {
      $this.parent().addClass('active');
    } else {
      $this.parent().removeClass('active');
    }
  });

  $('[data-confirm="true"]').click(function(e) {
    if (!confirm('Are you sure you want to delete this business?')) {
      e.preventDefault();
      return false;
    }
  });
});
