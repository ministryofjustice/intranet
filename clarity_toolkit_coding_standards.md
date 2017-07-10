#Clarity Toolkit Coding Standards

Please note, this is not an instruction guide on how to set the site or project up. This is a coding guideline document for the developers. Please see the [Readme](readme.md) file for more information.

## General coding best practice

### A function/method/mixin should only ever do one thing

When writing your code, look at what it's doing. If it's doing more than one thing, break it down into it's constituent parts and make sure all code is reusable. If something is specific to a component feel free to write it inside the component file but think long and hard about if this new code could be used elsewhere. If it can, it belongs in global.

### Tab spacing should be uniform

In the PHP it should be 4 spaces per tab and in all other files it should be 2 spaces per tab. Note: This excludes markdown as it has it's own tab structure for formatting.

### Stay DRY at all times.

DRY (Don't Repeat Yourself) is the best way to keep code from growing too complex. Before you write a new function/method/component/class find out if there is something that already exists that you can use or extend.

### Terser is better (to a point)

If you can write something in 10 lines of code or 2 lines of code, try to aim for two lines. However make sure that what you write is still understandable to someone who is new to the code.

### Document everything
Everything you write should be covered by documentation. This is especially true for new components as the documentation is how other developers know how to use them and where.

Note: CSS is very self-explanatory so more basic comments are acceptable here unless you are doing something out of the ordinary.

### Code comments should be helpful
In relation to the above. Whenever you add code comments, make sure they are useful, try to follow Docblokr conventions where possible. Don't worry about comment lengths in front end code (CSS/JS) as these comments will be stripped out when they are minfied. Where it's relevent also try to tag your comments like so:

//TODO: This is something that needs to be done in the future (note: This should also be added to either tech debt or JIRA)

//BUG: This is a bug that has been spotted but has not yet been fixed (note: This should also be added to either tech debt or JIRA)

//HACK: If you have had to do something janky because you didn't have the time or ability to do it properly. Add this comment so it can be easily found in future refactors (Note: This should also be added to the tech debt)

//FIXME: This is broken code (not just a bug), it is primarily used if you are going to come back to it soon or if you want to flag it for another developer to take a look at.

//XXX: This is where other developers can highlight questionable code. It is useful in code reviews.

Note: All tags should be in capitals. Depending in your dev environment, your editor should pick up the above tags and highlight them. Some (Webstorm for example) can even find all the specific tags in a project so you can see a list of everything that needs to be done.

##CSS

### All components styles will be nested inside a namespaced class and concatenated using BEM notation

Stylus can concatenate anything using the & operator, make sure that you use it properly in order to ensure minimal repetition and over-specificity.

**This is not acceptable**

CSS

	.c-product-tags {
	    text-align: center;
	    padding-left: 20px;
	    padding-right: 20px;
	}

	.c-product-tags__title {
	    margin-top: 0;
	    margin-bottom: 35px;
	}



**This is acceptable**

CSS

	.c-product-tags {
	    text-align: center;
	    padding-left: 20px;
	    padding-right: 20px;

	    &__title {
	        margin-top: 0;
	        margin-bottom: 35px;
	    }
	}

__NOTE:__ Utilities and objects may occasionally require two classes to work.

e.g.

	.o-title {
		margin-bottom: 2rem;
		&--page {
			font-weight: bold;
		}
	}

	<h1 class="o-title o-title--page">Welcome</h1>

[View on codepen](http://codepen.io/foxleigh81/pen/MpWddK)

If the first 'o-title' is omitted the margin-bottom property will not be applied

### Stick to the following naming convention prefixes
All components should be prefixed with a 'c'. All reusable single items (like a button for example) is an object and should be prefixed with 'o' Any class which modifies the behaviour of another item is a utility class and should be prefixed with'u'. Layout Items (which set the general layout of a template) should begin with 'l'. Template items will allow you to define overrides on components for specific templates and will begin with 't'.

e.g.

**Component:** `.c-product-tags`
**Object:** `.o-button`
**Utility:** `.u-highlight`
**Layout:** `.l-primary`
**Template:** `.t-primary`

In fact...

### Avoid 'raw' classes

A raw class is any class that doesn't have a prefix and/or namespace on it and should be avoided at all costs. As this is a wordpress site and wordpress generates it's own classes occasionally, there will be times where it is unavoidable but you should never add a raw class yourself.

### A Mobile-first approach must be used unless absolutely impossible

Stylus has a plugin called ['Rupture'](http://jescalan.github.io/rupture/), this makes media queries incredibly simple by slicing the page up into named breakpoints and then referencing that breakpoint by using a mixin:

e.g.

	+above(m) {
		margin: 0;
	}

The Clarity toolkit has 7 breakpoints which are as follows:

- `xxs` : `320px`
- `xs` : `410px`
- `s` : `500px`
- `m` : `740px`
- `l` : `1020px`
- `xl` : `1280px`
- `xxl` : `1700px;`

Additional breakpoints can be explicity specified if needed but please do this as a last resort.

e.g.

	+above('990px') {
		display: none;
	}

Just like with normal media queries, a mobile-first approach should be used, use of `+below()' or '+between()' is possible but discouraged. Only use these if it's absolutely needed and be prepared to justify it in a code review.

e.g.

**This is not acceptable**

	&__tags {
	     li {
	        margin-right: 6px;
	        margin-bottom: 10px;
	        display: inline-block;
	        +below(m) {
	            margin-right: 0;
	        }
	    }
    }

**This is acceptable**

	&__tags {
	     li {
	        margin-bottom: 10px;
	        display: inline-block;
	        +above(m) {
	            margin-right: 6px;
	        }
	    }
    }

### Don't neglect print stylesheets

There is a global print stylesheet file in src/globals/css which should handle most of the possible things that will happen in printing but it does make sense to keep print styles as component-based as the rest. In order to create a print stylesheet for your component, simply create a filed called style.print.styl and add your styles in there, they will be automatically compiled into the main print stylesheet.

Remember. Don't use regular values for print stylesheets, use millimetres. Also print stylesheets should not use jeet or rupture.

### Keep IE fixes to a minimum

IE stylesheets should be created inside each component and be named style.ie.styl. You should also replace any references to the _toolkit file with _ietoolkit as this one is tailored for IE.

Do not duplicate styles. The IE stylesheet should ONLY be used for overrides.

Please note: The use of !important is allowed here but please use it sparingly

### Always adopt the DRY (Don't Repeat Yourself) standard

If you are using code which is similar to code used in another function, turn that code into an object, utility or mixin and use/extend it. Don't create a new instance of something which is (practically) identical.

e.g.

**This is not acceptable**

HTML

	<a class="o-tag-btn"></a>

CSS

	.o-tag-btn {
		background-color: $light;
		border: 1px solid $border;
		color: $gray;
		cursor: pointer;
		outline: none;
		font-size: $innercore;
		text-transform: none;
		display: inline-block;
		&:hover, &:active, &:focus {
			text-decoration: none;
			color: currentColor;
		}
	}

	.o-btn {
		background-color: $dark;
		border: 1px solid $border;
		color: $gray;
		cursor: pointer;
		outline: none;
		font-size: $innercore;
		text-transform: none;
		display: inline-block;
		&:hover, &:active, &:focus {
			text-decoration: none;
			color: currentColor;
		}
	}

**This is acceptable**

HTML

	<a href="o-btn o-tag-btn"></a>

CSS

	.o-tag-btn {
		background-color: $light;
	}

	.o-btn {
		background-color: $dark;
		border: 1px solid $border;
		color: $gray;
		cursor: pointer;
		outline: none;
		font-size: $innercore;
		text-transform: none;
		display: inline-block;
		&:hover, &:active, &:focus {
			text-decoration: none;
			color: currentColor;
		}
	}

**This is also acceptable**

HTML

	<a href="o-tag-btn"></a>

CSS

	.o-tag-btn {
		@extend .o-btn;
		background-color: $light;
	}

	.o-btn {
		background-color: $dark;
		border: 1px solid $border;
		color: $gray;
		cursor: pointer;
		outline: none;
		font-size: $innercore;
		text-transform: none;
		display: inline-block;
		&:hover, &:active, &:focus {
			text-decoration: none;
			color: currentColor;
		}
	}

If this sort of thing was common, it may be appropriate to create a mixin instead.

### Don't add browser prefixing

The build tool will have auto prefixing enabled via the 'autoprefixer' plugin for Grunt, provided that the software is kept up-to-date then this will ensure that the correct prefixing is always being used for the right browsers. Because of this, don't add prefixing to a project, let the tool do it for you.

**This is not acceptable**

CSS

	-o-transition: background-color .6s ease-out;
	-ms-transition: background-color .6s ease-out;
	-moz-transition: background-color .6s ease-out;
	-webkit-transition: background-color .6s ease-out;
	transition: background-color .6s ease-out;

**This is acceptable**

CSS

	transition: background-color .6s ease-out

### Ensure all links have a :focus property and that it is appropriate
Screen readers and people who have to use a keyboard, pad to navigate a site can't rely on hover states. However they still need visual feedback.

As this closely follows GDS there is a focus property on every link but ensure that it works visually for the link you have created.

### Stick to variables where possible

Occasionally, using a custom value is necessary but by default you should use the pre-existing variables to specify things like breakpoints, font-sizes, margin/padding etc...

**This is not acceptable**

CSS

	+above(1024px) {
		 .c-main-nav {
		     margin: 10px;
		 }
	 }

**This is acceptable**

CSS

	+above(l) {
		 .c-main-nav {
		     margin: $spacing;
		 }
 	}

### Abstract often-used variables

If a variable is used all over the place and could conceivable be changed in the future, make sure the variable name doesn't have any non-transferrable meaning to it. Creating an abstract (but memorable) variable name is the better option.

e.g.

**These are not acceptable**

    $16px = 1.6rem
    $large = 1.6rem
    $header2 = 1.6rem

**This is acceptable**

	$outerCore = 1.9rem

This way, if another level needs to be added or if the font-size changes in the future. We don't have a ton of variables which need to be found and replaced as they no longer make sense.

### Don't use PX values
Unless something *has* to be a fixed width then don't use Pixel values. Use either em/rem units or percentages (if you are laying something out to the grid then use jeet, see below). Always take the above point into account though, if it's possible to use a variable then use that instead.

**This is not acceptable**

CSS

	.box {
		width: 400px;
		margin: 20px;
	}

**This is acceptable**

CSS

	.box {
		width: 20%;
		margin: $spacing*2
	}

### Consider the grid

Whilst we are on the subject of widths, wherever possible, widths should be either flexible or set to the grid. As we are using Jeet, you should set the width using that wherever possible.

**This is not acceptable**

CSS

	.box-aligned-to-grid {
		width: 400px;
		margin: 20px;
	}

**This is acceptable**

CSS

	.box-aligned-to-grid {
		column(1/4);
	}

### Be aware of existing resets

Don't automatically add `box-sizing:border-box` or `margin:0` to elements. The reset stylesheet will cover most of these. In fact it is always a good idea to refactor any css class you work on to ensure all properties are actually needed.

**This is not acceptable**

CSS

	.list {
		box-sizing: border-box;
		margin: 0;
		padding: 0;
		list-style-type: none;
		color: $primary;
	}

**This is acceptable**

CSS

	.list {
		color: $primary;
	}

### Avoid the use of the !important flag

Contrary to popular believe, the `!important` flag is not something which should never be used, however there are only three situations where it can reasonable used in:

**1: Active development**
Sometimes you want to test something and adding `!important` to a property will make it easier, provided you remove it as soon as your testing is done, that's fine.

**2: Global classes**
Some global classes should never be overwritten by other styles, if this is the case then using `!important` can ensure this. This is actually what it was created for.

**3: Media Queries**
Sometimes, a media query needs to overwrite something that already exists, rather than add undue nesting or referencing other components in order to achieve the override then the use of `!important` is acceptable. However if the component is structured correctly, this shouldn't be required.

**_Note:_** Even in the above examples. Only use `!important` if you absolutely have to, the flag should only be added when you've exhausted all other options.

### Don't mix namespaces

As we've namespaced all our components (see the first standard) we can be assured that there will be no styling overlaps or conflicts. Each component should only have access to two scopes: Itself and the global namespace. Don't put any styles from other components within the current one and don't put any component related styles in the global namespace. If overrides are required then keep them to the 'Parent overrides' section and use them only when required. (Also ensure they are documented).

Note: There is one exception to this rule and that is layouts if a component CONTAINS a layout (e.g. 'c-footer') then you can reference that component in the layouts.styl file. If a component should change depending on it's parent layout then that is a parent override and belongs in the component stylesheet.

### Any global styles should be added to one of the global stylesheets

This one is a basic rule, don't add global styles to any stylesheet which isn't a global stylesheet.

### Don't use ID's for Styling

ID's may be in the HTML, but these ID's should never be used to attach styles to.

**This is not acceptable**

HTML

	<div id="container"></div>

CSS

	#container {
		color: $primary;
	}

**This is acceptable**

HTML

	<div id="container" class="c-container"></div>

CSS

	.c-container {
		color: $primary;
	}

### Don't use 'js-' prefixed classes for styling

Javascript classes (prefixed with `js-`) should be kept separate from styling classes. Never apply styling to an element using the JS class. This ensures that behaviour and appearance are kept separate from one-another, so if the javascript or style class has to change for any reason, the other class is unaffected.

The naming convention should follow the same process as naming components. So instead of 'c-header', use 'js-header'.

The reverse of this rule is also true, don't use styling classes for JS hooks.

**This is not acceptable**

HTML

	<div class="js-header"></div>

CSS

	.js-header {
		font-weight:bold;
	}

**This is acceptable**

HTML

	<div class="js-header c-header"></div>

CSS

	.c-header {
		font-weight:bold;
	}

### Avoid universal selectors

Universal selectors (`*` is a good example of this) are not great for performance as they have to match multiple elements before they can finish their task. In reset stylesheets, this is an acceptable overhead. However they shouldn't be used as a matter of course.

### Don't worry about descendant selectors

There is a oft-quoted idea that using descendant and child (`>`) selectors has a performance impact. Whilst this is true, the impact is negligable.

So if you want to write `.list > li a` instead of `.list-item-link{}` then go ahead. Just make sure you follow the next rule when you do it.

### Don't be overly specific

In the example above, I targeted a link in a list like this `.list > li a`, whilst there may be a few valid reasons for doing this (for example you only want to target the links in the top level of a nested list), it's often overkill to specify that much. `.list a` will work just find for cases where you don't need the extra specificity.

### Don't qualify classes

This one ties to the one above. Don't write css like this:

	div.container {
		padding: $padding
	}

Whilst this will work fine if the HTML looks like this:

	<div class="container"></div>

Specifying things at a tag level will add to performance, also what happens to the styling if the HTML changes to this:

	<article class="container"></article>

Suddenly that CSS which specified that only `div` tags can have a class of 'container' fails as there was nothing to accommodate an `article` tag with a container class on it.

### Consider future-proofing when using location specific selectors

Location specific selectors (e.g. `:nth-child(n)`, `:last-child` etc...) are often useful but extra care must be taken with these to ensure that the future of the document is being taken into consideration. If the HTML structure changes at all, how will that impact your styling?

### Don't apply units to zero value properties.

The performance impact here is minor but as there is no valid reason to specify a unit then it's still good practice to not do so.

**This is unacceptable**

CSS

	margin: 0px;

**This is acceptable**

CSS

	margin: 0;

## HTML/PHP

### Write the HTML from a content first approach
Ideally the design will be created from a content-first approach anyway but even if it isn't there is a still a benefit to building the HTML this way. In fact it's a good idea to write out the HTML before even considering the styles. That way the content is informing the structure and not the appearance.

### Components should use self-contained elements
A component is supposed to be reusable and could (in theory) exist anywhere on the site (or even other sites!) so it's important that they are always written in a self-contained way. This means that the tag you use to create them must be a `section` tag. (You can also use `header`, `footer`, nav, `aside` and `article` but please ensure that this usage is appropriate for that component first).

**Note:** Some components need to be within another component as a parent, these child-components can use any tag that is appropriate but please put in the component.json which parent they belong to.

### Components should reset the document flow

Again, as you have no idea where the component will be used on the page, it's important that you reset the document flow (which is one of the reasons you must use one of the tags listed above).

**This is not acceptable**

<section class="c-product-item">
	<h3>Header</h3>
</section>

**This is acceptable**

<section class="c-product-item">
	<h1>Header</h1>
</section>

### Name classes in accordance to content, not appearance
Similar to the rule above, write your class names based on what is going in the container and not based on how it looks:

**This is not acceptable**

HTML

	<section class="c-product-item">
		<h1 class="border-bottom blue bold">Header</h1>
	</section>

**This is acceptable**

HTML

	<section class="c-product-item">
		<h1 class="c-product-item__title">Header</h1>
	</section>

CSS

	.c-product-item {
		 &__title {
		 	border-bottom: 1px solid $blue;
		 	color: $blue;
		 	font-weight: bold;
	 	}
	}

### Don't overly nest elements

Wrapping elements in a `div` tag isn't usually needed and should only be added where items need to be grouped together. A simple rule of thumb is that if a `div` tag has been added to a page and doesn't need a class, question if you even need the `div` in the first place.

**This is not acceptable**

	<div class="c-product-item">
		<div>
			<h1> Header </h1>
		</div>
	</div>

**This is acceptable**

	<div class="c-product-item">
		<h1> Header </h1>
	</div>

**This is also acceptable**

	<div class="c-product-item">
		<div class="c-grouped-items">
			<h1> Header </h1>
			<span> Sub-header </span>
		</div>
	</div>

### Don't use images for ui elements if it can be done in CSS

**This is not acceptable**

HTML

	<a href="">Read more <img src="/images/arrow-right.svg"></a>

**This is acceptable**

HTML

	<a class="read-more-link" href="">Read more</a>

CSS

	.read-more-link {
		&:after {
			content: '';
			width: 1rem;
			height: 2rem;
			margin-left: .5rem;
			background-image: url(/images/arrow-right.svg);
		}
	}

In fact if possible, don't use an image at all. If the item can be an icon font or even a text character. Use that instead (keep browser compatibility in mind though).

Again a good rule of thumb here is that if the page is still 100% understandable without the image then it's probably a UI element and should be added via CSS.

### Honour the HTML5 header structure

The `article` and `section` tags all contain their own internal document flow. This allows these tags to be transplanted to other places on the site (or even other sites) and still maintain the correct document order. In order to utilise this correctly always restart the header levels when using these tags. e.g. Start with a `h1` instead of a `h2`.

**This is not acceptable**

	<h1> Page title</h1>
	<section class="c-product-item">
		<h2>Product Item Title</h2>
	</component>

**This is acceptable**

	<h1> Page title</h1>
	<section class="c-product-item">
		<h1>Product Item Title</h1>
	</section>

### It's probably a component
Almost everything on the site is a component, the exceptions to this are utilities and objects and non-transferrable elements within a component.

The general rules are thus:

If it could be reused elsewhere and it has specific content: It's a component.

If it can be reused elsewhere and the content is not thematically related (eg a button): it's an object.

If it can be reused elsewhere and isn't an element in it's own right but will modify other elements: it's a utility.

e.g.

Featured news list component

	<div class="c-featured-news-list">
	  <?php get_component('c-article-item', 'featured_news'); ?>
	  <?php get_component('c-article-item', 'featured_news'); ?>
	</div>

Article item component

	<article class="c-article-item">
	  <img src="https://placeimg.com/650/433/tech" alt="description of image goes here">
	  <h1><a href="#">This is an example title</a></h1>
	  <?php
	  // If the 'featured_news' value has been passed to $params: Display the excerpt.
	  if ($params === 'featured_news') { ?>
	    <div class="c-article-item__excerpt">
	      <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Vero rem cumque aliquam, voluptatibus recusandae velit eos maiores molestiae illum eveniet deleniti magni delectus neque, distinctio sit error laudantium quos porro.</p>
	    </div>
	  <?php } ?>
	  <span class="c-article-item__dateline">11 Jan 2017</span>

	  <?php
	  // If the 'blog' value has been passed to $params: Display the byline.
	  if ($params === 'blog') { ?>
	    <span class="c-article-item__byline">Simon Quinn</span>
	  <?php } ?>
	</article>

As you can see the component above, The items inside the c-article-item component are all unique to that component and so use BEM notation to denote they are a part of it.

All components can contain an unlimited number of other components, just ensure that before you turn it into a component that it is likely to be reusable and also determine if an object would be better.

### Don't use tables for anything other than tabular data

Hopefully this doesn't need to be said anymore but just in case, tables should only be used for displaying table-based data and never for layouts.

**This is not acceptable**

	<table>
		<tr>
			<td>
				<h1>Header</h1>
				<p>This is some text</p>
				<p>This is some more text</p>
				<ul>
					<li>This is a list item</li>
					<li>This is too</li>
				</ul>
			</td>
			<td>
				<h2>Sidebar</p>
				<ul>
					<li>Item 1</li>
					<li>Item 2</li>
					<li>Item 3</li>
				</ul>
			</td>
		</tr>
	</table>

**This is acceptable**

	<div class="col primary">
		<h1>Header</h1>
		<p>This is some text</p>
		<p>This is some more text</p>
		<ul>
			<li>This is a list item</li>
			<li>This is too</li>
		</ul>
	</div>
	<div class="col sidebar">
		<h2>Sidebar</p>
		<ul>
			<li>Item 1</li>
			<li>Item 2</li>
			<li>Item 3</li>
		</ul>
	</div>

**This is an acceptable use of tables**

	<table>
		<tbody>
			<tr>
				<th>Year</th>
				<th>Movie name</th>
				<th>Main Actor</th>
			</tr>
			<tr>
				<td>2013</td>
				<td>The Wolf of Wall Street</td>
				<td>Leonardo DiCaprio</td>
			</tr>
			<tr>
				<td>1994</td>
				<td>The Shawshank Redemption</td>
				<td>Tim Robbins</td>
			</tr>
		</tbody>
	</table>

### Always consider accessibility

#### Use semantic tags or ARIA roles

Screen-readers and other assistive devices use page semantics to make sense of a page, so using tags like `nav`, `header`, `footer` etc... help. Sometimes though it's not possible to use a semantic tag. In which case a suitable ARIA role should be added:

e.g.

	<form action="" class="search" aria-role="search">
		<input type="text" value="Search">
		<button type="submit">Search</button>
	</form>

#### Always use a descriptive ALT attribute on an image.

An ALT attribute (also known as an ALT Tag) is a description of an image, it is there to provide context for screen readers and for users who can't/don't see images. It is not a marketing tool or an SEO tool.

If you are not sure what to write in an ALT tag, imagine how you would briefly describe the image to someone who couldn't see it.

**This is not acceptable**

	<a href=""><img src="/images/office-building.jpg" alt="Click to view a bigger version of this image"></a>

**This is acceptable**

	<a href=""><img src="/images/office-building.jpg" alt="An external view of our head office."></a>

#### Structure a document in order of reading

In CSS content positioning can be altered, however without CSS content follows a normal document flow. Always bear this in mind when structuring your HTML. The page should make complete sense without CSS.

**This is not acceptable**

	<div class="right">
		<p>
		   Pursued by the Empire's sinister agents, Princess
	       Leia races home aboard her starship, custodian of
	       the stolen plans that can save her people and
	       restore freedom to the galaxy...
	     </p>
	</div>
	<div class="left">
		<p>
			It is a period of civil war. Rebel spaceships,
			striking from a hidden base, have won their first
			victory against the evil Galactic Empire.
		</p>
		<p>
			During the battle, Rebel spies managed to steal
			secret plans to the Empire's ultimate weapon, the
			Death Star, an armored space station with enough
			power to destroy an entire planet.
		</p>
	</div>

**This is acceptable**

	<div class="l-left">
		<p>
			It is a period of civil war. Rebel spaceships,
			striking from a hidden base, have won their first
			victory against the evil Galactic Empire.
		</p>
		<p>
			During the battle, Rebel spies managed to steal
			secret plans to the Empire's ultimate weapon, the
			Death Star, an armored space station with enough
			power to destroy an entire planet.
		</p>
	</div>
	<div class="l-right">
		<p>
		   Pursued by the Empire's sinister agents, Princess
	       Leia races home aboard her starship, custodian of
	       the stolen plans that can save her people and
	       restore freedom to the galaxy...
	     </p>
	</div>

## Javascript
### Event based methods must be bound to the component

When calling a method, it must be bound to the component itself. This ensures that closures are preserved and avoids scope issues. It also means that functions are more likely to be resuable.

e.g.

**This is not acceptable**

	toggleMenu: function() {
	     var menu = $('.js-menu-toggle');
	     if(menu.length) {
	         menu.on('click', function() {
	             //do something
	         })
	     }
	 }

	 toggleMenu();

**This is acceptable**

	this.$el = $('.js-menu'),
	this.$el.on('click', '.js-menu__toggle',  ['params', 'go', 'here'], this.toggleMenu.bind(this)); // toggleMenu()

	this.toggleMenu: function() {
        // do something
    }

Using `bind(this)` isn't always required but it allows you to pass the 'this' context into the method and thereby provides access to the parent scope.

### Each function/method should only have one job.

A function should be written to do one thing and one thing only. This improves modularity and readability.

### Always use strongly typed comparators
When writing `if` statements, avoid using `==` if using `===` is possible, ensuring that code is strongly typed helps to maintain code parity, also no type conversion is carried out when using `===` which helps to increase performance.

See this article for more information: [Comparison Operators tutorial](http://www.c-point.com/javascript_tutorial/jsgrpComparison.htm)

### If listing errors or warnings appear during build. Make sure you correct them.

The build tool uses StandardJS to ensure that code quality is maintained. If an error or a warning appears on the build then ensure you correct them. If time is a factor then warnings can be temporarily ignored but ensure you add them to your technical debt.

### Comment everything as comprehensively as possible
In large projects such as this one, it's vital that everyone understands what the code is doing. Therefore if you write a piece of JavaScript then make sure you document it thoroughly.

**This is not acceptable**

	// Menu toggle
	toggleMenu: function(el, speed) {
	   [...]
	}

**This is acceptable**

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

### Keep local variables in one place

When instantiating a local function, ensure that you keep all the local variables at the top of the function. This will improve readability.

** This is not acceptable **

	var function = bigDamnHeroes() {
		var mal = 'in the nick of time';
		if(mal === 'in the nick of time') {
			var river = 'our witch';
			var zoe = 'big damn heroes, sir';
			console.log(zoe);
		}
	}

**This is acceptable**

	var function = bigDamnHeroes() {
		var mal = 'in the nick of time',
			zoe = 'big damn heroes, sir',
			river = 'burning at the stake';

		if(mal === 'in the nick of time') {
			river = 'our witch';
			console.info(zoe);
		}
	}

### Use ES6 where possible.

Babel is installed and ES6 has a lot of benefits over ES5. I won't give examples of these as the docs on ES6 are thorough and clear. A great resource for this is http://es6-features.org/

Note: Some things cannot be used in an ES6 manner, module loading (import/export) is a prime example of this as it cannot be transpiled in a way that will work in older versions of IE.

### Try to write encapsulated code. Use jQuery plugin architecture where possible.

The example is too long to paste in here but take a look at clarity-toolbar.js and script.js in /src/components/c-clarity-toolbar to see how to do this.

### If it can be global, make it global

When writing javscript tools. Think if it is something that could be re-used in another component? If so then make it into a plugin and put it in the /src/globals/js folder.

