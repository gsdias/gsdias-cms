/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 * @version    1.3
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */

(function (api, $, undefined) {

    'use strict';

    var url = '/gsd-api/',

        serverError = function () {

            api.loading();

            alert('Não foi possível comunicar com o servidor. Tente mais tarde');

        },

        success = function () {

            api.loading();

        };

    api.loading = function (elem) {
        if ('object' !== typeof elem) {
            $('.loadingimg').hide();
            return;
        }
        var size = [elem.innerWidth(), elem.innerHeight()];
        if (!$('.loadingimg').length) {
            $('body').append('<img src="/gsd-resources/images/ajax-loader.gif" class="loadingimg"/>');
        }

        $('.loadingimg').css({
            position: 'absolute',
            display: 'block',
            width: 'auto',
            height: '50',
            left: parseInt((elem.offset() || {}).left + (size[0] / 2) - 62, 10),
            top: parseInt((elem.offset() || {}).top + (size[1] / 2) - 62, 10),
            zIndex: 10
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
        if ('undefined' !== typeof file) {
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

}(GSD.Api = GSD.Api || {}, GSD.$));
