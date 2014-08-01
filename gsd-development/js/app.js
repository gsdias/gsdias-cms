/*jslint nomen: true, debug: true, evil: false, vars: true, browser: true, devel: true */
/*global require: false, define: false, $: false, _: false, Backbone: false, server: false */

window.GSD = window.GSD || {};

// Global event dispatcher - see http://www.michikono.com/2012/01/11/adding-a-centralized-event-dispatcher-on-backbone-js/ for discussion
GSD.globalEvents = _.extend({}, Backbone.Events);

// Global events
GSD.globalevents = {
    init: "pageinit.gsd",
    resize: "pageresize.gsd",
    scroll: "pagescroll.gsd"
};

// App settings
GSD.settings = {
    tabletPort: 768,
    tabletLand: 1024
};

(function (app, $, _, Backbone, undefined) {

    "use strict";

    _.templateSettings = {
        evaluate: /\<\%([\s\S]+?)\%\>/g,
        interpolate: /\(\(([\s\S]+?)\)\)/g
    };

    //Backbone.emulateJSON = true;

    Backbone.Collection.prototype.parse = function (response) {
        if (_.isObject(response.data)) {
            return response.data;
        } else {
            return response;
        }
    };

    Backbone.Model.prototype.parse = function (response) {
        if (_.isObject(response.data)) {
            return response.data;
        } else {
            return response;
        }
    };

    // Cached references
    app.page = {
        window: {
            el: $(window),
            width: $(window).width(),
            height: $(window).height(),
            scroll: 0
        },
        body: {
            el: $("body")
        },
        html: {
            el: $("html")
        },
        header: {
            el: $(".header")
        },
        content: {
            el: $(".main")
        },
        footer: {
            el: $(".footer")
        }
    };

    // Handle resize to cache sizes
    app.page.window.el.on('resize', function () {
        app.page.window.width = app.page.window.el.width();
        app.page.window.height = app.page.window.el.height();
        GSD.globalEvents.trigger(GSD.globalevents.resize);
        app.page.window.el.trigger(GSD.globalevents.resize);
    });

    // Handle scroll to cache scrolls
    app.page.window.el.on('scroll', function () {
        app.page.window.scroll = app.page.body.el.scrollTop() || app.page.html.el.scrollTop();
        GSD.globalEvents.trigger(GSD.globalevents.scroll);
        app.page.window.el.trigger(GSD.globalevents.scroll);
    });

    $().ready(function () {

        $(document).bind(GSD.globalevents.init, function () {
            // Add validation to any new forms that have been included
            $("form").removeData("validator");
            $("form").removeData("unobtrusiveValidation");
            //$.validator.unobtrusive.parse("form");
        });

        // GO!
        $(document).trigger(GSD.globalevents.init);
    });

}(GSD.App = GSD.App || {}, jQuery, _, Backbone));