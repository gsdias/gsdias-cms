/*jslint nomen: true, debug: true, evil: false, vars: true, browser: true, devel: true */
/*global require: false, define: false, $: false, _: false, Backbone: false, server: false */

(function (api, app, $, _, Backbone, undefined) {

    "use strict";

    var url = '/gsd-api/',
        messageBlock = $('.favicons');

        var serverError = function () {

            api.loading();

            alert("Não foi possível comunicar com o servidor. Tente mais tarde");

        };

        var success = function () {

            api.loading();

        };

        api.loading = function (elem) {
            if ('object' !== typeof elem) {
                $('.loadingimg').hide();
                return;
            }
            var size = [elem.innerWidth(), elem.innerHeight()];
            if (!$('.loadingimg').length) {
                $('body').append('<img src="/resources/images/ajax-loader.gif" class="loadingimg"/>');
            }

            $('.loadingimg').css({
                position: 'absolute',
                display: 'block',
                left: parseInt((elem.offset() || {}).left + (size[0] / 2) - 62, 10),
                top: parseInt((elem.offset() || {}).top + (size[1] / 2) - 62, 10)
            });
        };

        api.displayMessage = function (message) {

            if ('undefined' === typeof message || !message.length) {
                return;
            }

            alert(message);

            var timing = null,
                hide = function () {
                    messageBlock.slideUp(1000);
                };

            messageBlock.html('').append('<span>' + message + '</span>');

            if (messageBlock.children().length) {
                messageBlock.slideDown(1000, function () {
                    timing = setTimeout(hide, 5000);
                });
            }
            messageBlock.hover(function () {
                clearTimeout(timing);
            }, function () {
                messageBlock.slideUp(1000);
            });
        };

        api.displayError = function (json) {

            if (0 > json.error) {
                alert(json.message);
            }

            return 0 === json.error;
        };

        api.call = function (elem, method, service, fields, cb, fb, file) {

            var fallback = function (json) {

                if ('function' === typeof fb) {
                    fb(json);
                } else {
                    serverError();
                }

            },

                callback = function (json) {

                    success();

                    if ('function' === typeof cb) {
                        cb(json);
                    }

                };

            if ('undefined' !== typeof elem && null !== elem) {
                api.loading(elem);
            }
            if (typeof file !== 'undefined') {
                $.ajax({
                    url: url + service,
                    type: method,
                    timeout: 30000,
                    error: fallback,
                    data: fields,
                    success: callback,
                    dataType: 'json',
                    processData: false, // Don't process the files
                    contentType: false // Set content type to false as jQuery will tell the server its a query string request
                });
            } else {
                $.ajax({
                    url: url + service,
                    type: method,
                    timeout: 10000,
                    error: fallback,
                    data: fields,
                    success: callback
                });
            }
        };

    if (!messageBlock.length) {
        $('<div class="favicons">').appendTo('body');
        messageBlock = $('.favicons');
    }

    if (messageBlock.children().length) {
        displayMessage(messageBlock.html());
    }

}(GSD.Api = GSD.Api || {}, GSD.App, jQuery, _, Backbone));