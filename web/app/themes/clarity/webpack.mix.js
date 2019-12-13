const mix = require('laravel-mix'),
  stylDeps = [require('jeet')(), require('rupture')()]

require('laravel-mix-imagemin')

mix.setPublicPath('./dist/')

/*******************/

mix.babel([
      'src/globals/js/*.js',
      'src/components/**/*.js'
    ],
    'dist/js/main.js'
  )
  .stylus('src/globals/css/_init.styl','dist/css/globals.css', { use: stylDeps })
  .stylus('src/components/style.print.styl', 'dist/css/', { use: stylDeps })
  .stylus('src/components/style.ie.styl', 'dist/css/', { use: stylDeps })
  .stylus('src/components/style.ie8.styl', 'dist/css/', { use: stylDeps })
  .stylus('src/components/style.styl', 'dist/css/', { use: stylDeps })
  .imagemin([
      'images/**.*',
      'images/*/**.*',
    ],
    { context: 'src/globals' },
    {
      optipng: { optimizationLevel: 5 },
      jpegtran: null,
      plugins: [
        require('imagemin-mozjpeg')({
          quality: 100,
          progressive: true,
        }),
      ],
    }
  )
  .copy('src/vendors/*', 'dist/js/')
  .copy('./node_modules/jquery/dist/jquery.min.js', 'dist/js/')
  .copy('src/globals/fonts/*', 'dist/fonts/')
  .options({ processCssUrls: false })

if (mix.inProduction()) {
  mix.version()
} else {
  mix.sourceMaps()
}
