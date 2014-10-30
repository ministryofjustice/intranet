/*global console,ajaxurl,$,jQuery*/

/**
 * Mega Menu jQuery Plugin
 * @todo sort out widget.
 */
(function ($) {
    "use strict";

    $.fn.megaMenu = function (options) {

        var panel = $("<div />")
            .attr("id", "panel");

        panel.settings = $.extend({
            menu_item_id: $(this).attr("data-menu-item-id"),
            width: "790px",
            height: "80%",
            cols: 6
        }, options);


        panel.log = function (message) {
            if (window.console && console.log) {
                console.log(message);
            }

            if (message == -1) {
                alert(megamenu.nonce_check_failed);
            }
        };


        panel.init = function () {
            panel.log(megamenu.debug_launched + " " + panel.settings.menu_item_id);

            panel.add_widget_selector();
            panel.add_widget_area();

            $.colorbox({
                html: panel,
                innerWidth: panel.settings.width,
                innerHeight: panel.settings.height,
                scrolling: true
            });
        };


        panel.add_widget_selector = function () {

            panel.widget_selector = $("<select />")
                .attr("id", "widget_selector");

            // popular widget selector dropdown
            $.post(ajaxurl, {
                action: "mm_get_available_widgets",
                _wpnonce: megamenu.nonce
            }, function (response) {
                var data = $.parseJSON(response);

                panel.widget_selector.ddslick({
                    data: data,
                    width: 350,
                    height: 300,
                    background: "transparent",
                    imagePosition: "left",
                    selectText: megamenu.select_a_widget,
                    onSelected: function (data) {
                        var postdata = {
                            action: "mm_add_widget",
                            id_base: data.selectedData.value,
                            menu_item_id: panel.settings.menu_item_id,
                            _wpnonce: megamenu.nonce
                        };

                        $.post(ajaxurl, postdata, function (select_response) {
                            panel.add_widget(data.selectedData.text, 2, $.trim(select_response));
                            panel.log(select_response);
                        });
                    }
                });
            });

            panel.append(panel.widget_selector);

        };


        panel.add_widget_area = function () {

            panel.widget_area = $("<div />")
                .attr("id", "widgets")
                .sortable({
                    forcePlaceholderSize: true,
                    placeholder: "drop-area",
                    start: function (event, ui) {
                        $(".widget").removeClass("open");
                        ui.item.data('start_pos', ui.item.index());
                    },
                    stop: function (event, ui) {
                        // clean up
                        ui.item.removeAttr('style');

                        var start_pos = ui.item.data('start_pos');

                        if (start_pos !== ui.item.index()) {
                            ui.item.trigger("on_drop");
                        }
                    }
                });


            $.post(ajaxurl, {
                action: "mm_get_panel_widgets",
                menu_item_id: panel.settings.menu_item_id,
                _wpnonce: megamenu.nonce
            }, function (get_widgets_response) {
                var response = $.parseJSON(get_widgets_response);

                $.each(response, function (i, item) {
                    panel.add_widget(item.title, item.mega_columns, item.widget_id);
                    panel.log(megamenu.debug_added + " " + item.title);
                });
            });

            panel.append(panel.widget_area);

        };


        panel.add_widget = function (title, columns, widget_id) {

            var widget_spinner = $("<span class='spinner' style='display: none;'></span>");

            var widget = $("<div />")
                .addClass("widget")
                .attr("data-columns", columns)
                .bind("on_drop", function () {

                    widget_spinner.show();

                    var position = $(this).index();

                    $.post(ajaxurl, {
                        action: "mm_move_widget",
                        widget_id: widget_id,
                        position: position,
                        menu_item_id: panel.settings.menu_item_id,
                        _wpnonce: megamenu.nonce
                    }, function (move_response) {
                        widget_spinner.hide();
                        panel.log(move_response);
                    });
                });

            var widget_top = $("<div />")
                .addClass("widget-top");

            var widget_title = $("<div />")
                .addClass("widget-title")
                .html("<h4>" + title + "</h4>")
                .append(widget_spinner);

            var widget_inner = $("<div />")
                .addClass("widget-inner");

            var widget_title_action = $("<div />")
                .addClass("widget-title-action");

            var expand = $("<a />")
                .addClass("widget-option widget-expand")
                .on("click", function () {
                    var cols = parseInt(widget.attr("data-columns"), 10);

                    if (cols < panel.settings.cols) {
                        cols = cols + 1;

                        widget.attr("data-columns", cols);

                        widget_spinner.show();

                        $.post(ajaxurl, {
                            action: "mm_update_columns",
                            widget_id: widget_id,
                            columns: cols,
                            _wpnonce: megamenu.nonce
                        }, function (expand_response) {
                            widget_spinner.hide();
                            panel.log(expand_response);
                        });
                    }

                });

            var contract = $("<a />")
                .addClass("widget-option widget-contract")
                .on("click", function () {
                    var cols = parseInt(widget.attr("data-columns"), 10);

                    if (cols > 0) {
                        cols = cols - 1;
                        widget.attr("data-columns", cols);
                    }

                    widget_spinner.show();

                    $.post(ajaxurl, {
                        action: "mm_update_columns",
                        widget_id: widget_id,
                        columns: cols,
                        _wpnonce: megamenu.nonce
                    }, function (contract_response) {
                        widget_spinner.hide();
                        panel.log(contract_response);
                    });

                });

            var edit = $("<a />")
                .addClass("widget-option widget-edit")
                .on("click", function () {

                    if (!widget.hasClass("open") && !widget.data("loaded")) {

                        widget_spinner.show();

                        // retrieve the widget settings form
                        $.post(ajaxurl, {
                            action: "mm_edit_widget",
                            widget_id: widget_id,
                            _wpnonce: megamenu.nonce
                        }, function (form) {

                            var $form = $(form);

                            // bind delete button action
                            $(".delete", $form).on("click", function (e) {
                                e.preventDefault();

                                var data = {
                                    action: "mm_delete_widget",
                                    widget_id: widget_id,
                                    _wpnonce: megamenu.nonce
                                };

                                $.post(ajaxurl, data, function (delete_response) {
                                    widget.remove();
                                    panel.log(delete_response);
                                });

                            });

                            // bind close button action
                            $(".close", $form).on("click", function (e) {
                                e.preventDefault();

                                widget.toggleClass("open");
                            });

                            // bind save button action
                            $form.on("submit", function (e) {
                                e.preventDefault();

                                var data = $(this).serialize();

                                $(".spinner", $form).show();

                                $.post(ajaxurl, data, function (submit_response) {
                                    $(".spinner", $form).hide();
                                    panel.log(submit_response);
                                });

                            });

                            widget_inner.html($form);

                            widget.data("loaded", true).toggleClass("open");

                            widget_spinner.hide();
                        });

                    } else {
                        widget.toggleClass("open");
                    }

                    // close all other widgets
                    $(".widget").not(widget).removeClass("open");

                });


            var output = widget.html(
                    widget_top
                        .html(
                            widget_title_action
                                .append(contract)
                                .append(expand)
                                .append(edit)
                        )
                        .append(widget_title)
                    )
                    .append(widget_inner);

            panel.widget_area.append(output);
        };

        panel.init();

    };

}(jQuery));

/**
 *
 */
jQuery(function ($) {
    "use strict";

    $(".megamenu_launch").live("click", function (e) {
        e.preventDefault();

        $(this).megaMenu();
    });

    $('#megamenu_accordion').accordion({
        heightStyle: "content", 
        collapsible: true,
        active: false,
        animate: 200
    });

    $('.dashicon_dropdown').on("change", function() {
        var icon = $("option:selected", $(this)).attr('data-class');
        // clear and add selected dashicon class
        $(this).prev('.selected_icon').removeClass().addClass(icon).addClass('selected_icon');
    });

});