(function ($) {

Drupal.behaviors.partialDateEstimateChange = {
  attach: function (context, settings) {
//    $('.estimate_selector').once("estimateonchange", function() {
//      $(this).change(function () {
//          alert("working!");
////        alert($(this).attr('date_component') + " changed to: "  + $(this).val()); 
//      });
//    });
    $('.estimate_selector').change(function () {
        var values = $(this).val().split("|");
        var component = $(this).attr('date_component');
        $('.partial_date_component[fieldName="'+component+'"]').val(values[0]);
        $('.partial_date_component[fieldName="'+component+'_to"]').val(values[1]);
        $('#txt_long').val($('#txt_long').val() + $(this).find("option:selected").text() + ", ");
    });
  }
}

})(jQuery);
