/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 * @version    1.2
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */

(function (global, app, $, _, Backbone, undefined) {

    'use strict';

    global.updateHistory = function (url, title) {
        history.pushState(null, title, url);
    };

    global.resizemain = function () {
    };

    app.page.window.el.on(GSD.globalevents.resize, global.resizemain);

    $(document).bind(GSD.globalevents.init, function () {
        global.resizemain();

        if (app.isCMS) {
            tinymce.init({
                selector: '.html_module',
                plugins: 'code',
                toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image'
            });
        }
    });

}(GSD.Global = GSD.Global || {}, GSD.App, GSD.$, _, Backbone));
