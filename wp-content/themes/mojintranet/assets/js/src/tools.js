/** App tools - generic set of tools used across the whole application
 */
(function($) {
  "use strict";

  var App = window.App;

  var userLoggedIn = !!$('head').attr('data-user-logged-in');

  var settings = {
    sizeUnits: ['B', 'KB', 'MB', 'GB', 'TB', 'PB'],
    months: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']
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
    },

    setCookie: function(name, value, days) {
      var parts = [];
      var date;

      //name=value
      parts.push(encodeURIComponent(name) + "=" + encodeURIComponent(value));

      //expires
      if (days) {
        date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        parts.push("expires=" + date.toGMTString());
      }

      //path
      parts.push('path=/');

      document.cookie = parts.join('; ');

      if(window.hasFlash && window.mySwfStore) {
        window.mySwfStore.set(name, value);
      }
    },

    getCookie: function(name) {
      var cookieNameEq = encodeURIComponent(name) + "=";
      var parts = document.cookie.split(';');
      var part;
      var a;
      var length;
      var value;

      if(window.hasFlash && window.mySwfStore) {
        return window.mySwfStore.get(name);
      }

      for (a = 0, length = parts.length; a < length; a++) {
        part = parts[a].replace(/(^\s*|\s*$)/g, '');

        if (part.indexOf(cookieNameEq) === 0) {
          value = part.substring(cookieNameEq.length);

          if(window.hasFlash && window.mySwfStore) {
            window.mySwfStore.set(name, value);
          }

          return decodeURIComponent(value);
        }
      }

      return null;
    },

    deleteCookie: function(name) {
      this.setCookie(name, "", -1);
    },

    /** Finds a first occurrence of value in an object or an array (strict on types)
     * @param {Mixed} value Value to be found
     * @param {Array|Object} obj Subject array or object
     * @param {Boolean|String|Integer} returnKey=false
     * @returns {String|Integer|Boolean}
     *   returnKey=true: the {String} key will be returned (or {Integer} index if it's an array) if the value was found, otherwise returns {Boolean} false
     *   returnKey=false: {Boolean} true when value was found, otherwise {Boolean} false
     */
    search: function(value, obj, returnKey) {
      var type = $.type(obj);

      if(type === 'array') {
        for(var index = 0,length = obj.length; index<length; index++) {
          if(obj[index] === value) {
            return (returnKey) ? index : true;
          }
        }
      }
      else if(type === 'object') {
        for (var key in obj) {
          if(obj.hasOwnProperty(key)) {
            if(obj[key] === value) {
              return (returnKey) ? key : true;
            }
          }
        }
      }

      return false;
    },

    /** Counts number of elements in an array/object
     * @param {Array|Object} obj Subject array or object
     * @returns {Integer} array length or object size
     */
    count: function(obj) {
      var type = $.type(obj);
      var count, i;

      if (type === 'array') {
        return obj.length;
      }
      else if (type === 'object') {
        if (Object.keys) {
          return Object.keys(obj).length;
        }
        else {
          count = 0;

          for (i in obj) {
            if (obj.hasOwnProperty(i)) {
              count++;
            }
          }
        }
      }

      return false;
    },

    /** Merges two or more arrays/objects into one array/object. Every next object overwrites properties of the object to be returned
     *All arguments must be of the same type (either arrays or objects)
     *@param {Array|Object} ... Array/Object
     *@returns {Array|Object} Merged Array/Object
     */
    merge: function(base){
      var baseType = $.type(base);
      var a,b;
      var newObj;

      if (baseType === 'array') {
        newObj = [];
      }
      else if (baseType === 'object') {
        newObj = {};
      }
      else {
        throw new Error("expected base argument to be of type: array or object");
      }

      for (a = 0; a < arguments.length; a++) {
        var thisObj = arguments[a];

        if ($.type(thisObj) !== baseType) {
          throw new Error("argument types don't match");
        }

        if (baseType === 'array') {
          newObj = newObj.concat(thisObj);
        }
        else { //object
          for (b in thisObj) {
            if (thisObj.hasOwnProperty(b)) {
              newObj[b] = thisObj[b];
            }
          }
        }
      }

      return newObj;
    },

    /** Strips whitespace from beginning and end of a string
     * @param {String} string Input string
     * @param {String} character Custom Regex-compatible character to be stripped; defaults to whitespace (\\s)
     * @returns {String} Trimmed string
     */
    trim: function(string, character){
      if (!$.type(character)) {
        character = '\\s';
      }

      return string.replace(new RegExp('^'+character+'+|'+character+'+$', 'g'), '');
    },

    /** Strips whitespace from the beginning of a string
     * @param {String} string Input string
     * @param {String} character Custom Regex-compatible character to be stripped; defaults to whitespace
     * @returns {String} Trimmed string
     */
    trimLeft: function(string, character){
      if (!character) {
        character = '\\s';
      }

      return string.replace(new RegExp('^'+character+'+', 'g'), '');
    },

    /** Strips whitespace from the end of a string
     * @param {String} string Input string
     * @param {String} character Custom Regex-compatible character to be stripped; defaults to whitespace
     * @returns {String} Trimmed string
     */
    trimRight: function(string, character){
      if (!character) {
        character = '\\s';
      }

      return string.replace(new RegExp(''+character+'+$', 'g'), '');
    },

    parseDate: function(dateString) {
      var dateArray = dateString.split('-');
      if(dateArray.length === 2){
        dateArray.push('01');
      }

      return new Date(dateArray.join('/'));
    },

    formatDate: function(dateObject, shortMonth) {
      var month = settings.months[dateObject.getMonth()];

      if(shortMonth) {
        month = month.substr(0, 3);
      }

      return dateObject.getDate() + ' ' + month + ' ' + dateObject.getFullYear();
    },

    /** Changes the first character of the string to upper case
     * @param {String} string Input string
     * @returns {String} Converted string
     */
    ucfirst: function(string) {
      return string.charAt(0).toUpperCase() + string.substr(1);
    },

    sortByKey: function(array, key) {
      return array.sort(function(a, b) {
        var x = a[key];
        var y = b[key];

        return ((x < y) ? -1 : ((x > y) ? 1 : 0));
      });
    },

    isUserLoggedIn: function() {
      return userLoggedIn;
    }
  };
}(jQuery));
