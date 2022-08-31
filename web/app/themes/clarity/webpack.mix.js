let mix = require('laravel-mix')

let stylDeps = {
    stylusOptions: {
        use: [require('jeet')(), require('rupture')()]
    }
};

mix.setPublicPath('./dist/')

/*******************/
mix.js('src/globals/js/script-loader.js', 'dist/js/main.min.js')
    .stylus('src/globals/css/_init.styl', 'dist/css/globals.css', stylDeps)
    .stylus('src/components/style.print.styl', 'dist/css/', stylDeps)
    .stylus('src/components/style.ie.styl', 'dist/css/', stylDeps)
    .stylus('src/components/style.ie8.styl', 'dist/css/', stylDeps)
    .stylus('src/components/style.styl', 'dist/css/', stylDeps)
    .copy('src/vendors/*', 'dist/js/')
    .copy('./node_modules/jquery/dist/jquery.min.js', 'dist/js/')
    .copy('src/globals/fonts/*', 'dist/fonts/')
    .copy('src/globals/images', 'dist/images/')
    .options({ processCssUrls: false })

if (mix.inProduction()) {
    mix.version()
} else {
    mix.sourceMaps()
}
