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
});
