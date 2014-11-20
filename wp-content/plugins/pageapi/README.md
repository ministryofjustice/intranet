wp-pageapi
==========

WordPress plugin that lets you query the page structure through a RESTful API

##Endpoints
These can be accessed using the form 

	/{plugin-root}/{endpoint}/{page-id}/

The default root is _service_ but this can be changed in the code by altering the PAGEAPI_ROOT constant.

* children - retrieve the immediate children of the given page

##Direct use
You can use the plugin within by PHP as follows:

* $children = new children_request({page-id})

##Release history

0.1 - initial release with _children_ endpoint