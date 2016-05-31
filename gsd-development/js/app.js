/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */

window.GSD = window.GSD || {};

// Global event dispatcher - see http://www.michikono.com/2012/01/11/adding-a-centralized-event-dispatcher-on-backbone-js/ for discussion
GSD.globalEvents = _.extend({}, Backbone.Events);

// Global events
GSD.globalevents = {
    init: 'pageinit.gsd',
    frontend: 'pagefrontend.gsd',
    resize: 'pageresize.gsd',
    scroll: 'pagescroll.gsd',
    updateInternals: 'updateinternals.gsd'
};

// App settings
GSD.settings = {
    tabletPort: 768,
    tabletLand: 1024
};

GSD.$ = jQuery.noConflict(true);

window.jQuery = window.$ = {};

(function (app, $, _, Backbone, undefined) {

    'use strict';

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
            el: $('body')
        },
        html: {
            el: $('html')
        },
        header: {
            el: $('.header')
        },
        content: {
            el: $('.main')
        },
        footer: {
            el: $('.footer')
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
        var loading = $('<svg width="30px" height="30px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" class="uil-clock"><rect x="0" y="0" width="100" height="100" fill="none" class="bk"></rect><circle cx="50" cy="50" r="30" fill="#fff" stroke="#276876" stroke-width="8px"></circle><line x1="50" y1="50" x2="50" y2="30" stroke="#276876" stroke-width="9" stroke-linecap="round"><animateTransform attributeName="transform" type="rotate" from="0 50 50" to="360 50 50" dur="2s" repeatCount="indefinite"></animateTransform></line></svg>');
        $(document).bind(GSD.globalevents.init, function () {
            // Add validation to any new forms that have been included
            $('form').removeData('validator');
            $('form').removeData('unobtrusiveValidation');
            $('form').validate({
                onkeyup: false,
                submitHandler: function (form) {
                    var $submit = $(form).find('[type="submit"]'),
                        left = $submit.offset().left,
                        top = $submit.offset().top,
                        width = $submit.outerWidth() / 2;

                    loading.css({ position: 'absolute', left: left + width - 15, top: top });

                    app.page.body.el.append(loading);
                    form.submit();
                }
            });
            $.validator.messages.required = GSD.messages.required;
            $.validator.messages.email = GSD.messages.email;
            //$.validator.unobtrusive.parse('form');
        });

        // GO!
        $(document).trigger(GSD.globalevents.init);
    });

}(GSD.App = GSD.App || {}, GSD.$, _, Backbone));
