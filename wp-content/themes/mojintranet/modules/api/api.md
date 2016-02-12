The API
==========

The MoJ Intranet RESTful API

##Endpoints
These can be accessed using the form:

	/service/{endpoint}/{param_1}/.../{param_n}/

###Children:
  * **[GET]** /children/{page-id}/ - retrieve the immediate children of the given page

###News:
  * **[GET]** /news/{type}/{category}/{plus+seperated+keywords}/{initial}/{page}/{per-page} - retrieve news items

###Events:
  * **[GET]** /events/{date}/{keywords}/{page}/{per-page} - retrieve events

###Post:
  * **[GET]** /post/{type}/{category}/{plus+seperated+keywords}/{initial}/{page}/{per-page} - retrieve blog posts

###Search:
  * **[GET]** /search/{type}/{category}/{plus+seperated+keywords}/{page}/{per-page} - searches all site content

###Months:
  * **[GET]** /months - produces list of months (by default 12) with event count

###Likes:
  * **[GET]** /likes/{post_id} - get the number of likes for that post

  * **[PUT]** /likes/{post_id} - increment like counter for that post
