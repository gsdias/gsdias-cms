/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 * @version    1.2
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */

(function (media, app, api, $, _, Backbone, undefined) {

    'use strict';

    var overlay,

        MediaModel = Backbone.Model.extend({
            urlRoot: '/api/image',
            idAttribute: 'iid',

            initialize: function () {
                this.on('invalid', function (model, error) {
                    alert(error);
                });
            },

            validate: function (attrs) {
                if (isNaN(attrs.vid) || isNaN(attrs.uid) || !attrs.vid.length || !attrs.uid.length) {
                    return 'Falta inserir dados obrigatorios ou inseriu valores errados';
                }
            }

        }),

        MediaView = Backbone.View.extend({
            tagName: 'tr',
            events: {
                'click .close': 'closeoverlay',
                'click .use': 'useasset',
                'submit .search': 'filter'
            },
            initialize: function () {
                this.template = _.template($('#imageBlock').html());
            },
            useasset: function (e) {
                e.preventDefault();
                var use = $(e.currentTarget);
                $(overlay.$el.data('elm')).val(use.attr('href').substr(1));
                $(overlay.$el.data('elm')).next().addClass('is-hidden');
                $(overlay.$el.data('preview')).attr('src', use.data('image'));
                $(overlay.$el.data('preview')).removeClass('is-hidden');
                overlay.closeoverlay();
                $(overlay.$el.data('elm')).trigger('change');
            },

            render: function () {
                this.$el.html(this.template(this.model.attributes));
                return this;
            }

        }),

        MediasView = Backbone.View.extend({
            el: '#overlay',
            events: {
                'click .close': 'closeoverlay',
                'submit .search': 'filter'
            },

            filter: function (e) {
                e.preventDefault();

                api.call(this.$el, 'GET', 'images', { search: this.$('[name="search"]').val() }, function (data) {
                    var datacontent = $('#overlay .content');

                    datacontent.find('*').remove();

                    datacontent.append('<table><thead><tr><th>Imagem</th><th>Nome</th><th>Acção</th></tr></thead><tbody></tbody></table>');

                    _.each(data, function (item) {
                        var media = new MediaView({
                            model: new MediaModel({
                                iid: item.iid, name: item.name, extension: item.extension
                            })
                        });

                        media.render();
                        datacontent.find('tbody').append(media.$el);
                    });

                });
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
        overlay = new MediasView();
        $('body').on('click', '.findimage', function (e) {
            e.preventDefault();

            overlay.$el.data('elm', $(this).closest('.image_block').find('input[type="hidden"]'));
            overlay.$el.data('preview', $(this).closest('.image_block').find('img'));

            api.call($(this), 'GET', 'images', {}, function (data) {
                var datacontent = $('#overlay .content');

                datacontent.find('*').remove();

                datacontent.append('<table><thead><tr><th>Imagem</th><th>Nome</th><th>Acção</th></tr></thead><tbody></tbody></table>');

                _.each(data, function (item) {
                    var media = new MediaView({
                        model: new MediaModel({
                            iid: item.iid, name: item.name, extension: item.extension
                        })
                    });

                    media.render();
                    datacontent.find('tbody').append(media.el);
                });

                overlay.render();
            });
        });
        $('body').on('click', '.clearimage', function (e) {
            e.preventDefault();
            $(this).closest('.image_block').find('input[type="hidden"]').val('0');
            $(this).closest('.image_block').find('input[type="text"]').removeClass('is-hidden');
            $(this).closest('.image_block').find('img').attr('src', '/gsd-image.php?width=auto&&height=100');
            $(this).closest('.image_block').find('img').addClass('is-hidden');
        });
    });

}(GSD.Media = GSD.Media || {}, GSD.App, GSD.Api, GSD.$, _, Backbone));
