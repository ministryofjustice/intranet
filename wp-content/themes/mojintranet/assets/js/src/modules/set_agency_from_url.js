(function($) {
    "use strict";

    var App = window.App;

    App.SetAgencyFromUrl = function() {
        this.init();
    };

    App.SetAgencyFromUrl.prototype = {
        init: function() {
            var agency = App.tools.getUrlParam('agency');
            if (typeof agency === 'string') {
                this.setAgency(agency);
                this.removeFromUrl();
            }
        },

        setAgency: function(agency) {
            App.tools.helpers.agency.set(agency);
        },

        removeFromUrl: function() {
            if(!window.history.replaceState) {
                return;
            }

            var urlParts = window.location.href.split('?');
            var url = urlParts[0];
            var query = urlParts[1].split('&');

            var newUrl = url;
            var newQuery = [];

            var a, length;
            var pair, key;

            for (a = 0, length = query.length; a < length; a++) {
                pair = query[a].split('=');
                key = decodeURIComponent(pair.shift());

                if (key !== 'agency') {
                    newQuery.push(query[a]);
                }
            }

            if (newQuery.length > 0) {
                newUrl += '?' + newQuery.join('&');
            }

            window.history.replaceState(null, null, newUrl);
        }
    };
}(jQuery));
