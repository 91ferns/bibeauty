var txAdder = (function($,w,undefined){
  var init = function(isOffers){
    isOffers = (typeof isOffers === 'undefined') ? false : isOffers;
    $('.treatment.list-unstyled li').on('click',function(){
        var incr = getIncr();
        txAdd($(this),isOffers,incr);
    });
  },
  txAdd = function($row,doOffers,incr){
    var cat     = $row.data('cat');
    var txid    = $row.data('id');
    var txprice = $row.data('price');
    var txname  = $row.text();
    var $table  = getTableByCat(cat);
    addNewRow($table,cat,txid,txname,txprice,doOffers,incr);
    $('tr[data-id="'+txid+'"]').find('select').select2();
    $('.DayField').datepicker();
  },
  getIncr = function(){
    var rows = $('.container .panel .col-md-8 tbody tr');
    return rows.length+1;
  },
  getRowTemplate = function(cat,txid,txname){
   return '<tr data-id="'+txid+'">'+
            '<td><span class="cat-icon">'+cat.charAt(0)+'</span>'+txname+'<input type="hidden" name="newcategory[]" value="'+txid+'"/></td>'+
            '<td><div class="input-group "><input type="text" class="form-control" name="newname[]" value=""></div></td>'+
            '<td><div class="input-group "><input type="text" class="form-control" name="newduration[]" value=""><span class="input-group-addon">min.</span></div></td>'+
            '<td><div class="input-group "><span class="input-group-addon">$</span><input type="text" class="form-control" name="neworiginalPrice[]" value=""></div></td>'+
          '</tr>';
  },
  getOfferRowTemplate = function(cat,txid,txname,txprice,incr){
    return '<tr data-id="'+txid+'">'+
             '<td><span class="cat-icon">'+cat.charAt(0)+'</span>'+txname+'<input type="hidden" name="newTreatment['+incr+']" value="'+txid+'"/></td>'+
             '<td><div class="input-group "><input type="text" class="form-control DayField" name="newStartDate['+incr+']" value=""></div></td>'+
             '<td><div class="input-group ">'+ getTimeSelect(incr) +' </div></td>'+
             '<td><div class="input-group "><span class="input-group-addon">$</span><input type="text" disabled class="form-control" name="newOriginalPrice['+incr+']" value="'+txprice+'"></div></td>'+
             '<td><div class="input-group "><span class="input-group-addon">$</span><input type="text" class="form-control" name="newCurrentPrice['+incr+']" value=""></div>'+
               '<div class="form-group" style="padding-top: 30px;"><div class="input-group" style="position:relative;"><br><div class="btn-group" style="position: absolute;left:-500px;" data-toggle="buttons"><label for="">Repeat</label>'+
                  '<label class="btn btn-primary active" ><input type="radio" name="newRecurrenceType['+incr+'][]" checked value="never" class="form-control" /> Never</label>'+
                  '<label class="btn btn-primary" ><input type="radio" name="newRecurrenceType['+incr+'][]" value="daily" class="form-control" /> Daily</label>'+
                  '<label class="btn btn-primary"><input type="radio" name="newRecurrenceType['+incr+'][]" value="weekly" class="form-control" /> Weekly</label>'+
                  '<label class="btn btn-primary"><input type="radio" name="newRecurrenceType['+incr+'][]" value="monthly" class="form-control" /> Monthly</label>'+
                  '</div></div> <!-- /.form-group -->'+
              '</td>'+
           '</tr>';
  },
  addNewRow = function($table,cat,txid,txname,txprice,doOffers,incr){
    var rowHtml = (doOffers === true) ? getOfferRowTemplate(cat.trim(),txid,txname,txprice,incr) : getRowTemplate(cat.trim(),txid,txname);
    $table      = checkMakeTable($table,cat,doOffers);
    $table.append(rowHtml);
  },
  getTimeSelect = function(incr){
    var sel ='<div class="select2-wrapper"><select multiple="true" class="form-control select2" name="newTimes['+ incr +'][]"><option value="ALL">All Times</option>';
    for(var i = 7; i<=21; i++){
      for(var j=0; j<=3; j++){
        var min  = j*15;
        if(j===0) min += '0';
        var hour24 = i+':'+min;
        var hour12 = (i < 13) ? i+':'+min +' AM' : i-12 + ':' + min + ' PM';

        sel += '<option value="'+hour24+'">'+hour12+'</option>';
      }
    }

    return sel+' </select></div>';
  },
  checkMakeTable = function($table,cat,doOffers){
    if($table.length <= 0){
      table = getNewTableHtml(cat,doOffers);
      $submit = $('input[type="submit"]');
      $submit.removeClass('hidden');
      $('.no-offers').addClass('hidden');
      $(table).insertBefore($submit);
    }
    return $('table[data-cat="'+cat+'"]');
  },
  getNewTableHtml = function(cat,doOffers){

    var heads = (!doOffers) ? '<th>Treatment</th><th>Name</th><th>Duration</th><th>Full Price</th>' :   '<th>Treatment</th><th>Start Date</th><th>Times</th><th style="width:13%;">Original Price</th><th style="width:13%;">Discount Price</th>' ;

      return '<h4>'+cat+'</h4>'+
            '<div class="table-responsive shadow"><table data-cat="'+cat+'" class="table table-striped">'+
            '<thead>  <tr>'+ heads +'</tr></thead>'+
            '<tbody></tbody></table></div>';
  },
  getTableByCat = function(cat){
    return $('table[data-cat="'+cat+'"]');
  }
  ;
  return {
    init:init
  };

}(jQuery,window));
