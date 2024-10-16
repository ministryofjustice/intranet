import { getFormData, renderResults, renderPagination } from "./ajax-utils.js";

export default (function ($) {
  $.fn.moji_ajaxFilter = function () {
    // Default ajax props.
    const DEFAULT_AJAX_PROPS = {
      type: "POST",
      url: mojAjax.ajaxurl,
      dataType: "json",
      success: (response) => {
        renderResults(response);
        renderPagination(response.aggregates);
      },
    };

    const $form = $("#ff, #ff_events");

    // On page load get the ajax props and store them on the pagination element.
    $(".c-pagination").data("ajax-props", {
      ...DEFAULT_AJAX_PROPS,
      data: getFormData($form.get(0)),
    });

    /**
     * Handle a form submit event.
     *
     * @param {Event} e
     * @returns {void}
     */

    $form.on("submit", function (e) {
      e.preventDefault();

      const ajaxProps = {
        ...DEFAULT_AJAX_PROPS,
        data: getFormData(this),
      };

      // Make the ajax request.
      $.ajax(ajaxProps);

      // Store the ajax props on the pagination element.
      $(".c-pagination").data("ajax-props", ajaxProps);
    });

    /**
     * Handle events on the pagination button.
     *
     * @param {Event} e
     * @returns {void}
     */

    $(".c-pagination").on("click keydown", "button", function (e) {
      e.stopPropagation();

      // If the event is a keydown event, only allow enter or space keys.
      if (e.type === "keydown" && ![13, 32].includes(e.keyCode)) {
        return;
      }

      // Get the ajax props from the pagination element.
      const ajaxProps = $(this).closest(".c-pagination").data("ajax-props");

      // Get the page number from the button.
      const page = $(this).data("page");

      // Make an ajax request with the new page number.
      $.ajax({ ...ajaxProps, data: { ...ajaxProps.data, page } });
    });
  };
})(jQuery);
