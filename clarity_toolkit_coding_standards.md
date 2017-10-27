### Event based methods must be bound to the component

When calling a method, it must be bound to the component itself. This ensures that closures are preserved and avoids scope issues. It also means that functions are more likely to be resuable.

e.g.

**This is not acceptable**

```js
toggleMenu: function() {
     var menu = $('.js-menu-toggle');
     if(menu.length) {
         menu.on('click', function() {
             //do something
         })
     }
 }

 toggleMenu();
```

**This is acceptable**

```js
this.$el = $('.js-menu'),
this.$el.on('click', '.js-menu__toggle',  ['params', 'go', 'here'], this.toggleMenu.bind(this)); // toggleMenu()

this.toggleMenu: function() {
    // do something
}
```

Using `bind(this)` isn't always required but it allows you to pass the 'this' context into the method and thereby provides access to the parent scope.

### Each function/method should only have one job.

A function should be written to do one thing and one thing only. This improves modularity and readability.

### Always use type comparison
When writing `if` statements, avoid using `==` if using `===` is possible, ensuring that code is strongly typed helps to maintain code parity, also no type conversion is carried out when using `===` which helps to increase performance.

See this article for more information: [Comparison Operators tutorial](http://www.c-point.com/javascript_tutorial/jsgrpComparison.htm)

### If listing errors or warnings appear during build. Make sure you correct them.

The build tool uses StandardJS to ensure that code quality is maintained. If an error or a warning appears on the build then ensure you correct them. If time is a factor then warnings can be temporarily ignored but ensure you add them to your technical debt.

### Comment everything as comprehensively as possible
In large projects such as this one, it's vital that everyone understands what the code is doing. Therefore if you write a piece of JavaScript then make sure you document it thoroughly.

**This is not acceptable**

```js
// Menu toggle
toggleMenu: function(el, speed) {
   [...]
}
``

**This is acceptable**

```js
	/**
	* Menu toggle - opens and closes the mobile nav menu
	* @method toggleMenu
	* @return true
	* @args el(string) = The element to target (note: This is a selector, not an object)
	* @args speed(int) = The speed (in milliseconds) it should take the menu to open
	*/

	toggleMenu: function() {
	   [...]
	}
```

### Keep local variables in one place

When instantiating a local function, ensure that you keep all the local variables at the top of the function. This will improve readability and will eliminate the need for hoisting.

** This is not acceptable **

```js
var function = bigDamnHeroes() {
	var mal = 'in the nick of time';
	if(mal === 'in the nick of time') {
		var river = 'our witch';
		var zoe = 'big damn heroes, sir';
		console.log(zoe);
	}
}
```

**This is acceptable**

```js
var function = bigDamnHeroes() {
	var mal = 'in the nick of time',
		zoe = 'big damn heroes, sir',
		river = 'burning at the stake';

	if(mal === 'in the nick of time') {
		river = 'our witch';
		console.info(zoe);
	}
}
```
### Try to write encapsulated code. Use jQuery plugin architecture where possible.

Example:

```js
/* global jQuery */

;(function ($) {
 /**
 * Ensures that in a set of elements, they all have an equal height (equal to the height of the largest elemement)
 *
 * Usage: Simply add your container element to script-loader.js and add .moji_equaliser() on to it.
 * Make sure you reference the container and child elements. e.g. $('.c-news-list > .js-article-item').moji_equaliser()
 *
 */
  $.fn.moji_equaliser = function () {
    var container = this
    var tallestHeight = 0
    var heightCheck = 1
    for (var i = 0; i < container.length; i++) {
      var height = $(container[i]).outerHeight(true)
      if (height > tallestHeight) tallestHeight = height
      if (heightCheck === container.length) {
        // All items accounted for, now make all items the same height
        $(container).css('height', tallestHeight + 'px')
      } else {
        heightCheck++
      }
    }

    return container
  }
})(jQuery)
```

More information on jQuery plugins can be found [here](https://learn.jquery.com/plugins/basic-plugin-creation/)

### If it can be global, make it global

When writing javscript tools. Think if it is something that could be re-used in another component? If so then make it into a plugin and put it in the /src/globals/js folder.

