/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 * @version    1.5
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */

(function (pages, api, $, _, document, undefined) {

    'use strict';

    var beforedrag = 0,
        tbody = {},
        generateurl = function () {
            var value = this.value.toLowerCase(),
                url = $(this).closest('div').next().find('[name="url"]').get(0) || {};

            if (!_.isUndefined(url.value) && !url.value.length && value.length) {
                value = value
                    .replace(/ç/g, 'c')
                    .replace(/á/g, 'a').replace(/à/g, 'a').replace(/ã/g, 'a').replace(/â/g, 'a')
                    .replace(/é/g, 'e').replace(/è/g, 'e').replace(/ê/g, 'e')
                    .replace(/í/g, 'i').replace(/ì/g, 'i').replace(/î/g, 'i')
                    .replace(/ó/g, 'o').replace(/ò/g, 'o').replace(/õ/g, 'o').replace(/ô/g, 'o')
                    .replace(/ú/g, 'u').replace(/ù/g, 'u').replace(/û/g, 'u')
                    .replace(/[^A-Za-z0-9\- ]+/g, '')
                    .replace(/\ /g, '-');
                url.value = '/' + value.trim();
            }
        },

        newsubmodule = function () {
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
        },

        togglesettings = function () {
            var icon = $(this),
                parent = icon.closest('div'),
                settings = parent.find('.settings:first');

            settings.css({
                left: icon.position().left + 20,
                top: 0
            });

            settings.toggle();
        },

        startDrag = function (e) {
            var $tr = $(e.currentTarget).closest('tr'),
                $clone = document.createElement('tr'),
                stopDrag = function () {
                    $clone.off('mousemove');
                    $clone.remove();
                    $tr.removeClass('cloned');
                };

            e.preventDefault();

            tbody.el.append($clone);
            $clone = tbody.el.find('tr:last');
            $clone.addClass('drag').data('pid', $tr.data('pid')).css({
                height: $tr.outerHeight(true),
                width: $tr.outerWidth(true) - 4,
                top: $tr.position().top
            });

            $tr.addClass('cloned');

            $clone.on('mousemove', { diff: e.pageY - $tr.position().top }, drag);
            $clone.on('mouseup', stopDrag);
            $(document).on('mouseup', stopDrag);
        },

        drag = function (e) {
            var beforetop = e.pageY - e.data.diff,
                $drag = $(e.currentTarget),
                dragheight = $drag.outerHeight(true),
                $original = tbody.el.find('tr[data-pid="' + $drag.data('pid') + '"]'),
                $before = $original.prev(':not(.drag, .is-hidden)'),
                $after = $original.next(':not(.drag, .is-hidden)'),
                index = 0,
                top;

            top = beforetop < tbody.top - dragheight ? tbody.top - dragheight : (beforetop > tbody.bottom ? tbody.bottom : beforetop);

            $drag.css({ top: top });

            if ($before.length && $before.position().top + tbody.middle >= top) {
                $before.before($original.detach());
                index = $before.attr('data-index');

                $before.attr('data-index', $original.attr('data-index'));
                $original.attr('data-index', index);
            }

            if ($after.length && $after.position().top - tbody.middle <= top) {
                $after.after($original.detach());
                index = $after.attr('data-index');

                $after.attr('data-index', $original.attr('data-index'));
                $original.attr('data-index', index);
            }

            if (((tbody.top - dragheight === top) || tbody.bottom === top) && beforedrag === top) {
                $('.cloned').next().removeClass('is-hidden');
                $('.cloned').prev().removeClass('is-hidden');
            }

            beforedrag = top;
        },

        updateOrder = function (e) {
            var list = [];

            tbody.tr.each(function (i, item) {
                list.push({
                    i: $(item).attr('data-index'),
                    pid: $(item).data('pid')
                });
            });

            api.call($(e.currentTarget), 'PUT', 'pageorder', { list: JSON.stringify(list) }, function () {
                api.loading();
            }, function () {
                api.loading();
            });
        },

        updateInternals = function () {

            tbody.width = tbody.el.outerWidth(true);
            tbody.height = tbody.el.outerHeight(true);
            tbody.top = tbody.el.position().top;
            tbody.bottom = tbody.el.position().top + tbody.el.outerHeight(true);
            tbody.middle = tbody.el.find('tr:first').height() / 2;
            tbody.tr = tbody.el.find('tr');
        };

    $(document).bind(GSD.globalevents.init, function () {
        var $tbody = $('.pages tbody');

        if ($tbody.length) {
            tbody.el = $tbody;

            updateInternals();

            tbody.el.on('mousedown', '.order', startDrag);
            $('.pages').on('click', '.refresh', updateOrder);

            $(document).bind(GSD.globalevents.updateInternals, updateInternals);
        }
        $('[name="title"]').on('blur', generateurl);
        $('body').on('change', '.item_value', newsubmodule);
        $('body').on('click', '.fa-gear', togglesettings);
    });

}(GSD.Pages = GSD.Pages || {}, GSD.Api, GSD.$, _, document));
