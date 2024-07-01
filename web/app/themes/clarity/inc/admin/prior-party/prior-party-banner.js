/**
 * default data object
 */
jQuery(document).ready(function ($) {
    const JQ = {
        all: $('#ppb-posts'),
        header: $('.ppb-posts__row.header'),
        rows: $('.ppb-posts__row:not(.header)'),
        fixed: $('#header-fixed'),
        banners: {
            row: $('.ppb-banners__row')
        },
        search: $("#search-input")
    };

    const MOJ_PPB = {
        on: {
            scroll: function (e) {
                const pbbPosts = JQ.all.offset().top - 32;
                const fixed  = JQ.fixed.append(JQ.header.clone());

                // init
                fixed.hide();
                $(window).bind("scroll", function () {
                    const offset = $(this).scrollTop();
                    const width = JQ.header.outerWidth();

                    if (offset >= pbbPosts && fixed.is(":hidden")) {
                        fixed.css({width: width + 'px'}).show();
                    } else if (offset < pbbPosts) {
                        fixed.hide();
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
            JQ.search.keyup(this.delay(function () {
                if (this.value.length < 3) {
                    JQ.rows.show();
                    return;
                }

                // split the current value of searchInput
                const data = this.value.toUpperCase().split(' ')

                if (this.value === "") {
                    JQ.rows.show();
                    return;
                }

                //hide all the rows
                JQ.rows.hide();

                //Recursively filter the jquery object to get results.
                JQ.rows.filter(function (i, v) {
                    var $t = $(this);
                    for (var d = 0; d < data.length; ++d) {
                        if ($t.text().toUpperCase().indexOf(data[d]) > -1) {
                            return true;
                        }
                    }
                    return false;
                }).show();
            }, 500)).focus(function () {
                this.value = "";
                $(this).css({
                    "color": "#1d1d1d"
                });
                $(this).unbind('focus');
            }).css({
                "color": "#C0C0C0"
            });
        }
    }

    /**
     * React to clicks on banners, redirect to preview
     */
    JQ.banners.row.on('click', function (e) {
        // redirect to post preview...
        window.location.href = window.location.href + '&' +
            $.param({ 'ref': $(this).data('reference') })
    })

    /**
     * reconcile status
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
     * react to clicks on Post table, add the ID to the exclude array
     */
    JQ.rows.on('click', function (e) {
        const _this = $(this);
        const post_id = _this.data('id');
        const status  = _this.find('.ppb-posts__status');

        if (_this.attr('disabled') === 'disabled') {
            return;
        }

        _this.attr('disabled', 'disabled');

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

            _this.attr('disabled', null);

            status.removeClass(response.message.old);
            status.addClass(response.message.new);

            if (response.message.new === 'tick') {
                _this.removeClass('excluded').attr('title', null);
                return true;
            }

            _this.addClass('excluded').attr('title', 'The banner will not be shown');
        });
    });

    JQ.rows.find('.nav-link').on('click', function (e) {
        e.stopPropagation();
    });

    MOJ_PPB.on.scroll();
    MOJ_PPB.on.resize();
    MOJ_PPB.filter();

});
