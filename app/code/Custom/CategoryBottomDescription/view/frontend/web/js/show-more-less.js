define([
	"jquery", // declare your libraries, if you are using them
], function ($) { // delare library aliases
	'use strict';
 
	return function (config, node) {
		// snippet #1
 
        var moreLess = {
            button: {
                el: $("<a>", {
                    id: "toggle-bottom-description",
                    href: "#"
                }),
                expanded_text: "Read less",
                collapsed_text: "Read more"
            },
            target: {
                el: $(node),
                height: $(node).height(),
                maxHeight: config.contentMaxHeight,
                collapsedClassName: "collapsed",
                toggleClassName: "toggle-more"
            }
        };
		// snippet #2
 
        if (moreLess.target.height > moreLess.target.maxHeight) {

            // update button text value
            moreLess.button.el.text(moreLess.button.collapsed_text);
        
            moreLess.target.el
                // add css class to apply some styling
                .addClass(moreLess.target.collapsedClassName)
                .removeClass(moreLess.target.toggleClassName)
                // append link to product description
                .parent().append(moreLess.button.el);

        }
        
        moreLess.button.el.on("click", function (e) {
            e.preventDefault();
        
            if (moreLess.target.el.hasClass(moreLess.target.collapsedClassName)) {
                moreLess.target.el.removeClass(moreLess.target.collapsedClassName);
                moreLess.button.el.text(moreLess.button.expanded_text);
            } else {
                moreLess.target.el.addClass(moreLess.target.collapsedClassName);
                moreLess.button.el.text(moreLess.button.collapsed_text);
            }
        });
	}
});