import AjaxTemplating from "./ajax-templating.js";

/**
 * Parse the values of a form instance into an object.
 *
 * @param {HTMLElement} form
 * @returns {[string, string][]}
 */

export const getFormData = (form) => {
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

/**
 * Render the response to the page.
 *
 * The results type should match the php response object
 * as returned by the `FilterSearch->mapResults()` method.
 *
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
 *
 * @typedef {Object} Response
 * @property {Object} aggregates
 * @property {number} aggregates.currentPage
 * @property {number} aggregates.resultsPerPage
 * @property {number} aggregates.totalResults
 * @property {Results} results
 *
 * @param {Response} response
 */

export const renderResults = ({
  results: { posts, templateName },
  aggregates: { currentPage, resultsPerPage, totalResults },
}) => {
  // Remove all articles if page is 1.
  if (currentPage === 1) {
    $(".c-article-item, .c-events-item-list").remove();
  }

  // Load an instance of the AjaxTemplating class.
  const t = new AjaxTemplating(templateName);

  // Render the html for each post.
  const resultsHtml = posts.map((props) => t.renderHtml(props));

  // Append the html to the content section.
  $("#content").append(resultsHtml);

  // Update the title.
  $("#title-section").text(`${totalResults} search results`);

  // If we are on a page greater than 1, focus on the first new result.
  if (currentPage > 1) {
    const position = (currentPage - 1) * resultsPerPage + 1;
    $("#content").children().eq(position).focus();
  }
};

/**
 * Render pagination to the page.
 *
 * @param {Object} props
 * @param {number} props.currentPage
 * @param {number} props.resultsPerPage
 * @param {number} props.totalResults
 *
 * @returns {void}
 */

export const renderPagination = ({
  currentPage,
  resultsPerPage,
  totalResults,
}) => {
  if (resultsPerPage === undefined || resultsPerPage === -1) {
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
