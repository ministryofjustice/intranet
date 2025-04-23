export default (function ($) {
  console.log('Document Subscribe script loaded')

  const Subscribe = {
    init: () => {
      Subscribe.icons = $('.doc-subscribe-link')
    },
    vars: {
      context: null,
      context_id: null,
      context_name: null,
      document_id: null,
    },
    context: (id) => window.document_option_links.find( o => o.context === id),
    dialog: () => {
      const id = "doc-subscribe--" + Subscribe.vars.context_id
      const dialogCheck = $('#' + id)

      // check if the dialog already exists
      if (dialogCheck.length) {
        // remove it
        dialogCheck.remove()
        return;
      }

      // create the dialog
      const dialog = $('<section class="doc-subscribe--dialog" id="' + id + '"></section>')
      dialog.append('<span class="close"> X </span>')
        .append('<h1>Document Subscription</h1>')
        .append('<h2>'+ Subscribe.vars.context.text +'</h2>')
        .append('<p>Would you like to know when this document is updated?</p>')
        .append('<p>Enter your work email address and we\'ll keep you posted.</p>')
        .append('<input type="email" name="email_address" value="" placeholder="Add your email address" />')
        .append('<input type="hidden" name="post_id" value="'+ Subscribe.vars.context.id +'" />')
        .append('<button class="subscribe-button">Subscribe</button>')

      // remove all the other dialogs
      $('.doc-subscribe--dialog').remove()

      // add the dialog to the body
      $('body').append(dialog)
      Subscribe.listen.close();

      return dialog
    },
    listen: {
      click: function () {
        Subscribe.icons.each(function (index, icon) {
          $(icon).on('click', function (event) {
            if (event.target.tagName === 'A') {
              return true;
            }
            // resolve the context
            Subscribe.vars.context_id = $(this).attr('id');
            Subscribe.vars.context = Subscribe.context(Subscribe.vars.context_id);
            Subscribe.dialog()
          })
        });
      },
      close: function () {
        $('.doc-subscribe--dialog .close').on('click', function () {
          $(this).closest('.doc-subscribe--dialog').remove()
        })
      },
    }
  }

  $(function () {
    Subscribe.init();
    Subscribe.listen.click();
  })

  return Subscribe;

})(jQuery)
