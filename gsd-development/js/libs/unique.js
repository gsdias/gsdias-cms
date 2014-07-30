/*jslint nomen: true, debug: true, evil: false, vars: true, browser: true, devel: true */
/*global require: false, define: false, $: false, _: false, Backbone: false, server: false */

(function (unique, $, undefined) {

    "use strict";

    Array.prototype.unique = function () {
        var a = [];
        var l = this.length;
        for (var i = 0; i < l; i++) {
            for (var j = i + 1; j < l; j++) if (this[i] === this[j]) j = ++i;
            a.push(this[i]);
        }
        return a;
    };
    $.extend({
        getUrlVars: function (url) {
            url = url ? url : window.location.href;
            var vars = [],
                hash;
            var hashes = url.slice(url.indexOf('?') + 1).split('&');
            for (var i = 0; i < hashes.length; i++) {
                hash = hashes[i].split('=');
                vars.push(hash[0]);
                vars[hash[0]] = hash[1];
            }
            return vars;
        },
        getUrlVar: function (name, url) {
            return $.getUrlVars(url)[name];
        }
    });
    return this;
}(PRO.Unique = PRO.Unique || {}, jQuery));
