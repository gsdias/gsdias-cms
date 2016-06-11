/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */

(function (global, app, $, _, undefined) {

    'use strict';

    var transitionend = Modernizr.prefixed('transitionend');

    global.updateHistory = function (url, title) {
        history.pushState(null, title, url);
    };

    global.resizemain = function () {
    };

    app.page.window.el.on(GSD.globalevents.resize, global.resizemain);

    $(document).bind(GSD.globalevents.init, function () {
        global.resizemain();

        if (app.isCMS) {
            var editor = new MediumEditor('.html_module', {
                toolbar: {
                    buttons: ['bold', 'italic', 'underline', 'unorderedlist', 'anchor', 'h2', 'h3', 'quote', 'removeFormat']
                }
            });
            editor.subscribe('editableInput', function (event, editable) {
                $('[medium-editor-textarea-id="' + $(event.srcElement).attr('id') + '"]').val($(editable).html());
            });

            $('.messages .progress').on('webkitAnimationEnd oAnimationEnd MSAnimationEnd animationend', function () {
                $(this).closest('.messages').addClass('fades');
            });
            $('.messages').on('click', '.fa-close', function () {
                $(this).closest('.messages').addClass('fades');
            });
            $('.menu').on('click', function () {
                $('.nav').toggleClass('active');
            });
        }
    });

}(GSD.Global = GSD.Global || {}, GSD.App, GSD.$, _));
