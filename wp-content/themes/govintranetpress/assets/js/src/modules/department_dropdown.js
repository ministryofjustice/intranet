(function($) {
  "use strict";

  var App = window.App;

  App.DepartmentDropdown = function() {
    this.$departmentDropdownBox = $('.department-dropdown-box');
    if(!this.$departmentDropdownBox.length) { return; }
    this.init();
  };

  App.DepartmentDropdown.prototype = {
    init: function() {
      this.cacheEls();
      this.bindEvents();
    },

    cacheEls: function() {
      this.$departmentDropdown = this.$departmentDropdownBox.find('.department');
    },

    bindEvents: function() {
      this.$departmentDropdown.on('change keyup', $.proxy(this.changeDepartmentHandle, this));
      //$(window).on('DOMContentLoaded load', $.proxy(this.changeDepartmentHandle, this));
    },

    changeDepartmentHandle: function(e) {
      var selectedDropdown = $(e.currentTarget);
      var url = selectedDropdown.find('option:selected').attr('data-url');

      if(url) {
        window.location.href = url;
      }

      //var deptName = this.$departmentDropdown.find('option:selected').attr('data-department');
      //this.$departmentDropdownBox.attr('data-department', deptName);
    }
  };
}(window.jQuery));
