jQuery(document).ready(function () {
  if (typeof acf === "undefined") {
    return;
  }

  // @see https://www.advancedcustomfields.com/resources/javascript-api/#filters-select2_escape_markup
  acf.add_filter("select2_escape_markup", (escaped_value) => {
    // Let's make use of select2 escaping, but reverse it for a few allowed elements.

    // Allowed elements
    const allowedTags = ["br", "small"];

    // Regex parts.
    const openAngle = "&lt;";
    const closingAngle = "&gt;";
    // An optional closing slash.
    const closingSlash = "(?<closingSlash>/?)";
    // Name of the tag.
    const tagName = `(?<tagName>${allowedTags.join("|")})`;

    // Build up a regex expression, from the defined parts.
    const expression = `${openAngle}${closingSlash}${tagName}${closingAngle}`;

    var regexp = new RegExp(expression, "g");

    return escaped_value.replace(regexp, "<$<closingSlash>$<tagName>>");
  });
});
