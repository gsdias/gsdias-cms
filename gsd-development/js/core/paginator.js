/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 * @version    1.4
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.2
 */

(function (pages, app, api, global, $, _, Backbone, document, undefined) {

    'use strict';

    var tbody = {},
        $options = {},
        $check = {},
        classPaginator = '.paginator',
        list = '.pages',

        requestPage = function (e) {
            e.preventDefault();

            var page = this.href.split('='),
                search = $('[name="search"]').val();

            api.call($(e.currentTarget), 'GET', 'pages', { page: page[1], type: tbody.type, search: search }, function (response) {
                var template = $('#' + tbody.type + 'ExtendedBlock').length ? $('#' + tbody.type + 'ExtendedBlock') : $('#' + tbody.type + 'Block');
                tbody.el.empty();

                _.each(response.data.list, function (item) {
                    tbody.el.append(_.template(template.html(), item));
                });

                $(classPaginator).replaceWith(response.data.paginator);

                global.updateHistory('?page=' + page[1]);
                $(document).trigger(GSD.globalevents.updateInternals);
                $check = $('.hasPages input');
                api.loading();
            }, function () {
                api.loading();
            });
        },

        showOption = function () {
            if ($(this).hasClass('selection')) {
                if ($check.filter(':not(:checked)').length) {
                    $check.filter(':not(:checked)').trigger('click');
                } else {
                    $check.trigger('click');
                }
            }
            $options.toggleClass('is-visible', $check.filter(':checked').length > 0);
            $(this).toggleClass('is-visible');
        },

        action = function () {
            if ($(this).hasClass('clear')) {
                $check.filter(':checked').trigger('click');
            }

            if ($(this).hasClass('revert')) {
                $check.trigger('click');
            }

            if ($(this).hasClass('remove')) {
                var list = [];
                $check.filter(':checked').each(function(index, elem) {
                    list.push(elem.value);
                });
                api.call($(this), 'DELETE', tbody.type, { type: tbody.type, list: list.join(',') }, function (response) {
                    api.loading();
                    _.each(response.list, function(id) {
                        tbody.el.find('tr[data-' + tbody.type.substr(0, 1) + 'id="' + id + '"]').slideUp(function() {
                            $(this).remove();
                        });
                    });
                }, function () {
                    api.loading();
                });
            }
        };

    $(document).bind(GSD.globalevents.init, function () {
        var $tbody = $('.hasPages tbody');

        if ($tbody.length) {
            tbody.el = $tbody;
            tbody.type = $('.hasPages').data('type');

            $options = $('.multi-options');
            $check = $('.hasPages input');
        }

        $('body').on('click', classPaginator + ' a', requestPage);
        $('body').on('click', list + ' input, ' + list + ' .selection', showOption);
        $('.multi-options').on('click', 'button', action);
    });

}(GSD.Pages = GSD.Pages || {}, GSD.App, GSD.Api, GSD.Global, GSD.$, _, Backbone, document));
