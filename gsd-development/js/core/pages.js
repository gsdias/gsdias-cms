/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 * @version    1.0
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */

(function (pages, app, $, _, Backbone, undefined) {

    'use strict';

    var generateurl = function () {
        var value = this.value.toLowerCase(),
            url = $('[name="url"]').get(0);

        if ($(url).length && !url.value.length && value.length) {
            value = value
                    .replace(/!/g, '').replace(/@/g, '').replace(/£/g, '').replace(/\$/g, '').replace(/%/g, '')
                    .replace(/\^/g, '').replace(/&/g, '').replace(/\*/g, '').replace(/\(/g, '').replace(/\)/g, '')
                    .replace(/\=/g, '').replace(/\?/g, '').replace(/\€/g, '').replace(/\#/g, '').replace(/\~/g, '')
                    .replace(/\//g, '').replace(/\./g, '').replace(/\,/g, '').replace(/\\/g, '')
                    .replace(/ç/g, 'c')
                    .replace(/ç/g, 'c')
                    .replace(/á/g, 'a').replace(/à/g, 'a').replace(/ã/g, 'a').replace(/â/g, 'a')
                    .replace(/é/g, 'e').replace(/è/g, 'e').replace(/ê/g, 'e')
                    .replace(/í/g, 'i').replace(/ì/g, 'i').replace(/î/g, 'i')
                    .replace(/ó/g, 'o').replace(/ò/g, 'o').replace(/õ/g, 'o').replace(/ô/g, 'o')
                    .replace(/ú/g, 'u').replace(/ù/g, 'u').replace(/û/g, 'u')
                    .replace(/\b\ \b/g, '-');
            url.value = '/' + value.trim();
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
        var icon = $(this),
            parent = icon.closest('div'),
            settings = parent.find('.settings:first');

        settings.css({
            left: icon.position().left + 20,
            top: 0
        });

        settings.toggle();
    };

    $(document).bind(GSD.globalevents.init, function () {
        $('[name="title"]').on('blur', generateurl);
        $('body').on('change', '.item_value', newsubmodule);
        $('body').on('click', '.icon-gear', togglesettings);
    });

}(GSD.Pages = GSD.Pages || {}, GSD.App, GSD.$, _, Backbone));
