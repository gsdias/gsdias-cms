/*jslint nomen: true, debug: true, evil: false, vars: true, browser: true, devel: true */
/*global require: false, define: false, $: false, _: false, Backbone: false, server: false */

(function (pages, app, $, _, Backbone, undefined) {

    "use strict";

    var generateurl = function () {
        var value = this.value.toLowerCase(),
            url = $('[name="url"]');

        if (url.length && !url.val().length && value.length) {
            value = value.replace( new RegExp(" ", "gm"),"-")
                    .replace(/ç/g, "c")
                    .replace(/á/g, "a").replace(/à/g, "a").replace(/ã/g, "a").replace(/â/g, "a")
                    .replace(/é/g, "e").replace(/è/g, "e").replace(/ê/g, "ê")
                    .replace(/í/g, "i").replace(/ì/g, "i").replace(/î/g, "i")
                    .replace(/ó/g, "o").replace(/ò/g, "o").replace(/õ/g, "o").replace(/ô/g, "o")
                    .replace(/ú/g, "u").replace(/ù/g, "u").replace(/û/g, "u");
            url.val('/' + value.trim());
        }
    };

    var newsubmodule = function () {
        var id = this.name.replace('pm_s', ''),
            elm = $(this).closest('.submodule'),
            newelm = elm.clone();

        if (!elm.next().length) {
            newelm.find('input').val('');
            elm.after(newelm);
        }
    };

    $(document).bind(GSD.globalevents.init, function () {
        $('[name="title"]').on('blur', generateurl);
        $('body').on('focus', "input[name^='pm_s']", newsubmodule);
    });

}(GSD.Pages = GSD.Pages || {}, GSD.App, jQuery, _, Backbone));
