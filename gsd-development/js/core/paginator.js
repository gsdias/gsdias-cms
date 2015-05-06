/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 * @version    1.2
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */

(function (pages, app, api, global, $, _, Backbone, document, undefined) {

    'use strict';

    var tbody = {},
        classPaginator = '.paginator',

        requestPage = function (e) {
            e.preventDefault();

            var page = this.href.split('='),
                search = $('[name="search"]').val();

            api.call($(e.currentTarget), 'GET', 'pages', { page: page[1], type: tbody.type, search: search }, function (response) {
                tbody.el.empty();

                _.each(response.data.list, function (item) {
                    tbody.el.append(_.template($('#' + tbody.type + 'Block').html(), item));
                });

                $(classPaginator).replaceWith(response.data.paginator);

                global.updateHistory('?page=' + page[1]);
                $(document).trigger(GSD.globalevents.updateInternals);

                api.loading();
            }, function () {
                api.loading();
            });
        };

    $(document).bind(GSD.globalevents.init, function () {
        var $tbody = $('.hasPages tbody');

        if ($tbody.length) {
            tbody.el = $tbody;
            tbody.type = $('.hasPages').data('type');
        }

        $('body').on('click', classPaginator + ' a', requestPage);
    });

}(GSD.Pages = GSD.Pages || {}, GSD.App, GSD.Api, GSD.Global, GSD.$, _, Backbone, document));
