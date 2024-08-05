export default (function ($) {
  const Feedback= {
    style: {
      box: {
        backgroundColor: 'rgba(255, 255, 255, 0.75)',
        border: '5px solid #005ea5',
        bottom: '30px',
        boxSizing: 'border-box',
        fontFamily: 'nta, arial, helvetica, sans-serif',
        fontSize: '14px',
        fontSmoothing: 'antialiased',
        fontWeight: '900',
        height: 'auto',
        padding: '15px 20px',
        position: 'fixed',
        right: '30px',
        width: '240px',
      },
      link: {
        backgroundColor: 'green',
        color: 'white',
        fontFamily: 'nta, arial, helvetica, sans-serif',
        padding: '9px',
        textAlign: 'center',
        textDecoration: 'none',
        display: 'block',
      },
      span: {
        fontFamily: 'nta, arial, helvetica, sans-serif',
        fontSize: '12px',
        fontStyle: 'italic',
        textAlign: 'center',
        display: 'block',
        marginTop: '5px',
      }
    },
    init: () => {
      const feedback_box = $('<div\>', { id: 'feedback-box' })

      feedback_box.html(
        'You are viewing a test version of the MoJ Intranet<br><br>' +
        '<a class="uat-feedback" href="https://forms.office.com/e/aDTcBxUfdF" target="_blank">&nbsp; Send feedback &nbsp;</a>' +
        '<span> ( opens in a new tab )</span>',
      ).css(Feedback.style.box)

      // drop the box in the UI
      $('body').append(feedback_box)

      // apply styles
      $('#feedback-box a.uat-feedback').css(Feedback.style.link)
      $('#feedback-box span').css(Feedback.style.span)
    }
  }

  $(function () {
    Feedback.init()
  })

})(jQuery);
