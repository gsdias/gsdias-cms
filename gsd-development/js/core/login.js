/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
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
        },

        refreshState = function() {
            var value = this.value,
                numberStrong = 1,
                $container = $(this).closest('.colA');

            if (value.length >= 12 && checkHasNumber(value) && checkHasUpperAndLower(value) && checkSpecialChar(value)) {
                numberStrong = 5;
            } else if (value.length >= 10 && checkHasNumber(value) && checkHasUpperAndLower(value)) {
                numberStrong = 4;
            } else if (value.length >= 8 && checkHasNumber(value) && checkHasUpperAndLower(value)) {
                numberStrong = 3;
            } else if (value.length >= 8 && (checkHasNumber(value) || checkHasUpperAndLower(value))) {
                numberStrong = 2;
            }
            addCheckClass(numberStrong, $container);
        },

        addCheckClass = function(numberStrong, $container) {
            var passCells = $container.find('.gsd-complexity'),
                givenClass = 'gsd-complexity';

            passCells.removeClass();
            switch (numberStrong) {
                case 1:
                    givenClass += ' red';
                break;
                case 2:
                    givenClass += ' orange';
                break;
                case 3:
                    givenClass += ' yellow';
                break;
                case 4:
                    givenClass += ' blue';
                break;
                case 5:
                    givenClass += ' green';
                break;
            }
            passCells.addClass(givenClass);
        },

        checkSpecialChar = function(value) {
            value = value.replace(/[a-zA-Z0-9]/g, '');
            return value.length > 0;
        },

        checkHasNumber = function(value) {
            return value.replace(/\D/g, '').length > 0;
        },

        checkHasUpperAndLower = function(value) {
            return !(_.isNull(value.match(/[a-z]/g)) || _.isNull(value.match(/[A-Z]/g)));
        };

    $('.gsd-recover, .gsd-login').on('click', showForm);
    $('.gsd-pass-toggle').on('click', togglePassword);
    $('.gsd-password').on('keyup', refreshState);
}(GSD.App, GSD.Api, GSD.$, _));
