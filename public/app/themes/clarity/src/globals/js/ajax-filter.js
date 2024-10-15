import AjaxTemplating from "./ajax-templating.js";

export default (function ($) {
  /**
   * Render the response to the page.
   *
   * The results type should match the php response object
   * as returned by the `FilterSearch->mapResults()` method.
   * @typedef {Object} Post
   * @property {string} ID
   * @property {string} post_title
   * @property {string} post_date_formatted
   * @property {string} post_excerpt_formatted
   * @property {string} permalink
   * @property {string} post_type
   * @property {string} post_thumbnail
   * @property {string} post_thumbnail_alt
   *
   * @typedef {Object} Results
   * @property {Post[]} posts
   * @property {string} templateName
   *
   * The response type should match the php response object
   * as returned by the `FilterSearch->loadSearchResults()` method.
   * @typedef {Object} Response
   * @property {Object} aggregates
   * @property {number} aggregates.currentPage
   * @property {number} aggregates.resultsPerPage
   * @property {number} aggregates.totalResults
   * @property {Results} results
   *
   * @param {Response} response
   */

  const renderResults = ({
    results: { posts, templateName },
    aggregates: { currentPage, totalResults },
  }) => {
    // Remove all articles if page is 1.
    if (currentPage === 1) {
      $(".c-article-item, .c-events-item-list").remove();
    }

    const t = new AjaxTemplating(templateName);

    const resultsHtml = posts.map((props) => t.renderHtml(props));

    // Append the html to the content section.
    $("#content").append(resultsHtml);

    // Update the title.
    $("#title-section").text(`${totalResults} search results`);
  };

  /**
   * Render pagination to the page.
   */

  const renderPagination = ({ currentPage, resultsPerPage, totalResults }) => {

    if(resultsPerPage === undefined || resultsPerPage === -1) {
      return;
    }

    const isLastPage = currentPage * resultsPerPage >= totalResults;

    const paginationTitle = ({ totalResults, isLastPage }) => {
      if (!totalResults) {
        return "No Results";
      }
      if (isLastPage) {
        return "No More Results";
      }
      return `Load Next ${resultsPerPage} Results`;
    };

    const template = new AjaxTemplating("pagination");

    // Update the pagination.
    const paginationHtml = template.renderHtml({
      title: paginationTitle({ totalResults, isLastPage }),
      // Disable the button if it's the last page.
      disabled: isLastPage ? `disabled="disabled"` : "",
      // Adjust the zero-indexed current page.
      currentPageFormatted: parseInt(currentPage),
      // Calculate the total pages - if no results, then set as 1 to render '1 of 1'.
      totalPages: !totalResults ? 1 : Math.ceil(totalResults / resultsPerPage),
    });

    $(".c-pagination").html(paginationHtml);

    // Update the page number on the pagination element.
    if (!isLastPage) {
      $(".c-pagination button").attr("data-page", currentPage + 1);
    }
  };

  /**
   * Parse a form instance into an object.
   *
   * @param {HTMLElement} form
   * @returns {[string, string][]}
   */

  const getFormData = (form) => {
    const formData = new FormData(form);

    const prefix = formData.get("prefix");

    // Loop all form entries, remove the prefix from keys, and assign to data.
    const entries = [...formData.entries()]
      .map(([key, value]) => {
        // Remove prefix if it exists.
        const newKey = key.startsWith(prefix) ? key.replace(prefix, "") : key;

        // Parse the page number to integer.
        if ("page" === newKey) {
          value = parseInt(value);
        }

        return [newKey, value];
      })
      .filter(([key, value]) => {
        // Skip prefix and template keys, and empty values.
        if (["prefix"].includes(key) || value === "") {
          return false;
        }

        return true;
      });

    entries.push(["action", $(form).attr("action")]);

    return Object.fromEntries(entries);
  };

  $.fn.moji_ajaxFilter = function () {
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

    const initialFormData = getFormData($form.get(0));

    // On page load get the ajax props and store them on the pagination element.
    $(".c-pagination").data("ajax-props", {
      ...DEFAULT_AJAX_PROPS,
      data: initialFormData,
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
