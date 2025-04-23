export default (function ($) {
  console.log('Document Subscribe script loaded')

  const Subscribe = {
    init: () => {
      Subscribe.icons = $('.doc-subscribe-link')
      Subscribe.debug = false
    },
    vars: {
      context: null,
      context_id: null,
      context_name: null,
      document_id: null,
      subscribed_to: null,
      submit_timeout: null
    },
    context: (id) => window.document_option_links.find( o => o.context === id),
    dialog: {
      box: () => {
        // there might be an active timeout, clear it if it exists
        if (Subscribe.vars.submit_timeout) {
          clearTimeout(Subscribe.vars.submit_timeout);
        }

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
        dialog.append('<span class="close"> X </span>').
          append('<h1>Document Subscription</h1>').
          append('<h2>' + Subscribe.vars.context.text + '</h2>').
          append('<p>Would you like to know when this document is updated?</p>').
          append('<p>Enter your work email address and we\'ll keep you posted.</p>').
          append('<input type="email" name="email_address" value="" placeholder="Add your email address" />').
          append('<input type="hidden" name="post_id" value="' + Subscribe.vars.context.id + '" autocomplete="off" />').
          append('<button class="subscribe-button">Subscribe</button>').
          append('<div class="doc-subscribe--loading"></div>').
          append('<div class="doc-subscribe--events"></div>')

        // remove all the other dialogs
        $('.doc-subscribe--dialog').remove()

        // add the dialog to the body
        $('body').append(dialog)

        Subscribe.listen.close();
        Subscribe.listen.submit();
        Subscribe.dialog.log.target = dialog.find('.doc-subscribe--events');

        return dialog
      },
      log: {
        target: null, // set in the dialog
        add:(title, text, status) => {
          // check if the status is valid
          if (!Subscribe.validate.status(status)) {
            return false;
          }

          status = (status === 'none' ? '' : ' ' + status);

          const message = $('<div />')
          .addClass('doc-subscribe--message' + status)
          .append('<strong>' + title + '</strong><br><span>' + text + '</span>')

          Subscribe.dialog.log.target.append(message);
        },
        clear: () => {
          if (Subscribe.dialog.log.target) {
            Subscribe.dialog.log.target.empty();
          }
        }
      },
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
            Subscribe.dialog.box()
          })
        });
      },
      close: function () {
        $('.doc-subscribe--dialog .close').on('click', function () {
          $(this).closest('.doc-subscribe--dialog').remove()
        })
      },
      submit: function () {
        $('#doc-subscribe--' + Subscribe.vars.context_id + ' .subscribe-button').on('click', function (event) {
          event.preventDefault();
          Subscribe.dialog.log.clear();

          // get the email address
          const email = $(this).siblings('input[name="email_address"]').val();
          const post_id = $(this).siblings('input[name="post_id"]').val();

          // check if the email is valid
          if (!Subscribe.validate.email(email)) {
            Subscribe.dialog.log.add('Invalid', 'email address', 'error');
            return;
          }

          if (post_id > 0) {
            $.ajax({
              url: '/wp-json/document-subscriptions/v1/subscribe/' + post_id,
              type: 'POST',
              data: {
                email: email
              },
              beforeSend: function () {
                Subscribe.dialog.log.clear()
                $('.doc-subscribe--loading').addClass('show')
              },
              success: function (response) {
                $('.doc-subscribe--loading').removeClass('show')

                // check if the response is valid
                if (typeof response !== 'object') {
                  Subscribe.dialog.log.add('Invalid', 'response from server', 'error');
                  return;
                }

                Subscribe.dialog.log.add('Thank you!', response.message, 'success');
                Subscribe.dialog.log.add(
                  'Manage subscriptions here',
                  '<a href="https:intranet.justice.gov.uk/user-subscriptions">Show me documents I\'m subscribed to</a>',
                  'none'
                );

                if (Subscribe.debug) {
                  Subscribe.dialog.log.add('Debug', response, 'info');
                }

                if (!Subscribe.debug) {
                  // ... wait to close the dialog box
                 Subscribe.vars.submit_timeout = setTimeout(function () {
                    jQuery('#doc-subscribe--' + Subscribe.vars.context_id).fadeOut(400, function () {
                      $(this).remove();
                    });
                  }, 3000);
                }
              },
              error: function (response) {
                const result = JSON.parse(response.responseText);
                $('.doc-subscribe--loading').removeClass('show')
                Subscribe.dialog.log.add('Oops, that didn\'t work.', result.message, 'error');
                Subscribe.dialog.log.add('', 'Please try again.', 'none');
              }
            })
          } else {
            Subscribe.dialog.log.add(
              'Oops, that didn\'t work.',
              'We are sorry.<br>Data is missing meaning we couldn\'t complete your request.',
              'warning'
            );
          }
        })
      }
    },
    validate: {
      email: (email) => {
        const re = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        return re.test(String(email).toLowerCase());
      },
      status: (status) => {
        const allowed = ['success', 'error', 'warning', 'info', 'none']

        // check if the status is a string
        if (typeof status !== 'string') {
          console.log('Status must be a string:', status);
          return false;
        }

        // check if the status is in the allowed list
        if (allowed.indexOf(status) === -1) {
          console.log('Invalid status:', status);
          return false;
        }

        return true;
      }
    }
  }

  $(function () {
    Subscribe.init();
    Subscribe.listen.click();
  })

  return Subscribe;

})(jQuery)
