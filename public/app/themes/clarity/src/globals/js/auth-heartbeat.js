
export default (function ($) {
    const Backdrop = {
        /**
         * cookie object containes methods to set, get and
         * delete the user_accetance cookie.
         */
        cookie: {
            name: 'moj_uat_session',
            value: 'user_accepted',
            set: (value, days = 7) => {
                const expires = new Date( Date.now() + days * 864e5).toUTCString()
                document.cookie = Backdrop.cookie.name + '=' + value + '; expires=' + expires + '; path=/'
            },
            get: () => {
                return document.cookie.split('; ').reduce((r, v) => {
                    const parts = v.split('=')
                    return parts[0] === Backdrop.cookie.name ? parts[1] : r
                }, '')
            },
            delete: () => Backdrop.cookie.set('', -1),
        },
        /**
         * We are not using a style sheet.
         * jQuery is fed properties and values to inline styles for
         * the element it creates
         */
        style: {
            img: {
                maxWidth: '110px',
                height: 'auto',
                float: 'left',
                marginRight: '30px'
            },
            button: {
                continue: {
                    backgroundColor: '#00823b',
                    color: 'white',
                    cursor: 'pointer',
                    // Additional styles for admin view.
                    padding: '9px',
                    border: 'none',
                    textDecoration: 'none'
                },
                escape: {
                    backgroundColor: '#2271b1',
                    color: 'white',
                    padding: '9px',
                    textDecoration: 'none'
                },
                refresh: {
                    backgroundColor: '#00823b',
                    color: 'white',
                    cursor: 'pointer',
                    margin: '16px -15px 5px 0',
                    float: 'right',
                    // Additional styles for admin view.
                    padding: '10px',
                    border: 'none',
                    textDecoration: 'none'
                }
            },
            modal: {
                textAlign:'left',
                padding:'20px 30px 10px',
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
        /**
         * Feature flags...
         */
        feature: {
            /**
             * Prevents the UAT confirm box from running on production
             * The object format used here is for readability
             *
             * @returns {boolean}
             */
            can_run: () => [
                    'intranet.docker',
                    'dev.intranet.justice.gov.uk',
                    'staging.intranet.justice.gov.uk'
                ].includes(location.hostname)
        },
        /**
         * Create a Modal
         *
         * @param title string
         * @param html string
         * @returns {*|jQuery}
         */
        modal: (title, html) => {
            let modal = $('<div\>', { 'class': 'heartbeat__modal' })
                .css(Backdrop.style.modal);

            const backdrop = $('<div\>', { 'class': 'heartbeat__backdrop' })
                .css(Backdrop.style.backdrop);

            // This element is on frontend and admin views. The frontend html has `font-size: 62.5%`, so use px values here.
            const heading = $('<h3\>').text(title).css({fontWeight:'700', fontSize: '20px', lineHeight: '1.2', margin: 0})
            const content = $('<p\>').html(html).css({})
            const image = $('<img>', {
                src: '/app/themes/clarity/dist/images/crown_copyright_logo.png',
                alt: 'Crown copyright logo',
            }).css(Backdrop.style.img);

            modal = modal.append(image, heading, content);

            return backdrop.append(modal);
        },
        /**
         * On first visit to the site, ask the user what they would like to do.
         */
        confirm: () => {
            if (Backdrop.cookie.get() !== Backdrop.cookie.value) {
                const title = 'You are about to view a test version of the MoJ Intranet'
                const html = '<br />To participate in user acceptance testing, please continue.<br /><br />' +
                  '<button class="modal-continue" type="button">&nbsp; Continue &nbsp;</button> &nbsp; or &nbsp; ' +
                  '<a class="modal-escape" href="https://intranet.justice.gov.uk/">&nbsp; Visit the live Intranet &nbsp;</a>'

                // present the modal
                $("body").prepend(Backdrop.modal(title, html));

                $('.heartbeat__modal button.modal-continue')
                .on('click', () => {
                    Backdrop.cookie.set(Backdrop.cookie.value)
                    location.reload();
                })
                .css(Backdrop.style.button.continue);

                $('.heartbeat__modal a.modal-escape')
                .css(Backdrop.style.button.escape);
            }
        },
        /**
         * Let the user know they should refresh the page they're on.
         * The session has expired.
         */
        failed: () => {
            if ($('.heartbeat__backdrop').length === 0) {
                // Set up some different behaviour depending on screen.
                const isAdmin = location.pathname.includes('/wp-admin/');

                // For admin screens, send the user to login in a new tab.
                const linkTarget = isAdmin ? '_blank' : '';
                const linkHref = isAdmin ? `${location.origin}/wp/wp-admin/?heartbeat-modal=success` : location;
                const linkLabel = isAdmin ? 'Login (opens in a new tab)' : 'Reload';
                const linkLabelShort = isAdmin ? 'Login' : 'Reload';

                const title = 'Your session has expired'
                const html = `Please press ‘${linkLabelShort}’ to sign in to the Intranet again.
                    <br>
                    <a 
                        target="${linkTarget}" 
                        class="modal-expired primary" 
                        href="${linkHref}" 
                        type="button">
                        &nbsp; ${linkLabel} &nbsp;
                    </a>`;

                // present the modal
                $("body").prepend(Backdrop.modal(title, html));

                // Add a class to the modal, so that we can remove it when heartbeat is successful.
                $('.heartbeat__backdrop').addClass('heartbeat__backdrop--failed');

                $('.heartbeat__modal a.modal-expired')
                .css(Backdrop.style.button.refresh)
                // Set the loading state on click.
                .on('click', function () {
                    const $link = $(this);
                    $link.text('Loading...');
                    // Revert back to normal state after 60s.
                    setTimeout(
                        function () { $link.text(linkLabel) },
                        60_000,
                    )
                });
            }
        },
        /**
         * Let the user know they should close the tab they're on.
         * They've successfully logged in from an admin screen via a new tab.
         */
        adminSuccess: () => {
            if ($('.heartbeat__backdrop').length === 0) {
                const title = 'You\'ve successfully logged in.'
                const html = `Please close this browser tab to return to where you left off.`;

                // present the modal
                $("body").prepend(Backdrop.modal(title, html));
            }
        }
    }

    $(function(){
        if (Backdrop.feature.can_run()) {
            Backdrop.confirm();
        }

        const urlParams = new URLSearchParams(window.location.search);

        if(urlParams.get('heartbeat-modal') === 'success') {
            Backdrop.adminSuccess();
        }

        // Send a request to the heartbeat endpoint, this will refresh the oauth token.
        setInterval(function () {
            $.get('/auth/heartbeat').fail(() => {
                Backdrop.failed();
            }).done(function() {
                // Remove the failed modal if we have a success.
                if($('.heartbeat__backdrop--failed').length) {
                    $('.heartbeat__backdrop--failed').remove();
                }
            })
        }, 10000)
    });
})(jQuery);
