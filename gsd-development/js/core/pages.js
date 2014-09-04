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
        var id = this.name.replace('pm_s_', '').split('_'),
            elm = $(this).closest('.submodule'),
            newelm = elm.clone();

        if (!elm.next().length) {
            var name = id[2].split('[');
            newelm.find('.item_value').val('');
            newelm.find('.item_value').attr('name', 'value_pm_s_' + (parseInt(id[1], 10) + 1) + '_' + name[0] + '[]');
            newelm.find('.item_class').attr('name', 'class_pm_s_' + (parseInt(id[1], 10) + 1) + '_' + name[0] + '[]');
            newelm.find('.item_style').attr('name', 'style_pm_s_' + (parseInt(id[1], 10) + 1) + '_' + name[0] + '[]');
            elm.after(newelm);
            newelm.find('.clearimage').trigger('click');
        }
    };

    var togglesettings = function () {
        var parent = $(this).closest('div'),
            settings = parent.find('.settings');

        settings.toggle();
    };

    $(document).bind(GSD.globalevents.init, function () {
        $('[name="title"]').on('blur', generateurl);
        $('body').on('change', ".item_value", newsubmodule);
        $('body').on('click', ".icon-gear", togglesettings);
    });

}(GSD.Pages = GSD.Pages || {}, GSD.App, jQuery, _, Backbone));
