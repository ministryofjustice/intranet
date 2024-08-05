/**
 Script loader
 In order to avoid performance issues, scripts are not automatically
 loaded when a component is generated. You must explicitly import and execute your scripts here.
 */

// component script registration
import "../../components/c-feedback-form/feedback-form.js";
import "../../components/c-gallery-lightbox/feature-video.js";
import "../../components/c-left-hand-menu/left-hand-menu.js";
import "../../components/c-tabbed-content/tabbed-content.js";
import "../../components/c-input-container/on-change.js";
import "../../components/c-notes-from-antonia/lazy_load.js";

// Global scripts
import "../../../inc/admin/js/feedback.js";
import "./auth-heartbeat.js";
import "./blog-content_filter.js";
import "./condolences-filter.js";
import "./equaliser.js";
import "./slider.js";

/**
 You can attach a script to any element but please put a js- class for any hooks to ensure future proofing.
 */
jQuery(function ($) {
    "use strict";

    // Let css know that JavaScript has loaded successfully
    $('html').removeClass('no-js').addClass('js');

    // Load global scripts
    $('.js-left-hand-menu').moji_leftHandMenu();
    $('.js-feature-video').moji_featureVideo();
    $('.js-radios-onChange').mojRadiosOnChange();
    $('.c-news-list > .js-article-item').moji_equaliser();

    // Load component scripts
    $('.js-tabbed-content-container').mojTabbedContent();
    $('.js-reveal').moji_feedbackForm();
    $('.js-blog-content-ajaxfilter').moji_ajaxFilter();
    $('.js-condolences-filter').moji_condolencesFilter();
    $('.js-notes-from-antonia').notesFromAntonia_getNote();
});
