# Instructions when managing/updating MOJ icons
Steps needed in order to modify, update or change the MoJ icons in anyway:

1.) Upload `selection.json` in this folder to https://icomoon.io
2.) Modify your fonts accordingly on icomoon.io . The names given to the icons here, need to match the CSS name where the icon is displayed on the site.
3.) Make sure all icons are highlighted and download load them from icomoon.io by clicking on download fonts.
4.) You will now have a downloaded zip containing the following:
* moji-clarity.eot
* moji-clarity.svg
* moji-clarity.ttf
* moji-clarity.woff
* selection.json
* style.css

You can disregard the other files, the above are all that is needed.

5.) Copy all files above, except `style.css`, to ~ /src/globals/fonts (this folder) and /intranet-theme-clarity/assets/fonts overwriting the existing files in those locations.
6.) Now open `style.css` and copy everything in it, and paste it into ~ /src/globals/css/icons.styl overwriting everything in that file.
7.) Now in `icons.styl` you will need to change the @font-face urls to point to the right directory. This can be done by adding `../` to the file URL. So for example `../fonts/moji-clarity.eot?vklv4a`.
8.) Make sure to run gulp. Everything should be updated. If the name of the icon in `icon.styl` is different then on the page and the icon is not showing, you may need to change it in `mymoj.php` or `agency.php`.
