export default (function ($) {
  /**
   * A helper to render a template with props.
   *
   * @see https://stackoverflow.com/a/39065147/6671505
   *
   * @param {[string, string|number]} props
   * @returns {function(string, number): string}
   */
  const templateRender = (props) => (tok, i) => i % 2 ? props[tok] : tok;

  /**
   * Render the response to the page.
   *
   * The results type should match the php response object
   * as returned by the `FilterSearch->mapResults()` method.
   * @typedef {Object} Result
   * @property {string} ID
   * @property {string} post_title
   * @property {string} post_date_formatted
   * @property {string} post_excerpt_formatted
   * @property {string} permalink
   * @property {string} post_type
   * @property {string} post_thumbnail
   * @property {string} post_thumbnail_alt
   *
   * The response type should match the php response object
   * as returned by the `FilterSearch->loadSearchResults()` method.
   * @typedef {Object} Response
   * @property {Object} aggregates
   * @property {number} aggregates.currentPage
   * @property {number} aggregates.resultsPerPage
   * @property {number} aggregates.totalResults
   * @property {Result[]} results
   *
   * Templates used for rendering the response.
   * @typedef {Object} Templates
   * @property {string[]} result
   * @property {string[]} pagination
   *
   * @param {Response} response
   * @param {Templates} templates
   */

  const renderResponse = (
    { results, aggregates: { currentPage, resultsPerPage, totalResults } },
    templates,
  ) => {
    // Remove all articles if page is 1.
    if (currentPage === 1) {
      $(".c-article-item").remove();
    }

    // Build the html from the response.
    // See https://stackoverflow.com/a/39065147/6671505
    const resultsHtml = results.map((props) =>
      templates.result.map(templateRender(props)).join(""),
    );

    // Append the html to the content section.
    $("#content").append(resultsHtml);

    // Update the title and pagination.
    $("#title-section").text(`${totalResults} search results`);

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

    // Update the pagination.
    const paginationHtml = templates.pagination
      .map(
        templateRender({
          title: paginationTitle({ totalResults, isLastPage }),
          // Disable the button if it's the last page.
          disabled: isLastPage ? `disabled="disabled"` : "",
          // Adjust the zero-indexed current page.
          currentPageFormatted: parseInt(currentPage),
          // Calculate the total pages - if no results, then set as 1 to render '1 of 1'.
          totalPages: !totalResults
            ? 1
            : Math.ceil(totalResults / resultsPerPage),
        }),
      )
      .join("");

    $(".c-pagination").html(paginationHtml);
  };

  const getAjaxProps = (form) => {
    const formData = new FormData(form);

    const prefix = formData.get("prefix");

    const resultTemplateName = formData.get("template");
    const templates = {
      result: $(`script[data-template="${resultTemplateName}"]`)
        .text()
        .split(/\$\{(.+?)\}/g),
      pagination: $(`script[data-template="pagination"]`)
        .text()
        .split(/\$\{(.+?)\}/g),
    };

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
        // Skip prefix and template keys.
        if (["prefix", "template"].includes(key)) {
          return false;
        }

        // Skip empty values.
        if (value === "") {
          return false;
        }

        if (key === "post_type" && value === "posts") {
          console.error("posts needs to transformed to post! or edit the form");
          value = "post";
        }

        return true;
      });

    entries.push(["action", $(form).attr("action")]);

    const data = Object.fromEntries(entries);

    const ajaxProps = {
      type: $(form).attr("method"),
      url: mojAjax.ajaxurl,
      dataType: "json",
      data,
      success: (response) => renderResponse(response, templates),
    };

    return ajaxProps;
  };

  $.fn.moji_ajaxFilter = function () {

    // On page load get the form data and store it on the pagination element.
    const ajaxProps = getAjaxProps($("#ff").get(0));
    // Store form data on the pagination element.
    $(".c-pagination").data("ajax-props", ajaxProps);

    $("#ff").on("submit", function (e) {
      e.preventDefault();

      const ajaxProps = getAjaxProps(this);

      // Store form data on the pagination element.
      $(".c-pagination").data("ajax-props", ajaxProps);

      console.log($(".c-pagination").data("parent-form"));

      $.ajax(ajaxProps);
    });

    $(".c-pagination").on("click keydown", "button", function (e) {
      e.stopPropagation();

      if (e.type === "keydown" && ![13, 32].includes(e.keyCode)) {
        console.log("pressed: ".e.keyCode);
        return;
      }

      const ajaxProps = $(this).closest(".c-pagination").data("ajax-props");

      // if(!ajaxProps) {
      //   // handle the load next button being pressed without a filter.
      //   $("#ff").submit();
      //   return
      // }

      ajaxProps.data.page += 1;

      $.ajax(ajaxProps);
    });

    $(document).on("submit", "#ff_events", function (e) {
      e.preventDefault();

      const nextPageToRetrieve = $("#ff").data("page") + 1;
      $(".more-btn").attr("data-page", nextPageToRetrieve);

      $.ajax({
        type: "post",
        url: mojAjax.ajaxurl,
        dataType: "html",
        data: {
          action: "load_events_filter_results",
          query: $(this).find('input[name="ff_keywords_filter"]').val(),
          valueSelected: $(this).find("#ff_date_filter option:selected").val(),
          postType,
          termID,
          nonce_hash: nonceHash,
        },
        success: function (response) {
          $(".c-article-item").remove();
          $("#content").html(response);
        },
      });
      return false;
    });
  };
})(jQuery);
