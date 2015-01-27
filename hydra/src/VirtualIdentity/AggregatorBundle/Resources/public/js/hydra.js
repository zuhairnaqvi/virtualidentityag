(function( $ ){

    $.fn.viHydra = function(method) {

        var methods = {

            init: function(options) {
                var $this = $(this);
                options = options || {};

                options.contentId                       = options.contentId             || '#content';
                options.overlayId                       = options.overlayId             || '#overlay';

                $this.data('viHydra', options);

                //check for special initialization
                var action = $this.data('action');
                if (typeof(action) != "undefined") {
                    $this.viHydra('init'+action.charAt(0).toUpperCase()+action.substr(1), options);
                }
            }

            ,initModerate: function(options) {
                //ajax updates for approved status
                var $this = $(this);
                $this.find('tbody td.col_approve a').on('click', function(event) {
                    event.preventDefault();
                    var target = $(this);
                    $.ajax({
                        type: 'POST',
                        url: target.attr('href'),
                        success: function() {
                            target.siblings().removeClass('active');
                            target.addClass('active');
                            return false;
                        }
                    });
                    return false;
                });
            }
        }

        var args = arguments;
        return this.each(function() {
            if ( methods[method] ) {
                return methods[ method ].apply( this, Array.prototype.slice.call( args, 1 ));
            } else if ( typeof method === 'object' || ! method ) {
                return methods.init.apply( this, args );
            } else {
                $.error( 'Method ' +  method + ' does not exist on jQuery.viHydra' );
            }
        });

    };
})( jQuery );

$(document).ready(function() {
    $('#content').viHydra('init');
});