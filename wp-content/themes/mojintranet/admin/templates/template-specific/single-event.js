jQuery(function($) {
  $("input.datepicker").datepicker({
    dateFormat: "dd/mm/yy",
    constrainInput: true
  }).keypress(function(event) {
    event.preventDefault();
  });
  $("input.timepicker").timepicker({
    disableTextInput: true
  });
});
