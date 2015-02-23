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
    });

}(GSD.Global = GSD.Global || {}, GSD.App, jQuery, _, Backbone));
