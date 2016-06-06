/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */

(function (global, app, $, _, undefined) {

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
            var editor = new MediumEditor('.html_module');
            editor.subscribe('editableInput', function (event, editable) {
                $('[medium-editor-textarea-id="' + $(event.srcElement).attr('id') + '"]').val($(editable).html());
            });
        }
    });

}(GSD.Global = GSD.Global || {}, GSD.App, GSD.$, _));
