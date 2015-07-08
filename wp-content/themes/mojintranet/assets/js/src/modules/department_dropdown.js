(function($) {
  "use strict";

  var App = window.App;

  App.DepartmentDropdown = function() {
    this.$myIntranetForm = $('.my-intranet-form');
    if(!this.$myIntranetForm.length) { return; }
    this.init();
  };

  App.DepartmentDropdown.prototype = {
    init: function() {
      this.settings = {
        cookieName: 'department_dropdown'
      };

      this.cacheEls();
      this.bindEvents();
      this.setDropdown();
    },

    cacheEls: function() {
      this.$departmentDropdown = this.$myIntranetForm.find('.department');
      this.$visitCta = this.$myIntranetForm.find('.visit-cta');
    },

    bindEvents: function() {
      this.$myIntranetForm.on('submit', $.proxy(this.visitDepartment, this));
      this.$myIntranetForm.find('.department').on('change', $.proxy(this.saveState, this));
    },

    setDropdown: function() {
      var department = this.readState();
      this.$departmentDropdown.find('[data-department="' + department + '"]').attr('selected', true);
    },

    visitDepartment: function(e) {
      var $form = $(e.currentTarget);
      var selectedDepartmentUrl = $form.closest('.my-intranet-form').find('.department :selected').attr('data-url');

      e.preventDefault();

      if(selectedDepartmentUrl) {
        window.location.href = selectedDepartmentUrl;
      }
    },

    saveState: function(e) {
      var department = $(e.currentTarget).find(':selected').attr('data-department');
      App.tools.setCookie(this.settings.cookieName, department, 3650);
    },

    readState: function() {
      return App.tools.getCookie(this.settings.cookieName);
    }
  };
}(window.jQuery));
