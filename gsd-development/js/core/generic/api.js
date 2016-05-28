/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
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
            $('#loading').hide();
            return;
        }

        if (!$('#loading').length) {
            $('body').append('<div id="loading"><img src="/clock.svg"></div>');
            //            <svg version="1.1" id="loader-1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="140px" height="140px" viewBox="0 0 50 50" style="enable-background:new 0 0 50 50;" xml:space="preserve">
            //    <path fill="#276876" d="M25.251,6.461c-10.318,0-18.683,8.365-18.683,18.683h4.068c0-8.071,6.543-14.615,14.615-14.615V6.461z">
            //        <animateTransform attributeType="xml" attributeName="transform" type="rotate" from="0 25 25" to="360 25 25" dur="0.6s" repeatCount="indefinite"/>
            //    </path>
            //</svg>
            //            <img src="/clock.svg">
        }
        $('#loading').show();
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
