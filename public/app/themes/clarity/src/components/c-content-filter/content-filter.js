/* global console */
/* jshint esversion: 6 */
export default (function ($) {
  $.fn.mojContentFilter = function () {
    const $section = this;
    const $toggleButton = $section.find(".c-content-filter__toggle");
    const $form = $section.find("#ff");

    /**
     * Toggle the filter visibility
     *
     * @returns {void}
     */

    function toggleFilter() {
      const isCollapsed = $section
        .toggleClass("c-content-filter--collapsed")
        .hasClass("c-content-filter--collapsed");

      $toggleButton.attr("aria-expanded", !isCollapsed);
    }

    $toggleButton.on("click", toggleFilter);


    /**
     * Hide/close the filter panel
     *
     * @returns {void}
     */

    function closeFilter() {
      const isCollapsed = $section.hasClass("c-content-filter--collapsed");

      if (!isCollapsed) {
        toggleFilter();
      }
    }


    /**
     * Handle a form submit event.
     *
     * @param {Event} e
     * @returns {void}
     */

    $form.on("submit", function (e) {
      e.preventDefault();

      closeFilter();

      const $selected = $('input[name="opg_pillar"]:checked');
      const selectedLabel = $selected.next("label").text();

      if (!$selected?.length || $selected.val() === "any") {
        $section.removeClass("c-content-filter--has-filter");
        $section.find("#ff_state").text(``);
      } else {
        $section.addClass("c-content-filter--has-filter");
        $section.find("#ff_state").text(`Filter applied: ${selectedLabel}`);
      }
    });

    /**
     * Handle a clear click event.
     *
     * @param {Event} e
     * @returns {void}
     */

    $section.find("#ff_clear").on("click", function (e) {
      e.preventDefault();

      $('input[name="opg_pillar"]').prop("checked", false);
      $form.submit();
    });
  };

  return null;
})(jQuery);
