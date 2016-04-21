/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 * @version    1.5
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */

(function (app, api, $, _, undefined) {
    'use strict';

    var hiddenClass = 'is-hidden',
        $loginForm = $('.login-form'),
        $recoverForm = $('.recover-form'),
        showForm = function (e) {
            var showRecover = $(this).hasClass('gsd-recover');
            
            e.preventDefault();
            $loginForm.toggleClass(hiddenClass, showRecover);
            $recoverForm.toggleClass(hiddenClass, !showRecover);
        };

    $('.gsd-recover, .gsd-login').on('click', showForm);
}(GSD.App, GSD.Api, GSD.$, _));
