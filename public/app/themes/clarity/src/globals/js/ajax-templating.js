/**
 * This class is responsible for rendering HTML from an AJAX template.
 *
 * This is an extension of a bare bones templating engine.
 * @see https://stackoverflow.com/a/39065147/6671505
 *
 * @example
 * const template = new AjaxTemplating("results-template");
 * const html = template.renderHtml({
 *  title: "Hello World",
 *  content: "This is a test."
 * });
 */

export default class AjaxTemplating {
  /**
   * Constructor
   *
   * @param {string} templateName
   */
  constructor(templateName) {
    this.resultsTemplate = this.loadTemplate(templateName);
  }

  /**
   * Load the template from the DOM.
   *
   * Returns an array of strings where:
   * - every odd index is a template variable
   * - every even is a string of html text
   *
   * @param {string} templateName
   * @returns {string[]}
   */

  loadTemplate(templateName) {
    // do this without jQuery
    return document
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
    let skip = false;

    let parts = [];

    for (let i = 0; i < this.resultsTemplate.length; i++) {
      const tok = this.resultsTemplate[i];

      // Handle the html text - even indexes

      if (i % 2 === 0 && !skip) {
        parts.push(tok);
        continue;
      }

      if (i % 2 === 0) {
        continue;
      }

      // Handle the template variables - odd indexes

      if (tok.startsWith("?") && !props[tok.substring(1)]) {
        skip = true;
      }

      if (tok.startsWith("/?") && !props[tok.substring(2)]) {
        skip = false;
      }

      if (!skip) {
        parts.push(props[tok]);
      }
    }

    return parts.join("");
  }
}
