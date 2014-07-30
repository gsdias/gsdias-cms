/**
 * jQuery Radio Check Custom
 * This jQuery plugin gives a style to input tag with the type of radio and checkbox
 * @name radiocheck.js
 * @author Gonçalo Silva Dias
 * @version 1.2
 * @date April 9, 2012
 * @category jQuery plugin
 * @copyright (c) 2012 Gonçalo Silva Dias
 */

;(function($) {

  var rcfunctions = {
    init : function(settings) {
      this.reset(settings);
    },
    //Resets all the inputs and clear the styles
    reset : function(settings) {
      var className = "." + settings.className;
      $(className).closest('form').find('input[type="reset"]').bind('click.GU', function() {
        var form = $(this).closest('form');
        form.find(className+' .selected').removeClass('selected');
        form.find(className+' input').each(function() {
          var elThis = $(this), span = elThis.closest(className).find('span'), label = $('label[for="' + elThis.attr('id') + '"]');
          if(elThis.data('default')) {
            elThis.check();
            span.addClass('selected');
            if(settings.label)
              label.addClass('selected');
          } else {
            elThis.uncheck();
            if(settings.label)
              label.removeClass('selected');
          }
        });
      });
    },
    //Sets the right events to the input and the label
    events : function(elThis, settings) {
      var className = "." + settings.className,
          span = elThis.closest(className).find('span'), label = $('label[for="' + elThis.attr('id') + '"]');
      elements = [];
      elements.push(elThis);
      elements.push(label);

      if('radio' == elThis.attr('type'))
        this.radio(elThis, settings);
      if('checkbox' == elThis.attr('type'))
        this.check(elThis, settings);

      for(var total = elements.length; 0 < total; total--) {
        element = elements.pop();
        element.bind({
          'mouseover.GU focus.GU' : function() {
            if(settings.hover)
              span.addClass('hover');
            if(settings.labelhover)
              label.addClass('hover');
          },
          'mouseout.GU blur.GU' : function() {
            if(settings.hover)
              span.removeClass('hover');
            if(settings.labelhover)
              label.removeClass('hover');
          }
        });
      }
    },
    select : function(elThis, settings){
      elThis.bind('click.GU', function() {
        var block = $('#'+elThis.data('selectId'));
        block.toggle()
        elThis.toggleClass('selected');
      });
    },
    list : function(elThis, list, settings){
      list.children().children().bind('click.GU', function() {
        var option = $(this),
            select = elThis.parent().find('select');

        select.val(option.data('value'));
        elThis.find('span').html(option.html())
        list.toggle();
        elThis.toggleClass('selected');
      });
    },
    //Create the event to the input type radio
    radio : function(elThis, settings) {
      var className = "." + settings.className;
      elThis.bind('click.GU', function() {
        var span = elThis.parent().find('span'), label = $('label[for="' + elThis.attr('id') + '"]'), allRadios = $(className+' input[type="radio"][name="' + elThis.attr('name') + '"]'), allLabels = $('.GULabel-' + elThis.attr('name'));
        allRadios.prev().removeClass('selected');
        span.addClass('selected');
        if(settings.label) {
          allLabels.removeClass('selected');
          label.addClass('selected');
        }
      });
    },
    //Create the event to the input type checkbox
    check : function(elThis, settings) {
      elThis.bind('click.GU', function() {
        var span = elThis.parent().find('span'), label = $('label[for="' + elThis.attr('id') + '"]');
        span.toggleClass('selected');
        if(settings.label)
          label.toggleClass('selected');
      });
    }
  };

  //Object itself
  $.fn.radiocheck = function(options) {

    //Sets the settings with the default values if necessary
    var settings = $.extend({
      'hover' : 1,
      'label' : 0,
      'labelhover' : 0,
      'className' : this.attr('class')
    }, options);

    //Initializates the function that controles all the actions
    rcfunctions.init(settings);

    return this.each(function() {
      var elThis = $(this), choosenClass = settings.className, label = $('label[for="' + elThis.attr('id') + '"]');

      //Creates the structure to personalize the input
      if(elThis.is('input')) {
        elThis.css('opacity', 0).wrap('<div class="' + choosenClass + '"/>').removeClass(choosenClass).parent().prepend('<span/>');

        var span = elThis.parent().find('span');
        span.addClass('radio' == elThis.attr('type') ? 'radio' : 'check');

        label.addClass('GULabel').addClass('GULabel-' + elThis.attr('name'));

        //Verify if the input is checked, gives the selected class and saves the default value
        if('string' == typeof elThis.attr('checked')) {
          elThis.data('default', true);
          span.addClass('selected');
          if(settings.label)
            label.addClass('selected');
        }
        rcfunctions.events(elThis, settings);
      }
      if(elThis.is('select')) {
        elThis.hide().wrap('<div class="' + choosenClass + '"/>').removeClass(choosenClass).parent().append('<a/>').find('a').append('<span/>').append('<cite/>');
        var a = elThis.parent().find('a'), listId = 'undefined' == typeof elThis.attr('id') ? choosenClass + '-' + new Date().getTime() : choosenClass + '-' + elThis.attr('id');
        a.addClass('select').data('selectId', listId);
        a.find('span').html(elThis.find('option').html());
        a.css({
          'width' : elThis.innerWidth(),
          'height' : 30
        }).parent().css({
          'width' : elThis.innerWidth(),
          'height' : 30
        });

        $('body').append('<div class="'+choosenClass+'-select" id="' + listId + '"/>');
        $('#'+listId).append('<ul/>').hide();
        var block = $('#'+listId).children();
        elThis.children().each(function(){
          var option = $(this);
          block.append('<li/>');
          block.children(':last')
               .html(option.html())
               .data('value',option.val());
        });
        block = $('#'+listId);
        var top = elThis.parent().offset().top + elThis.parent().innerHeight(),
            left = elThis.parent().offset().left;
        block.css({top:top,left:left, position:'absolute', width:elThis.innerWidth()});
        rcfunctions.select(a, settings);
        rcfunctions.list(a, block, settings);
      }
    });
  };
        jQuery.fn.extend({
                check: function() {
                        var span = $(this).parent().find('span');
                                        span.addClass('selected');
                                        $(this).attr('checked','checked');
                },
                uncheck: function() {
                        var span = $(this).parent().find('span');
                                        span.removeClass('selected');
                                        $(this).removeAttr('checked');
                }
        });
})(jQuery);
