var txAdder = (function($,w,undefined){
  var init = function(isOffers){
    isOffers = (typeof isOffers === 'undefined') ? false : isOffers;
    $('.treatment.list-unstyled li').on('click',function(){
        txAdd($(this),isOffers);
    });
  },
  txAdd = function($row,doOffers){
    var cat     = $row.data('cat');
    var txid    = $row.data('id');
    var txprice = $row.data('price');
    var txname  = $row.text();
    var $table  = getTableByCat(cat);
    console.log(txprice);
    addNewRow($table,cat,txid,txname,txprice,doOffers);
  },
  getRowTemplate = function(cat,txid,txname){
   return '<tr data-id="'+txid+'">'+
            '<td><span class="cat-icon">'+cat.charAt(0)+'</span>'+txname+'<input type="hidden" name="category[]" value="'+txid+'"/></td>'+
            '<td><div class="input-group "><input type="text" class="form-control" name="name[]" value=""></div></td>'+
            '<td><div class="input-group "><input type="text" class="form-control" name="duration[]" value=""><span class="input-group-addon">min.</span></div></td>'+
            '<td><div class="input-group "><span class="input-group-addon">$</span><input type="text" class="form-control" name="originalPrice[]" value=""></div></td>'+
          '</tr>';
  },
  getOfferRowTemplate = function(cat,txid,txname,txprice){
    return '<tr data-id="'+txid+'">'+
             '<td><span class="cat-icon">'+cat.charAt(0)+'</span>'+txname+'<input type="hidden" name="category[]" value="'+txid+'"/></td>'+
             '<td><div class="input-group "><input type="text" class="form-control" name="StartDate[]" value=""></div></td>'+
             '<td><div class="input-group "><input type="text" class="form-control" name="Times[]" value=""></div></td>'+
             '<td><div class="input-group "><span class="input-group-addon">$</span><input type="text" disabled class="form-control" name="OriginalPrice[]" value="'+txprice+'"></div></td>'+
             '<td><div class="input-group "><span class="input-group-addon">$</span><input type="text" class="form-control" name="CurrentPrice[]" value=""></div>'+
               '<div class="form-group" style="padding-top: 30px;"><div class="input-group"><label for="">Repeat</label><br><div class="btn-group" data-toggle="buttons">'+
                  '<label class="btn btn-primary active" ><input type="radio" name="RecurrenceType" checked value="never" class="form-control" /> Never</label>'+
                  '<label class="btn btn-primary" ><input type="radio" name="RecurrenceType" value="daily" class="form-control" /> Daily</label>'+
                  '<label class="btn btn-primary"><input type="radio" name="RecurrenceType" value="weekly" class="form-control" /> Weekly</label>'+
                  '<label class="btn btn-primary"><input type="radio" name="RecurrenceType" value="monthly" class="form-control" /> Monthly</label>'+
                  '</div></div></div> <!-- /.form-group -->'+
              '</td>'+
           '</tr>';
  },
  addNewRow = function($table,cat,txid,txname,txprice,doOffers){
    var rowHtml = (doOffers === true) ? getOfferRowTemplate(cat.trim(),txid,txname,txprice) : getRowTemplate(cat.trim(),txid,txname);
    $table      = checkMakeTable($table,cat);
    $table.append(rowHtml);
  },
  checkMakeTable = function($table,cat){
    if($table.length <= 0){
      $table = $('.panel-body > .col-md-8').append(getNewTableHtml(cat));
    }
    return $table;
  },
  getNewTableHtml = function(cat){
    return '<h4>'+cat+'</h4>'+
          '<table data-cat="'+cat+'" class="table table-striped">'+
          '<thead>  <tr><th>Treatment</th><th>Name</th><th>Duration</th><th>Full Price</th></tr></thead>'+
          '<tbody></tbody></table>';
  },
  getTableByCat = function(cat){
    return $('table[data-cat="'+cat+'"]');
  }
  ;
  return {
    init:init
  };

}(jQuery,window));
