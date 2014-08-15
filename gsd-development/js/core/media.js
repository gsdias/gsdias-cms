/*jslint nomen: true, debug: true, evil: false, vars: true, browser: true, devel: true */
/*global require: false, define: false, $: false, _: false, Backbone: false, server: false */

(function (media, app, api, $, _, Backbone, undefined) {

    "use strict";

    var overlay;

    var MediaCollection = Backbone.Collection.extend('');

    var MediaView = Backbone.View.extend({
        el: '#overlay',
        events: {
            'click .close': 'closeoverlay',
            'click .use': 'useasset'
        },
        
        useasset: function (e) {
            e.preventDefault();
            var use = $(e.currentTarget);
            
            $(overlay.$el.data('elm')).val(use.attr('href').substr(1));
            $(overlay.$el.data('preview')).attr('src', use.data('image'));
            this.closeoverlay();
        },
        
        closeoverlay: function () {
            this.$el.removeClass('is-visible');
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

            overlay.$el.data('elm', $(this).closest('.colA').find('input[type="hidden"]'));
            overlay.$el.data('preview', $(this).closest('.colA').find('img'));
            
            api.call($(this), 'GET', 'images', {}, function (data) {
                var datacontent = $('#overlay div');
                
                datacontent.find('*').remove();
                
                datacontent.append('<table><thead><tr><th>Imagem</th><th>Nome</th><th>Accao</th></tr></thead><tbody></tbody></table>');
                
                _.each(data, function (item) {
                    datacontent.find('tbody').append('<tr><td><image src="/gsd-assets/images/' + item.iid + '.' + item.extension + '" height="100"></td><td>' + item.name + '</td><td><a href="#' + item.iid + '" data-image="/gsd-assets/images/' + item.iid + '.' + item.extension + '" class="use">Usar</a></td></tr>');
                });
                
                overlay.render();
            });
        });
        $('.clearimage').on('click', function (e) {
            e.preventDefault();
            $(this).closest('.colA').find('input[type="hidden"]').val(0);
            $(this).closest('.colA').find('img').attr('src', '/gsd-image.php?width=auto&&height=100');
        });
    });

}(GSD.Media = GSD.Media || {}, GSD.App, GSD.Api, jQuery, _, Backbone));
