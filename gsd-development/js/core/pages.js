/*jslint nomen: true, debug: true, evil: false, vars: true, browser: true, devel: true */
/*global require: false, define: false, $: false, _: false, Backbone: false, server: false */

(function (pages, app, $, _, Backbone, undefined) {

    "use strict";

    var generateurl = function () {
        var value = this.value,
            url = $('[name="url"]');

        if (url.length && !url.val().length && value.length) {
            value = value.replace( new RegExp(" ", "gm"),"-").toLowerCase();
            url.val('/' + value);
        }
    };

    $(document).bind(GSD.globalevents.init, function () {
        $('[name="title"]').on('blur', generateurl);
    });

}(GSD.Pages = GSD.Pages || {}, GSD.App, jQuery, _, Backbone));
