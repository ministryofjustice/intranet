/*
GULP 4.0 asset (CSS, JS) compilier
https://gulpjs.com/

Instructions:
Run `npm install`

If everything installs correctly, you will have several Gulp commands available,

`gulp` = runs default task of watching files for changes and then compiling
`gulp watch` = same as default task above, watches files
`gulp build` = compiles the assest on command then stops
`gulp resync` = sync WP theme folders with the Docker environment

If issues installing try run `sudo npm i --unsafe-perm`
The --unsafe-perm flag ignores some issues caused by running in root (locally)
*/

// constants

const { src, dest, task, parallel, series, watch } = require('gulp')
const { exec } = require('child_process')

const stylus = require('gulp-stylus')
      uglify = require('gulp-uglify')
      rename = require('gulp-rename')
      notifier = require('node-notifier')
      standard = require('gulp-standard')
      csso = require('gulp-csso')
      cssnano = require('gulp-cssnano')
      jeet = require('jeet')
      del = require('del')
      rupture = require('rupture')
      concat = require('gulp-concat')
      plumber = require('gulp-plumber')
      autoprefixer = require('autoprefixer')

const supportedBrowsers = [
      'last 2 versions',
      'safari >= 8',
      'ie >= 7',
      'ff >= 20',
      'ios 6',
      'android 4'
]

/* Source Gulp glob vars
* Be careful on source order, make sure to follow
* https://gulpjs.com/docs/en/getting-started/explaining-globs
*/

const styleWatchFiles = [
  'src/**/*.styl'
]

const jsSRC = [
      'src/components/**/*.js',
      'src/globals/js/*.js'
]

const jsVendorSRC = [
      'node_modules/jquery/dist/jquery.min.js',
      'src/vendors/**/*'
]

const cssSRC = [
      'src/globals/css/*.styl',
      'src/**/*.styl',
      '!src/**/*.print.styl',
      '!src/**/*.ie.styl',
      '!src/**/*.ie8.styl'
]

const printSRC = 'src/**/*.print.styl'
      ieSRC = 'src/**/*.ie8.styl'
      cssASSETS = 'assets/css/*.css'
      fontSRC = 'src/globals/fonts/**/*'
      iconSRC = 'src/globals/images/icons/*'

const imgSRC = [
      'src/globals/images/*.png', 
      'src/globals/images/*.jpg', 
      'src/globals/images/*.gif'
]

// tasks

function js() {
  return src(jsSRC)
  .pipe(plumber())
  .pipe(standard())
  .pipe(standard.reporter('default', {
    breakOnError: true,
    quiet: true
  }))
  .pipe(concat('core.js'))
  .pipe(rename({ suffix: '.min' }))
  .pipe(uglify({
    ie8: true,
    mangle: { reserved: ['$', 'jQuery'] }
  }))
  .pipe(dest('assets/js'))
}

function css() {
  return src(cssSRC)
  .pipe(plumber())
  .pipe(stylus({
    'include css': true,
    use: [jeet(), rupture()]
  }))
  .pipe(concat('core.css'))
  .pipe(dest('assets/css'))
}

function print() {
  return src(printSRC)
  .pipe(plumber())
  .pipe(stylus({
    'include css': true
  }))
  .pipe(concat('print.css'))
  .pipe(dest('assets/css'))
}

function ie() {
  return src(ieSRC)
  .pipe(plumber())
  .pipe(stylus({
    'include css': true,
    use: [jeet(), rupture()]
  }))
  .pipe(concat('ie8.css'))
  .pipe(dest('assets/css'))
}

// format final CSS file to spec

function formatCSS() {
  return src(cssASSETS)
  .pipe(plumber())
  .pipe(csso())
  .pipe(cssnano({
    autoprefixer: { browsers: supportedBrowsers, add: true }
  }))
  .pipe(rename({ suffix: '.min' }))
  .pipe(dest('assets/css'))
}

function clean() {
  return del(['assets/css/*']);
}

function jsVendor() {
  return src(jsVendorSRC)
  .pipe(plumber())
  .pipe(dest('assets/vendors'))
}

function fonts() {
  return src(fontSRC)
  .pipe(plumber())
  .pipe(dest('assets/fonts'))
}

function icons() {
  return src(iconSRC)
  .pipe(plumber())
  .pipe(dest('assets/icons'))
}

function images() {
  return src(imgSRC)
  .pipe(plumber())
  .pipe(dest('assets/images'))
}

// Sync theme folders into directory for Docker volume to load
function clarityTheme() {

  var sourcePath = '.'
  var destinationPath = '../../../docker/bedrock_volume/web/app/themes/'
  return exec('rsync -a --delete ' + sourcePath + ' ' + destinationPath + 'intranet-theme-clarity')
}

function mojintranetTheme() {
  var sourcePath = '../mojintranet/*'
  var destinationPath = '../../../docker/bedrock_volume/web/app/themes/'
  return exec('rsync -a --delete ' + sourcePath + ' ' + destinationPath + 'mojintranet')
}

function watchFiles() {
  // watch and process files in order
  watch(styleWatchFiles, series([clean, css, ie, print, formatCSS, resync]))
  watch(jsSRC, js)

  // watch and then move files
  watch(jsVendorSRC, series([jsVendor, resync]))
  watch(fontSRC, series([fonts, resync]))
  watch(iconSRC, series([icons, resync]))
  watch(imgSRC, series([images, resync]))

  notifier.notify({ title: 'Gulp running', message: '(•_•) watching Clairty theme files' })
}

// consolidate two main functions (watching and building) into variables

let resync = series([clarityTheme, mojintranetTheme])
let watcher = parallel(watchFiles)
let build = series([clean, css, ie, print, formatCSS, js, jsVendor, fonts, icons, images])

// expose functions

exports.js = js
exports.css = css
exports.print = print
exports.ie = ie
exports.clean = clean
exports.formatCSS = formatCSS
exports.jsVendor = jsVendor
exports.fonts = fonts
exports.icons = icons
exports.images = images
exports.build = build
exports.clarityTheme = clarityTheme
exports.mojintranetTheme = mojintranetTheme

/* 
* allow the running of Gulp tasks via cmd
* run `gulp --tasks` to view task structure
*/

task('default', watcher)
task('watch', watcher)
task('build', build)
task('resync', resync)
