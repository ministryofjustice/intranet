export default (function ($) {
  $.fn.moji_ajaxFilter = function () {
    const postType = $(".data-type").data("type");
    const termID = $(".l-secondary").data("termid");
    const nonceHash = $("#_search_filter_wpnonce").val();

    $(document).on("submit", "#ff", function (e) {
      e.preventDefault();

      const nextPageToRetrieve = $("#ff").data("page") + 1;
      $(".more-btn").attr("data-page", nextPageToRetrieve);

      const valueSelected = $(this)
        .find(
          "#ff_date_filter option:selected, #ff_region_news_date_filter option:selected",
        )
        ?.val();
      const newsCategoryValue =
        $(this)
          .find(
            '#ff_categories_filter_e-news, input[name="ff_categories_filter_regions"]',
          )
          ?.val() ?? 0;

      $.ajax({
        type: "post",
        url: mojAjax.ajaxurl,
        dataType: "json",
        data: {
          action: "load_search_results",
          query: $(this).find("#ff_keywords_filter").val(),
          nextPageToRetrieve,
          valueSelected,
          postType,
          newsCategoryValue,
          termID,
          nonce_hash: nonceHash,
        },
        success: function (response) {
          $(".c-article-item").remove();
          $("#content").html(response.results);
          $("#title-section").html(response.total);
          $(".c-pagination").html(response.pagination);
        },
      });

      return false;
    });

    $(".c-pagination").on("click", function () {
      $("#load_more div.data-type").addClass("shown-item");

      const nextPageToRetrieve = $(".more-btn").data("page") + 1;
      $(".more-btn").attr("data-page", nextPageToRetrieve);

      const newsCategoryValue =
        $('input[name="ff_categories_filter_news-category"]:checked')?.val() ??
        0;

      $.ajax({
        type: "post",
        url: mojAjax.ajaxurl,
        dataType: "json",
        data: {
          action: "load_search_results",
          query: $(this).find("#ff_keywords_filter").val(),
          valueSelected: $(".more-btn, .nomore-btn").data("date"),
          nextPageToRetrieve,
          postType,
          newsCategoryValue,
          termID,
          nonce_hash: nonceHash,
        },

        success: function (response) {
          $("#load_more").append(response.results);
          $(".c-pagination").html(response.pagination);
          $(
            "#load_more div.data-type:not('.shown-item')+article div.content a",
          ).focus();
        },
      });

      return false;
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
