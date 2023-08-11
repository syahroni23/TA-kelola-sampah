"use strict";

(function($, window, i) {
    $.cardProgress = function(card, options) {
        var options = $.extend({
            dismiss: false,
            dismissText: 'Cancel',
            spinner: true,
            onDismiss: function() {}
        }, options);
    
        var me = $(card);
    
        me.addClass('card-progress');
        if(options.spinner == false) {
            me.addClass('remove-spinner');
        }
    
        if(options.dismiss == true) {
            var btn_dismiss = '<a class="btn btn-danger card-progress-dismiss">'+options.dismissText+'</a>';
            btn_dismiss = $(btn_dismiss).off('click').on('click', function() {
                me.removeClass('card-progress');
                me.find('.card-progress-dismiss').remove();
                options.onDismiss.call(this, me);
            });
            me.append(btn_dismiss);
        }
    
        return {
            dismiss: function(dismissed) {
                $.cardProgressDismiss(me, dismissed);
            }
        };
    }
    
    $.cardProgressDismiss = function(card, dismissed) {
        var me = $(card);
        me.removeClass('card-progress');
        me.find('.card-progress-dismiss').remove();
        if(dismissed) dismissed.call(this, me);
    }
})(jQuery, this, 0);