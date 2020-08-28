/**
 Script loader
 In order to avoid performance issues, scripts are not automatically
 loaded when a component is generated. You must explicitly import and execute your scripts here.
 */

// component script registration
import "../../components/c-feedback-form/feedback-form";
import "../../components/c-gallery-lightbox/feature-video";
import "../../components/c-left-hand-menu/left-hand-menu";
import "../../components/c-tabbed-content/tabbed-content";

// Global scripts
import "./blog-content_filter";
import "./condolences-filter";
import "./equaliser";
import "./slider";

/**
 You can attach a script to any element but please put a js- class for any hooks to ensure future proofing.
 */
jQuery(function ($) {
    // Let css know that JavaScript has loaded successfully
    $('html').removeClass('no-js').addClass('js')

    // Load global scripts
    $('.js-left-hand-menu').moji_leftHandMenu()
    $('.js-feature-video').moji_featureVideo()
    $('.c-news-list > .js-article-item').moji_equaliser()

    // Load component scripts
    $('.js-tabbed-content-container').moji_tabbedContent()
    $('.js-reveal').moji_feedbackForm()
    $('.js-blog-content-ajaxfilter').moji_ajaxFilter()
    $('.js-condolences-filter').moji_condolencesFilter()
});
