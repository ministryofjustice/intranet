jQuery(function($) {
  $("input.datepicker").datepicker({
    dateFormat: "yy-mm-dd",
    constrainInput: true
  }).keypress(function(event) {
    event.preventDefault();
  });
  $("input.timepicker").timeEntry({
    show24Hours: true,
    timeSteps: [1,15,0],
    initialField: 0,
    spinnerImage: ''
  });
  $("#event-start-date, #event-end-date").on("change",dateValidation);
  $("#event-allday").on("change",toggleEventTimes);

  function dateValidation(e) {
    startDate = $("#event-start-date").val();
    endDate = $("#event-end-date").val();
    if(endDate=="") {
      $("#event-end-date").val(startDate);
    } else if(startDate>endDate) {
      if (e.currentTarget.id=="event-end-date") {
        targetDate = startDate;
      } else {
        targetDate = endDate;
      }
      $(e.currentTarget).val(targetDate);
      alert("Event cannot start after it ends");
    }
  }

  function toggleEventTimes(e) {
    if($("#event-allday").prop('checked')) {
      $('.event-times').css('display','none');
      $("#event-start-time").val('00:00');
      $("#event-end-time").val('23:45');
    } else {
      $('.event-times').css('display','table-cell');
      $("#event-start-time").val('');
      $("#event-end-time").val('');
    }
  }
});
