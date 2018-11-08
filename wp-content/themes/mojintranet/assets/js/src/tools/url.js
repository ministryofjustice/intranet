(function($) {
  "use strict";

  var App = window.App;

  App.tools.url = (function() {
    /* Creates a new url object
     * (false {Boolean}) - sets an empty url
     * (true {Boolean}) - creates a url from current url
     * (url {String}) - creates a url from string
     * (url {Object}) - creates a url from key/value pairs (scheme, host, port, segments[], params{})
     */
    var Url = function(url) {
      this.set(url);
      this.currentUrl = this._parse(window.location.href);

      return this;
    };

    /** gets the url as a string
     */
    Url.prototype.get = function() {
      var str = this.authority() || '';
      var i;
      var joined;

      if (str.length) { //append slash if authority is not empty
        str += '/';
        }

      if (this.urlParts.segments.length) {
        str += this.urlParts.segments.join('/');
      }

      if (App.tools.count(this.urlParts.params)) {
        joined = [];
        for (i in this.urlParts.params) {
          if (this.urlParts.params.hasOwnProperty(i)) {
            joined.push(i + '=' + this.urlParts.params[i]);
          }
        }
        str += '?' + joined.join('&');
      }

      if (this.urlParts.partial) {
        str += '#'+this.urlParts.partial;
      }

      return str;
    };

    /** sets the url to the value
     * (false {Boolean}) - sets an empty url
     * (true {Boolean}) - creates a url from current url
     * (url {String}) - creates a url from string
     * (url {Object}) - creates a url from key/value pairs (scheme, host, port, segments[], params{})
     */
    Url.prototype.set = function(url) {
      var type = $.type(url);

      if (url === false) {
        this.urlParts = this._getBlankUrlPartsObject();
      }
      else if (url === true) {
        this.urlParts = this._parse(window.location.href);
      }
      else if (type === 'string') {
        this.urlParts = this._parse(url);
      }
      else if (type === 'object') {
        this.urlParts = App.tools.merge(this._getBlankUrlPartsObject(), url);
      }

      return this;
    };

    /** unsets the url
     */
    Url.prototype.unset = function() {
      this.set(false);
      return this;
    };

    /** returns a blank urlParts object
     */
    Url.prototype._getBlankUrlPartsObject = function() {
      return JSON.parse(JSON.stringify({
        scheme : null,
        username : null,
        password : null,
        host : null,
        port : null,
        segments : [],
        params : {},
        partial : null
      }));
    };

    /** parses the specified url
     * (urlStr {String})
     *
     * @returns {Object} parsed url
     */
    Url.prototype._parse = function(urlStr) {
      var urlParts = this._getBlankUrlPartsObject();

      var schemeSeparatorPos = -1;
      var hostSeparatorPos = -1;
      var qstrSeparatorPos = -1;
      var partialSeparatorPos = -1;
      var hostStr = null;
      var segmentsStr = null;
      var queryStr = null;
      var paramsJoined;
      var a, length;
      var credentialsSeparatorPos;
      var credentialsParts;
      var portSeparatorPos;
      var qstrParts;
      var name;
      var value;

      var isAbsolute = /^[A-Za-z]+:\/\//.test(urlStr);

      if (isAbsolute) {
        schemeSeparatorPos = urlStr.indexOf('://');
        hostSeparatorPos = urlStr.indexOf('/', schemeSeparatorPos + 3);
        hostStr = urlStr.substring(schemeSeparatorPos + 3, hostSeparatorPos >= 0 ? hostSeparatorPos : undefined);

        //get scheme
        urlParts.scheme = urlStr.substring(0, schemeSeparatorPos);


        //get host parts
        if (hostStr) {
          credentialsSeparatorPos = hostStr.indexOf('@');
          if (credentialsSeparatorPos >= 0) {
            credentialsParts = hostStr.substring(0, credentialsSeparatorPos).split(':');
            urlParts.username = credentialsParts[0];
            if (credentialsParts[1]) {
              urlParts.password = credentialsParts[1];
            }
            hostStr = hostStr.substring(credentialsSeparatorPos + 1); //strip credentials from the hostStr
          }

          portSeparatorPos = hostStr.indexOf(':');
          if (portSeparatorPos >= 0) {
            urlParts.port = hostStr.substring(portSeparatorPos + 1);
            hostStr = hostStr.substring(0, portSeparatorPos); //strip port from the hostStr
          }

          urlParts.host = hostStr;
        }

        //get rid of everything before the host separator
        urlStr = urlStr.substring(hostSeparatorPos >= 0 ? hostSeparatorPos + 1 : urlStr.length);
      }


      //get partial
      partialSeparatorPos = urlStr.indexOf('#');
      if (partialSeparatorPos >= 0) {
        urlParts.partial = urlStr.substring(partialSeparatorPos + 1);
        urlStr = urlStr.substring(0, partialSeparatorPos);
      }

      qstrSeparatorPos = urlStr.indexOf('?');
      segmentsStr = urlStr.substring(0, qstrSeparatorPos >= 0 ? qstrSeparatorPos : undefined);
      queryStr = qstrSeparatorPos >= 0 ? urlStr.substring(qstrSeparatorPos + 1) : null;

      //get segments
      if (segmentsStr) {
        segmentsStr = segmentsStr.replace(/\/$/, ''); //remove trailing slash
        urlParts.segments = segmentsStr.split('/');
      }

      //get params
      if (queryStr) {
        paramsJoined = queryStr.split('&');
        for(a = 0, length = paramsJoined.length; a < length; a++){
          //param can look like this: ?param=value=stillValueOfTheParam (i.e. multiple equal signs), so basically param name is everything before the first '=' and value is the rest
          qstrParts = paramsJoined[a].split('=');
          name = qstrParts.shift(); //get the first part off the array
          value = qstrParts.join('='); //join the rest using '=' as glue
          urlParts.params[name] = value;
        }
      }

      return urlParts;
    };

    /** sets, unsets or gets the scheme
     * () - gets the scheme
     * (scheme {string}) - sets the scheme
     * (true {boolean}) - sets scheme to current scheme (from window.location.href)
     * (false {boolean}) - unsets the scheme
     */
    Url.prototype.scheme = function(scheme) {
      var type = $.type(scheme);
      if (type === 'string') { //setter
        this.urlParts.scheme = scheme;
      }
      else if (scheme === true) { //use current scheme from window.location.href
        this.urlParts.scheme = this.currentUrl.scheme;
      }
      else if (scheme === false){ //remove scheme
        this.urlParts.scheme = null;
      }
      else if (type === 'undefined') { //getter
        return this.urlParts.scheme;
      }
      else {
        throw new Error('Url.scheme: Scheme must be a string, boolean or undefined');
      }
      return this;
    };

    /** sets, unsets or gets the username
     * () - gets the username
     * (username {string}) - sets the username
     * (true {boolean}) - sets username to current username (from window.location.href)
     * (false {boolean}) - unsets the username
     */
    Url.prototype.username = function(username) {
      var type = $.type(username);
      if (type === 'string') { //setter
        this.urlParts.username = username;
      }
      else if (username === true) { //use current scheme from window.location.href
        this.urlParts.username = this.currentUrl.username;
      }
      else if (username === false) { //remove username
        this.urlParts.username = null;
      }
      else if (type === 'undefined') { //getter
        return this.urlParts.username;
      }
      else {
        throw new Error('Url.username: Username must be a string, boolean or undefined');
      }
      return this;
    };

    /** sets, unsets or gets the password
     * () - gets the password
     * (password {string}) - sets the password
     * (true {boolean}) - sets password to current password (from window.location.href)
     * (false {boolean}) - unsets the password
     */
    Url.prototype.password = function(password) {
      var type = $.type(password);
      if (type === 'string') { //setter
        this.urlParts.password = password;
      }
      else if (password === true) { //use current scheme from window.location.href
        this.urlParts.password = this.currentUrl.password;
      }
      else if (password === false) { //remove password
        this.urlParts.password = null;
      }
      else if (type === 'undefined') { //getter
        return this.urlParts.password;
      }
      else {
        throw new Error('Url.password: Password must be a string, boolean or undefined');
      }
      return this;
    };

    /** sets, unsets or gets the host
     * () - gets the host
     * (host {string}) - sets the host
     * (true {boolean}) - sets host to current host (from window.location.href)
     * (false {boolean}) - unsets the host
     */
    Url.prototype.host = function(host) {
      var type = $.type(host);
      if (type === 'string') { //string? set it then
        this.urlParts.host = App.tools.trimRight(host, '/');
      }
      else if (host === true) { //bool:true? then the current host will be used
        this.urlParts.host = this.currentUrl.host;
      }
      else if (host === false) { //bool:false? then remove host completely
        this.urlParts.host = null;
      }
      else if (type === 'undefined') { //undefined? then it's a getter
        return this.urlParts.host;
      }
      else {
        throw new Error('Url.host: host must be a string, boolean or undefined');
      }
      return this;
    };

    /** sets, unsets or gets the port
     * () - gets the port
     * (port {integer}) - sets the port
     * (true {boolean}) - sets port to current port
     * (false {boolean}) - unsets the port
     */
    Url.prototype.port = function(port) {
      var type = $.type(port);
      if (type === 'string' || type === 'number') { //setter
        this.urlParts.port = port;
      }
      else if (port === true) { //use current port from window.location.href
        this.urlParts.port = this.currentUrl.port;
      }
      else if (port === false) { //remove host
        this.urlParts.port = null;
      }
      else if (type === 'undefined') { //getter
        return this.urlParts.port;
      }
      else {
        throw new Error('Url.port: Port must be a string, number, boolean or undefined');
      }
      return this;
    };

    /** sets, unsets or gets the authority
     * () - gets the authority
     * (authority {string}) - sets the authority
     * (true {boolean}) - sets authority to current authority
     * (false {boolean}) - unsets the authority
     */
    Url.prototype.authority = function(authority) {
      var _this = this;
      var overwriteAuthorityParts = function(authorityParts) {
        _this.urlParts.scheme = authorityParts.scheme;
        _this.urlParts.username = authorityParts.username;
        _this.urlParts.password = authorityParts.password;
        _this.urlParts.host = authorityParts.host;
        _this.urlParts.port = authorityParts.port;
      };
      var type = $.type(authority);
      var authorityStr;

      if (type === 'string') { //string? set it then
        overwriteAuthorityParts(App.tools.url(authority).urlParts);
      }
      else if (authority === true) { //bool:true? then the current authority will be used
        overwriteAuthorityParts(this.currentUrl.urlParts);
      }
      else if (authority === false) { //bool:false? then remove authority completely
        overwriteAuthorityParts(this._getBlankUrlPartsObject());
      }
      else if (type === 'undefined') { //undefined? then it's a getter
        if (this.urlParts.scheme !== null) {
          authorityStr = this.urlParts.scheme + '://';

          if (this.urlParts.host) {
            if (this.urlParts.username) {
              authorityStr += this.urlParts.username;
              if (this.urlParts.password) {
                authorityStr += ':'+this.urlParts.password;
              }
              authorityStr += '@';
            }

            authorityStr += this.urlParts.host;

            if (this.urlParts.port) {
              authorityStr += ':'+this.urlParts.port;
            }
          }

          return authorityStr;
        }
        else {
          return null;
        }
      }
      else {
        throw new Error('Url.authority: authority must be a string, boolean or undefined');
      }
      return this;
    };

    /** gets a segment or sets one or more URL segments
     * (index {integer}) - gets segment by index number
     * (index {integer}, value {string}) - sets segment at index to the value of the string
     * (value {string}) - appends segment from string (or multiple slash-separated segments)
     * (segments {array}) - appends multiple segments specified in an array at the end of the existing segments
     */
    Url.prototype.segment = function() {
      var arg0 = arguments[0];
      var arg0Type = $.type(arg0);
      var arg1 = arguments[1];
      var arg1Type = $.type(arg1);
      var offset;

      if (arg0Type === 'number') { //get/set segment by index
        if (arg1Type === 'string') { //setter
          this.urlParts.segments[arg0] = arg1;
        }
        else if (arg1Type === 'undefined') { //getter
          offset = arg0<0 ? this.urlParts.segments.length + arg0 : arg0;
          return this.urlParts.segments[offset];
        }
        else { //error
          throw new Error('Url.segment: incorrect type for argument 1');
        }
      }
      else if (arg0Type === 'string') { //append segment or multiple segments
        this.urlParts.segments = this.urlParts.segments.concat(arg0.split('/'));
      }
      else if (arg0Type === 'array') { //append multiple segments
        this.urlParts.segments = this.urlParts.segments.concat(arg0);
      }
      else {
        throw new Error('Url.segment: Argument 0 must be a number, string or an array');
      }
      return this;
    };

    /** inserts one or more segments before index
     * (index {integer}, segment {string}) - inserts segment at index
     * (index {integer}, segment {array}) - inserts multiple segments at index
     */
    Url.prototype.insertSegmentBefore = function(index, segment) {
      var _this = this;
      var a, length;
      var insertMultiple = function(segments) {
        segments.reverse();
        for (a = 0, length = segments.length; a < length; a++){
          _this.urlParts.segments.splice(index, 0, segments[a]);
        }
      };

      var segmentType = $.type(segment);

      if (segmentType === 'string') {
        insertMultiple(segment.split('/'));
      }
      else if (segmentType === 'array') {
        insertMultiple(segment);
      }
      return this;
    };

    /** unsets one or more segments
     * (index {integer}) - unsets segment at index
     * (indexes {array}) - unsets segments at indexes specified in the array
     */
    Url.prototype.unsetSegment = function(arg0) {
      var arg0Type = $.type(arg0);
      var a, length;

      if (arg0Type === 'number') { //unset by index
        this.urlParts.segments.splice(arg0, 1);
      }
      else if (arg0Type==='array') { //unset multiple by indexes
        //we need to unset the segments in a reversed order
        arg0.sort().reverse();
        for (a = 0, length = arg0.length; a < length; a++) {
          this.urlParts.segments.splice(arg0[a], 1);
        }
      }
      else {
        throw new Error('Url.unsetSegment: Argument 0 must be a number');
      }
      return this;
    };

    /** unsets all segments
     */
    Url.prototype.unsetAllSegments = function() {
      this.urlParts.segments = [];

      return this;
    };

    /** gets a param or sets one or more URL params
     * (key {string}) - gets param by key
     * (key {string}, value {string}) - sets param's value
     * (params {object}) - sets multiple params
     */
    Url.prototype.param = function() {
      var arg0 = arguments[0];
      var arg0Type = $.type(arg0);
      var arg1 = arguments[1];
      var arg1Type = $.type(arg1);

      if (arg0Type === 'string') { //get/set param
        if (arg1Type === 'string') { //setter
          this.urlParts.params[arg0] = arg1;
        }
        else { //getter
          return this.urlParts.params[arg0];
        }
      }
      else if (arg0Type === 'object') { //set multiple params
        this.urlParts.params = App.tools.merge(this.urlParts.params, arg0);
      }
      else {
        throw new Error('Argument 0 must be a string or an object');
      }
      return this;
    };

    /** unsets one or more params
     * (key {string}) - unsets param
     * (keys {array}) - unsets all params specified in the array
     */
    Url.prototype.unsetParam = function(arg0) {
      var type = $.type(arg0);
      var a, length;

      if (type === 'string') { //delete one by name
        delete(this.urlParts.params[arg0]);
      }
      else if (type === 'array') { //delete multiple by array of strings (key names)
        for(a = 0, length = arg0.length; a < length; a++){
          delete(this.urlParts.params[arg0[a]]);
        }
      }
      return this;
    };

    /** unsets all params
     */
    Url.prototype.unsetAllParams = function() {
      this.urlParts.params = {};

      return this;
    };

    /** sets, unsets or gets the partial
     * () - gets the partial
     * (partial {string}) - sets the partial
     * (true {boolean}) - sets partial to current partial (from window.location.href)
     * (false {boolean}) - unsets the partial
     */
    Url.prototype.partial = function(partial) {
      var type = $.type(partial);
      if (type === 'string') { //setter
        this.urlParts.partial = partial;
      }
      else if (partial === true) { //use current scheme from window.location.href
        this.urlParts.partial = this.currentUrl.partial;
      }
      else if (partial === false) { //remove partial
        this.urlParts.partial = null;
      }
      else if (type === 'undefined') { //getter
        return this.urlParts.partial;
      }
      else {
        throw new Error('Url.partial: Partial must be a string, boolean or undefined');
      }
      return this;
    };

    /** redirects to this url
     */
    Url.prototype.go = function() {
      window.location.href = this.get();
    };

    /** clones the url objects and returns the new instance
    */
    Url.prototype.clone = function() {
      return App.tools.url(this.urlParts);
    };

    return function(url) {
      if (url instanceof Url) {
        return url;
      }
      else {
        return new Url(url);
      }
    };
  }());
}(jQuery));
