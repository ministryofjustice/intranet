jQuery(function($) {
  $("input.datepicker").datepicker({
    dateFormat: "yy-mm-dd",
    constrainInput: true
  }).keypress(function(event) {
    event.preventDefault();
  });
  $("input.timepicker").timepicker({
    disableTextInput: true,
    timeFormat: 'H:i'
  });
});
