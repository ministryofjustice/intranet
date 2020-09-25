const mix = require('laravel-mix')
const stylDeps = [require('jeet')(), require('rupture')()]
const ImageminPlugin = require('imagemin-webpack-plugin').default
const CopyPlugin = require('copy-webpack-plugin')

mix.setPublicPath('./dist/')

mix.webpackConfig({
    plugins: [
        new CopyPlugin({
            patterns: [
                { from: '**.*', to: 'images', context: 'src/globals/images' },
                { from: '*/**.*', to: 'images', context: 'src/globals/images' }
            ]
        }),
        new ImageminPlugin({
            test: /\.(jpe?g|png|gif|svg)$/i
        })
    ]
})

/*******************/
mix.js('src/globals/js/script-loader.js', 'dist/js/main.min.js')
    .stylus('src/globals/css/_init.styl', 'dist/css/globals.css', { use: stylDeps })
    .stylus('src/components/style.print.styl', 'dist/css/', { use: stylDeps })
    .stylus('src/components/style.ie.styl', 'dist/css/', { use: stylDeps })
    .stylus('src/components/style.ie8.styl', 'dist/css/', { use: stylDeps })
    .stylus('src/components/style.styl', 'dist/css/', { use: stylDeps })
    .copy('src/vendors/*', 'dist/js/')
    .copy('./node_modules/jquery/dist/jquery.min.js', 'dist/js/')
    .copy('src/globals/fonts/*', 'dist/fonts/')
    .options({ processCssUrls: false })

if (mix.inProduction()) {
    mix.version()
} else {
    mix.sourceMaps()
}
