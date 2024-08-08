const mix = require('laravel-mix');
const pathJS = 'dist/js/';
const pathCSS = 'dist/css/';
const stylDeps = {
    stylusOptions: {
        use: [require('jeet')(), require('rupture')()]
    }
};

mix.setPublicPath('./dist/');

/*******************/
mix.js('src/globals/js/script-loader.js', pathJS + 'main.min.js')
    .js('inc/admin/js/prior-party-banner.js', pathJS)
    .js('inc/admin/js/force-title.js', pathJS)
    .js('inc/admin/js/acf.js', pathJS)
    .js('inc/admin/js/colour-contrast-checker.js', pathJS)
    .js('inc/admin/js/feedback.js', pathJS)
    .js('src/globals/js/login.js', pathJS + 'login.min.js')
    .stylus('src/globals/css/_init.styl', pathCSS + 'globals.css', stylDeps)
    .stylus('src/components/style.print.styl', pathCSS, stylDeps)
    .stylus('src/components/style.ie.styl', pathCSS, stylDeps)
    .stylus('src/components/style.ie8.styl', pathCSS, stylDeps)
    .stylus('src/components/style.styl', pathCSS, stylDeps)
    .sass('src/globals/sass/login.scss', pathCSS + 'login.min')
    .css('inc/admin/css/admin.css', pathCSS)
    .copy('src/vendors/*', pathJS)
    .copy('./node_modules/jquery/dist/jquery.min.js', pathJS)
    .copy('src/globals/fonts/*', 'dist/fonts/')
    .copy('src/globals/images', 'dist/images/')
    .options({ processCssUrls: false });

if (mix.inProduction()) {
    mix.version();
} else {
    mix.sourceMaps();
}
