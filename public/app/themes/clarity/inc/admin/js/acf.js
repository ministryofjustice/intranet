jQuery(document).ready(function () {
  if (typeof acf === "undefined") {
    return;
  }

  // @see https://www.advancedcustomfields.com/resources/javascript-api/#filters-select2_escape_markup
  acf.add_filter(
    "select2_escape_markup",
    (escaped_value, original_value, _$select, _settings, field) => {
      // If the data type is post_object then the html is post title and date.
      // It's not user input, so we can safely return the original_value here.
      if (field.data("type") === "post_object") {
        return original_value;
      }

      // Otherwise, return the escaped html.
      return escaped_value;
    },
  );

  // Run when ACF's select2 fields are initialized.
  acf.addAction("select2_init", function ($select, args, settings, field) {
    // If we're not dealing with a post_object, return early.
    if (field.data.type !== "post_object") {
      return;
    }

    // Set the title to plain text.
    const updateTitleTag = () => {
      const $element = $select
        .next(".select2")
        .find(".select2-selection__rendered");

      // Add a space before line breaks, to have `text()` preserve a space.
      $element.find("br").before(" ");

      // Set the title property to `text()`.
      $element.attr("title", $element.text());
    };

    // Triggered whenever an option is selected.
    $select.on("select2:select", function (e) {
      // There is no render event, so use change event + 500ms.
      setTimeout(updateTitleTag, "500");
    });

    // Run once on init.
    updateTitleTag();
  });
});
