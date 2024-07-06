/**
 * Feature frontend functionality
 */
jQuery(document).ready(function ($) {
    /**
     * jQuery object store
     * Provides an organised way to cache objects
     *
     * @type {jQuery|HTMLElement|*}
     */
    const JQ = {
        all: $('#ppb-posts'),
        header: $('.ppb-posts__row.header'),
        rows: $('.ppb-posts__row:not(.header)'),
        fixed: $('#header-fixed'),
        banners: {
            row: $('.ppb-banners__row')
        },
        search: {
            input: $('#search-input'),
            clear: $('#clear-filter')
        },
        top: $('#back-to-top'),
        totalCount: $('#total-count')
    };

    /**
     * Feature functions
     * Provides an organised way to store functions to execute on the application
     *
     * @type Object
     */
    const MOJ_PPB = {
        on: {
            scroll: function () {
                const pbbPosts = JQ.all.offset().top - 32;
                const fixed  = JQ.fixed.append(JQ.header.clone());
                const backToTop = JQ.top;

                // init
                fixed.hide();
                $(window).bind("scroll", function () {
                    const offset = $(this).scrollTop();
                    const width = JQ.header.outerWidth();

                    if (offset >= pbbPosts && fixed.is(":hidden")) {
                        fixed.css({width: width + 'px'}).show();

                        MOJ_PPB.toTop.display(); // back to top
                    } else if (offset < pbbPosts) {
                        fixed.hide();
                        backToTop.fadeOut();
                    }
                });
            },
            resize: function () {
                $(window).bind("resize", function () {
                    const width = JQ.header.outerWidth();

                    if (JQ.fixed.is(":visible")) {
                        JQ.fixed.css({width: width + 'px'})
                    }
                });
            }
        },
        delay: function (callback, ms) {
            let timer = 0
            return function () {
                const context = this, args = arguments
                clearTimeout(timer);
                timer = setTimeout(function () {
                    callback.apply(context, args);
                }, ms || 0);
            };
        },
        filter: function () {
            const delay_time = 400; // milliseconds
            let remaining = JQ.rows.length;

            JQ.search.clear.addClass('disabled').on('click', function (e) {
                e.preventDefault();
                JQ.search.input.val("").keyup().focus();
            });


            JQ.search.input.keyup(this.delay(function () {
                if (this.value.length < 2) {
                    JQ.totalCount.text(JQ.rows.length);
                    JQ.search.clear.addClass('disabled');
                    JQ.rows.show();
                    return;
                }

                // split the current value of searchInput
                const data = this.value.toUpperCase().split(' ')

                if (this.value === "") {
                    JQ.totalCount.text(JQ.rows.length);
                    JQ.search.clear.addClass('disabled');
                    JQ.rows.show();
                    return;
                }

                // hide all the rows
                JQ.rows.hide();

                // activate the clear button
                JQ.search.clear.removeClass('disabled');

                // Recursively filter the jquery object to get results.
                remaining = JQ.rows.filter(function (i, v) {
                    const $t = $(this)
                    let d = 0;
                    for (d; d < data.length; ++d) {
                        if ($t.text().toUpperCase().indexOf(data[d]) > -1) {
                            return true;
                        }
                    }
                    return false;
                }).show().length;

                JQ.totalCount.text(remaining);

            }, delay_time)).focus(function () {
                this.value = "";
                $(this).css({
                    "color": "#1d1d1d"
                });
                $(this).unbind('focus');
            }).css({
                "color": "#C0C0C0"
            });
        },
        toTop: {
            init: () => {
                JQ.top.hide();
                JQ.top.on('click', function (e) {
                    const topOfBanner = $('.prior-party-banner').offset().top - 50;
                    window.scrollTo({top: topOfBanner, behavior: 'smooth'});
                });
            },
            display: () => {
                JQ.top.fadeIn();
            }
        }
    }

    /**
     * Initialise post filter
     */
    MOJ_PPB.filter();

    /**
     * React to clicks on banners, redirect to preview
     */
    JQ.banners.row.on('click', function (e) {
        // redirect to post preview...
        window.location.href = window.location.href + '&' +
            $.param({ 'ref': $(this).data('reference') })
    })

    /**
     * Reconcile status
     */
    JQ.rows.find('.ppb-posts__status').each(function (key, element) {
        console.log('Data', {key: key, value: element})
        if ($(element).data('status') === 'on') {
            $(element).addClass('tick');
            return;
        }

        $(element).addClass('cross');
        $(element).parent().addClass('excluded');
    })

    /**
     * React to clicks on Post table
     */
    JQ.rows.on('click', function (e) {
        const _this = $(this);
        const post_id = _this.data('id');
        const status  = _this.find('.ppb-posts__status');

        if (_this.attr('disabled') === 'disabled') {
            return;
        }

        _this.attr('disabled', 'disabled');
        status.addClass('hide-icon').html('<span class="ppb-loader"></span>');

        $.ajax({
            url: wpApiSettings.root + 'prior-party/v2/update',
            method: 'GET',
            beforeSend: function ( xhr ) {
                xhr.setRequestHeader('X-WP-Nonce', wpApiSettings.nonce);
            },
            data:{
                'status' : status.hasClass('tick') ? 'on' : 'off',
                'id': post_id
            }
        }).done(function ( response ) {
            response = JSON.parse(response);
            status.html('');

            _this.attr('disabled', null);

            status.removeClass('hide-icon ' + response.message.old);
            status.addClass(response.message.new);

            if (response.message.new === 'tick') {
                _this.removeClass('excluded').attr('title', null);
                return true;
            }

            _this.addClass('excluded').attr('title', 'The banner will not be shown');
        });
    });

    /**
     * When the row is in focus, the enter key will cause a click event
     */
    JQ.rows.on('keydown', function (e) {
        if (e.which === 13) {
            $(this).click();
        }
    });

    /**
     * If we are on a row, is the tabbed element near the bottom?
     * If so, move the interface so tabbing can stay close to the eye
     */
    JQ.rows.on('keydown', function (e) {
        if (e.which === 9) {
            const element_top = $(this).offset().top;
            const viewport_top = $(window).scrollTop();
            const viewport_bottom = viewport_top + $(window).height();

            if ((viewport_bottom - element_top) < (window.innerHeight / 2)) {
                const upto = window.innerHeight / 2 + viewport_top - 150;
                window.scrollTo({top: upto, behavior: 'smooth'});
            }
        }
    });

    /**
     * Prevent bubbling on post rows when a user clicks a link
     *
     * This was implemented to fix a bug where a link click
     * also toggled the banners visibility status
     */
    JQ.rows.find('.nav-link a, span.event-data').on('click', function (e) {
        e.stopPropagation();
    });

    /**
     * Initialise scroll and resize listeners
     */
    MOJ_PPB.on.scroll();
    MOJ_PPB.on.resize();

    /**
     * Initialise to-top button
     */
    MOJ_PPB.toTop.init();

});
