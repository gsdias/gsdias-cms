/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 * @version    1.5.1
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
            tinymce.init({
                'selector': '.html_module',
                'plugins': 'link code',
                'toolbar': 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
                'convert_urls': false,
                'setup': function(editor) {
                    editor.on('change', function() {
                        var text = tinyMCE.get(this.id).getContent({format : 'text'}),
                            strip = document.createElement('span');

                        $(strip).html(text);
                        $('[name="description"]').val(text);
                    });
                }
            });
        }
    });

}(GSD.Global = GSD.Global || {}, GSD.App, GSD.$, _));
