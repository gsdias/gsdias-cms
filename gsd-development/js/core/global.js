/*jslint nomen: true, debug: true, evil: false, vars: true, browser: true, devel: true */
/*global require: false, define: false, $: false, _: false, Backbone: false, server: false */

(function (global, app, $, _, Backbone, undefined) {

    "use strict";

    global.resizemain = function () {
        var height = app.page.window.height - app.page.header.el.height() - app.page.footer.el.height();
        app.page.content.el.css({ height: height });
    };
    
    app.page.window.el.on(GSD.globalevents.resize, global.resizemain);
    
    $(document).bind(GSD.globalevents.init, function () {
        global.resizemain();
    });

}(GSD.Global = GSD.Global || {}, GSD.App, jQuery, _, Backbone));