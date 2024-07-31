
export default (function ($) {
    const Backdrop = {
        cookie: {
            name: 'moj_uat_session',
            value: 'user_accepted',
            set: (value, days = 7, path = '/') => {
                const expires = new Date( Date.now() + days * 864e5).toUTCString()
                document.cookie = Backdrop.cookie.name + '=' + value + '; expires=' + expires + '; path=' + path
            },
            get: () => {
                return document.cookie.split('; ').reduce((r, v) => {
                    const parts = v.split('=')
                    return parts[0] === Backdrop.cookie.name ? parts[1] : r
                }, '')
            },
            delete: () => Backdrop.cookie.set('', -1),
        },
        style: {
            img: {
                maxWidth: '120px',
                height: 'auto',
                float: 'left',
                marginRight: '30px'
            },
            button: {
                continue: {
                    backgroundColor: 'green',
                    color: 'white',
                    cursor: 'pointer'
                },
                escape: {
                    backgroundColor: '#2271b1',
                    color: 'white',
                    cursor: 'pointer'
                }
            },
            modal: {
                textAlign:'left',
                padding:'20px 30px',
                background:'#fff',
                borderRadius:'5px',
                alignItems: 'center',
                verticalAlign:'middle',
                maxWidth:'450px',
                width: '450px',
                margin:'50px auto',
                border: '6px solid #2271b1'
            },
            backdrop: {
                display:'flex',
                alignItems: 'center',
                backdropFilter: 'blur(5px)',
                textAlign:'center',
                position: 'fixed',
                zIndex:1000000,
                height:'100%',
                width:'100%'
            }
        },
        feature: {
            can_run: () => {
                const runOn = [
                    'dev.intranet',
                    'intranet.docker',
                    'staging.intranet'
                ];
                let ii = 0;

                for (ii; ii < runOn.length; ii++) {
                    if (location.href.includes(runOn[ii])) {
                        return true;
                    }
                }
                return false;
            }
        },
        modal: (title, html) => {
            let modal = $('<div\>', { 'class': 'heartbeat__modal' })
                .css(Backdrop.style.modal);

            const backdrop = $('<div\>', { 'class': 'heartbeat__backdrop' })
                .css(Backdrop.style.backdrop);

            const heading = $('<h3\>').text(title).css({fontWeight:'700', fontSize: '2rem', lineHeight: '3.3rem'})
            const content = $('<p\>').html(html).css({})
            const image = $('<img>', {
                src: '/app/themes/clarity/dist/images/crown_copyright_logo.png',
                alt: 'Crown copyright logo',
            }).css(Backdrop.style.img);

            modal = modal.append(image, heading, content);
            console.log('Heartbeat:', modal);

            return backdrop.append(modal);
        },
        feedback: () => {
            // We can use this to place a box in the bottom right corner of
            // the screen. This box can remind the user they are UATing and
            // offer a link to submit feedback.
            console.log('Display the feedback box.');
        },
        confirm: () => {
            const title = 'You are about to view a test version of the MoJ Intranet'
            const html = '<br />If you intend to assist in user acceptance testing, please continue.<br /><br />' +
              '<button class="modal-continue primary" type="button">&nbsp; Continue &nbsp;</button> &nbsp; or &nbsp; ' +
              '<button class="modal-escape primary" type="button">&nbsp; Visit the live Intranet &nbsp;</button>'

            if (Backdrop.cookie.get() !== Backdrop.cookie.value) {

                const modal = Backdrop.modal(title, html);
                $("body").prepend(modal);

                $('.heartbeat__modal button.modal-continue')
                .on('click', () => {
                    Backdrop.cookie.set(Backdrop.cookie.value)
                    location.reload();
                })
                .css(Backdrop.style.button.continue);

                $('.heartbeat__modal button.modal-escape')
                .on('click', () => location.href = 'https://intranet.justice.gov.uk/')
                .css(Backdrop.style.button.escape);
            }
        },
        failed: () => {
            const title = 'Your session has expired.'
            const html = '<button class="modal-expired primary" type="button">&nbsp; Refresh &nbsp;</button>'

            if ($('.heartbeat__backdrop').length === 0) {
                const modal = Backdrop.modal(title, html);
                $("body").prepend(modal);

                $('.heartbeat__modal button.modal-expired').
                  on('click', () => location.reload()).
                  css(Backdrop.style.button.escape).css({marginTop:'16px'});
            }
        }
    }

    $(function(){
        if (Backdrop.feature.can_run()) {
            Backdrop.confirm();
            Backdrop.feedback();
        }

        // Send a request to the heartbeat endpoint, this will refresh the oauth token.
        setInterval(function () {
            $.get('/auth/heartbeat').fail(() => {
                Backdrop.failed();
            })
        }, 10000)
    });
})(jQuery);
