export default (function ($) {
  $(function () {
    $("body").append(`
            <dialog>
                <button autofocus>Close</button>
                <p>This modal dialog has a groovy backdrop!</p>
            </dialog>
            <button>Show the dialog</button>
          `);
    console.log("ready!");

    const dialog = document.querySelector("dialog");
    const showButton = document.querySelector("dialog + button");
    const closeButton = document.querySelector("dialog button");

    // "Show the dialog" button opens the dialog modally
    showButton.addEventListener("click", () => {
    dialog.showModal();
    });

    // "Close" button closes the dialog
    closeButton.addEventListener("click", () => {
    dialog.close();
    });

  });

  console.log("hi");

  function showModal() {
    console.log("in showModal");
  }

  // Send a request to the heartbeat endpoint, this will refresh the oauth token.
  setInterval(function () {
    jQuery.ajax({
      url: "/auth/heartbeat",
      error: function (xhr, status) {
        showModal();
      },
    });
  }, 10000);
})(jQuery);
