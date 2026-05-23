(function($) {
    $.fn.ReignMore = function(reduceWidth) {
        $(this).each(function() {
            $(this).addClass("rg-responsive-menu");
            alignMenu(this);
            var robj = this;

            $(window).on('resize', function() {
                $(robj).append($($($(robj).children('li.hideshow')).children('ul')).html());
                $(robj).children('li.hideshow').remove();
                alignMenu(robj);
            });

            function alignMenu(obj) {
                var w = 0;
                var mw = $(obj).width() - reduceWidth;
                var i = -1;
                var menuhtml = '';
                jQuery.each($(obj).children(), function() {
                    i++;
                    w += $(this).outerWidth(true);
                    if (mw < w) {
                        menuhtml += $('<div>').append($(this).clone()).html();
                        $(this).remove();
                    }
                });

                $(obj).append(
                    '<li class="hideshow menu-item-has-children header-more-dropdown-toggle"><a class="rg-more-button dropdown-toggle" href="#"><i class="far fa-ellipsis-h"></i></a><ul class="sub-menu header-more-dropdown-menu">' + menuhtml + '</ul></li>');
                $(obj).children("li.hideshow ul").css("top",
                    $(obj).children('li.hideshow').outerHeight(true) + 'px');

                if ($(obj).find('li.hideshow').find('li').length > 0) {
                    $(obj).find('li.hideshow').show();
                } else {
                    $(obj).find('li.hideshow').hide();
                }
            }

        });

    }
}(jQuery));

(function($) {
    $.fn.BuddyPressMenu = function(reduceWidth) {
        $(this).each(function() {
            //alignMenu( this );
            var elem = this,
                $elem = $(this);

            window.addEventListener('resize', run_alignMenu);
            run_alignMenu();

            function run_alignMenu() {
                $elem.append($($($elem.children('li.hideshow')).children('ul')).html());
                $elem.children('li.hideshow').remove();
                alignMenu(elem);
            }

            function alignMenu(obj) {
                var self = $(obj),
                    w = 0,
                    i = -1,
                    menuhtml = '',
                    mw = self.width() - reduceWidth;

                $.each(self.children(), function() {
                    i++;
                    w += $(this).outerWidth(true);
                    if (mw < w) {
                        menuhtml += $('<div>').append($(this).clone()).html();
                        $(this).remove();
                    }
                });

                self.append('<li class="hideshow menu-item-has-children1"><a class="more-button" href="#"><i class="far fa-ellipsis-h"></i></a><ul class="sub-menu">' + menuhtml + '</ul></li>');

                if (self.find('li.hideshow').find('li').length > 0) {
                    self.find('li.hideshow').show();
                } else {
                    self.find('li.hideshow').hide();
                }
            }

        });
    }
}(jQuery));