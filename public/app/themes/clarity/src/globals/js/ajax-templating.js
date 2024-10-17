/**
 * This class is responsible for rendering HTML from an AJAX template.
 *
 * This is an extension of a bare bones templating engine.
 * @see https://stackoverflow.com/a/39065147/6671505
 *
 * @example
 * Template:
 * <script type="text/template" data-template="results-template">
 *  <div class="result">
 *    <h2>${title}</h2>
 *    <p>${content}</p>
 *    ${?imgSrc}
 *      <img src="${imgSrc}" alt="${title}" />
 *    ${/?imgSrc}
 * </div>
 *
 * Implementation:
 * const template = new AjaxTemplating("results-template");
 * const html = template.renderHtml({
 *  title: "Hello World",
 *  content: "This is a test.",
 *  imgSrc: "https://example.com/image.jpg",
 * });
 *
 */

export default class AjaxTemplating {
  /**
   * Constructor
   *
   * @param {string} templateName
   */
  constructor(templateName) {
    /**
     * The template from the DOM.
     *
     * An array of strings where:
     * - every odd index is a template variable
     * - every even is a string of html text
     *
     * @type {string[]}
     */
    this.resultsTemplate = document
      .querySelector(`script[data-template="${templateName}"]`)
      .textContent.split(/\$\{(.+?)\}/g);
  }

  /**
   * Render the HTML from the template and props.
   *
   * @param {Object} props
   * @returns {string}
   */

  renderHtml(props) {
    // Keep track of the conditional blocks
    // If a conditional block is not met, we skip the block
    let skip = 0;

    return this.resultsTemplate
      .map((tok, i) => {
        // Handle the html text - even indexes

        if (i % 2 === 0) {
          return skip ? "" : tok;
        }

        // Handle the template variables - odd indexes

        if (tok.startsWith("?") && !props[tok.substring(1)]) {
          skip++;
        }

        if (tok.startsWith("/?") && !props[tok.substring(2)]) {
          skip--;
        }

        return skip ? "" : props[tok];
      })
      .join("");
  }
}
