/*jslint nomen: true, debug: true, evil: false, vars: true, browser: true, devel: true */
/*global require: false, define: false, $: false, _: false, Backbone: false, server: false */

(function (media, app, api, $, _, Backbone, undefined) {

    "use strict";

    var overlay;

    var MediaCollection = Backbone.Collection.extend('');

    var MediaView = Backbone.View.extend({
        el: '#overlay',
        events: {
            'click this': 'closeoverlay'
        },
        closeoverlay: function () {

        },
        render: function () {
            this.$el.addClass('is-visible');
            return this;
        }
    });

    $(document).bind(GSD.globalevents.init, function () {
        overlay = new MediaView();
        $('.findimage').on('click', function (e) {
            e.preventDefault();

            api.call($(this), 'GET', 'images', {}, function (data) {
                overlay.render();
                $('#overlay section div').html(data);
            });
        });
    });

}(GSD.Media = GSD.Media || {}, GSD.App, GSD.Api, jQuery, _, Backbone));
