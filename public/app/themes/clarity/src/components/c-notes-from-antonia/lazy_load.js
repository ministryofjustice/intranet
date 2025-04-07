/* notes-From-Antonia function */
export default (function ($) {

    $.fn.imgLoad = function (callback) {
        return this.each(function () {
            if (callback) {
                if (this.complete || /*for IE 10-*/ $(this).height() > 0) {
                    callback.apply(this);
                } else {
                    $(this).on('load', function () {
                        callback.apply(this);
                    });
                }
            }
        });
    };

    $.fn.notesFromAntonia_getNote = function () {

        let notes = [];

        $('article.c-article-item__note-from-antonia').each(function (k, v) {
            notes[k] = v;
        });

        /**
         * Load the note content via AJAX
         * and replace the content of the note
         * with the response.
         * 
         * @param note
         * @returns {boolean}
         */

        function loadNote(note) {
            let id = $(note).attr('id');
            const preloader = '<span class="pre-loading" aria-busy="true"></span>';
            $(note).html('<strong class="align-middle">' + mojAjax.page_settings.loading_message + ' ' + preloader + '</strong>');
            $(note).addClass('v-align');

            $.ajax({
                url: mojAjax.ajaxurl,
                data: {action: 'get_note_from_antonia', notes_id: id.split('-')[1]},
                complete: () => {
                    // hash match scrolling
                    // does a hash exist in the URL?
                    let url = window.location.href;
                    let hash = url.split("#");

                    // scroll to view
                    if (hash.length > 1 && id === hash[1]) {
                        setTimeout(
                            () => {
                                const $article = $('#' + hash[1]);
                                let offset = $article.offset().top;
                                if (mojAjax.page_settings.scroll_to_view.active) {
                                    $('html,body').animate({
                                        scrollTop: offset
                                    }, mojAjax.page_settings.scroll_to_view.speed);
                                } else {
                                    $article.scrollTop(offset);
                                }
                            },
                            mojAjax.page_settings.scroll_to_view.delay
                        );
                    }
                },
                success: (response) => {
                    $(note).removeClass('v-align');
                    $(note).html(response);

                    // wait for image load and fade
                    if (mojAjax.page_settings.image_load.active) {
                        $(note).find('img').hide().imgLoad(function () {
                            $(this).fadeIn(mojAjax.page_settings.image_load.fade_in);
                        });
                    }
                },
                dataType: 'html'
            });
        }

        // Load all notes if URL fragment is #load-archive
        if (window.location.href.indexOf('#load-archive') > -1) {
            notes.forEach((note) => {
                loadNote(note);
            });
            // Return early
            return true;
        }

        detectNotes();
        $(window).scroll(detectNotes);

        function detectNotes() {
            notes.forEach((note, index) => {
                if (isInViewport(note, 100)) {
                    loadNote(note);

                    delete notes[index];
                }
            });
        };

        /**
         * Tests if the element is in the viewport.
         * Uses padding to essentially extend the boundaries
         * of the element, forcing content to load earlier.
         *
         * @param element
         * @param padding pixel value
         * @returns {boolean}
         */
        function isInViewport(element, padding) {
            const rect = element.getBoundingClientRect();
            padding = padding || 400;

            return (
                rect.top >= -padding &&
                rect.left >= 0 &&
                (rect.bottom -padding) <= (window.innerHeight || document.documentElement.clientHeight) &&
                rect.right <= (window.innerWidth || document.documentElement.clientWidth)
            );
        }

        return true;
    }
})(jQuery);
