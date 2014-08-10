/*jslint nomen: true, debug: true, evil: false, vars: true, browser: true, devel: true */
/*global require: false, define: false, $: false, _: false, Backbone: false, server: false */

(function (ui, app, api, $, _, Backbone, undefined) {

    "use strict";

    var thiz = {},
        ajax;

    ui.page = {
        window: {
            el: $(window)
        }
    };

    ui.init = function () {
        thiz = this;
    };

    ui.insertDomFile = function (k, v, path) {
        if (v.length) {
            var file = $('input[name="' + k + '"]');
            file.after('<a href="' + path + v + '" target="_blank" class="download-link"><img src="/images/link.png" width="20"></a>');
        }
    };

    ui.reversedate = function (date, year) {
        if (!date) {
            return '';
        }
        date = date.split("-");
        if (typeof year === 'boolean' && year) {
            if (date[0].length === 4) {
                return date[0] + '-' + date[1] + '-' + date[2];
            }
        }
        return date[2] + '-' + date[1] + '-' + date[0];
    };
}(GSD.Ui = GSD.Ui || {}, GSD.App, GSD.Api, jQuery, _, Backbone));
