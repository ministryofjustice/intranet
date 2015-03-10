/*global console,ajaxurl,$,jQuery*/

/**
 *
 */
jQuery(function ($) {
    "use strict";

    $("input[type=range]").on('input change', function() {
        console.log($(this).val());
        $(this).next('.pixel_value').html($(this).val() + 'px');
    });


    var codeMirror = CodeMirror.fromTextArea(document.getElementById('codemirror'), {
        tabMode: 'indent',
        lineNumbers: true,
        lineWrapping: true,
        onChange: function(cm) {
            cm.save();
        }
    });

    $(".mm_colorpicker").spectrum({
        preferredFormat: "rgb",
        showInput: true,
        showAlpha: true,
        clickoutFiresChange: true,
        change: function(color) { 
            if (color.getAlpha() === 0) {
                $(this).siblings('div.chosen-color').html('transparent');
            } else {
                $(this).siblings('div.chosen-color').html(color.toRgbString());
            }
        }
    });

    $(".confirm").on("click", function() {
        return confirm(megamenu_theme_editor.confirm);
    });

    $('.icon_dropdown').on("change", function() {
        var icon = $("option:selected", $(this)).attr('data-class');
        // clear and add selected dashicon class
        $(this).next('.selected_icon').removeClass().addClass(icon).addClass('selected_icon');
    });

    $('.nav-tab-wrapper a').on('click', function() {
        $(this).siblings().removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active');
        var tab = $(this).attr('data-tab');
        $('.row').hide();
        $('.row[data-tab=' + tab + ']').show();
    });

});