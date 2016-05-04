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
        },
        togglePassword = function () {
            var $field = $(this),
                isHidden = $field.hasClass('fa-eye');

            if (isHidden) {
                $field.siblings('input').attr('type', 'text');
            } else {
                $field.siblings('input').attr('type', 'password');
            }
            $field.toggleClass('fa-eye', !isHidden);
            $field.toggleClass('fa-eye-slash', isHidden);
        };

    $('.gsd-recover, .gsd-login').on('click', showForm);
    $('.gsd-password').on('click', togglePassword);
}(GSD.App, GSD.Api, GSD.$, _));
