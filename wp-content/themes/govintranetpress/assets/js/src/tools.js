/** App tools - generic set of tools used across the whole application
 */
(function($) {
  "use strict";

  var App = window.App;

  var settings = {
    sizeUnits: ['B', 'KB', 'MB', 'GB', 'TB', 'PB']
  };

  App.tools = {
    /** Rounds a number with a specified precision
     * @param {Number} num Input number
     * @param {Number} precision Number of decimal places
     * @returns {Number} Rounded number
     */
    round: function(num, precision) {
      var p;

      if(!precision){
        return Math.round(num);
      }

      p = (precision) ? Math.pow(10, precision) : 1;
      return Math.round(num*p)/p;
    },

    /** Formats data size
     * @param {Number} size Input size in bytes
     * @returns {String} Formatted size (e.g. 103.4KB)
     */
    formatSize: function(size) {
      var level = 0;

      while(size >= 1024) {
        size = App.tools.round(size / 1024, 2);
        level++;
      }

      return (level > 0 ? this.round(size, 2) : size) + settings.sizeUnits[level];
    },

    inject: (function() {
      var Inject = function(url, callback) {
        this.callback = callback;
        this.loadedCount = 0;

        if(url instanceof Array) {
          this.count = url.length;

          for(var a=0; a<url.length; a++) {
            this.loadScript(url[a]);
          }
        }
        else {
          this.count = 1;
          this.loadScript(url);
        }
      };

      Inject.prototype = {
        loadScript: function(url) {
          var _this = this;
          var script = document.createElement('script');
          script.type = 'text/javascript';
          script.async = true;
          script.onload = function() {
            _this.scriptLoaded();
          };
          script.src = url;
          document.getElementsByTagName('head')[0].appendChild(script);
        },

        scriptLoaded: function() {
          this.loadedCount++;

          if(this.loadedCount >= this.count) {
            if(this.callback) {
              this.callback();
            }
          }
        }
      };

      return function(url, callback) {
        return new Inject(url, callback);
      };
    }()),

    urlencode: function(string) {
      string = encodeURIComponent(string);
      string = string.replace(/%2F/g, '%252F');
      string = string.replace(/%5C/g, '%255C');

      return string;
    },

    urldecode: function(string) {
      string = string.replace(/%252F/g, '%2F');
      string = string.replace(/%255C/g, '%5C');
      string = decodeURIComponent(string);

      return string;
    }
  };
}(jQuery));
