
$(function() {

 $('body').on('click', 'li', function() {
   $(this).toggleClass('selected');
 });

 $('#sortableFields_sf').dblclick(function() {
    var content = $('#sortableFields_sf');
    $('.list2').append($('.list1 .selected').clone());

    $('.list1 li').removeClass('selected');
    $('.list2 li').removeClass('selected');


     /*
       $.ajax({
          url: 'ajax/set_keys.php',
          type: 'post',
          data: {service_type: "map_add", keys: fieldsOrder},
       }); // end ajax call
    */
 });

  $('.list2').dblclick(function() {
    $('.list2 .selected').remove();
 });

});

//$('#move_right').click(function() {
//    $('.list2').append($('.list1 .selected').removeClass('selected'));
//});



  $(function() {
    $('#sortableFields_gnl').sortable({
      update: function(event, ui) {
        var order = [];
        $('#sortableFields_gnl').each(function(e) {
          var fieldsOrder = $(this).sortable('toArray').toString();
        $.ajax({
          url: 'ajax/set_keys.php',
          type: 'post',
          data: {service_type: "gnl", keys: fieldsOrder},
         }); // end ajax call
        });
       }
      })
     });

  $(function() {
    $('#mapped_fields').sortable({
      update: function(event, ui) {
        var order = [];
        $('#mapped_fields').each(function(e) {
          var fieldsOrder = $(this).sortable('toArray').toString();
        $.ajax({
          url: 'ajax/set_keys.php',
          type: 'post',
          data: {service_type: "mapped", keys: fieldsOrder},
         }); // end ajax call
        });
       }
      })
     });

