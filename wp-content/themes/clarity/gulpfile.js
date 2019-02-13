/*
GULP assets (CSS, JS) compilier
Run `npm install`
If issues try run `sudo npm i --unsafe-perm` first
then run `gulp` cmd in gulpfile.js dir
*/

// vars

var gulp = require('gulp')
    stylus = require('gulp-stylus')
    uglify = require('gulp-uglify')
    rename = require('gulp-rename')
    notifier = require('node-notifier')
    standard = require('gulp-standard')
    cmq = require('gulp-merge-media-queries')
    csso = require('gulp-csso')
    cssnano = require('gulp-cssnano')
    jeet = require('jeet')
    del = require('del')
    rupture = require('rupture')
    concat = require('gulp-concat')
    plumber = require('gulp-plumber')
    autoprefixer = require('autoprefixer')

var supportedBrowsers = [
  'last 2 versions',
  'safari >= 8',
  'ie >= 7',
  'ff >= 20',
  'ios 6',
  'android 4'
]

var styleWatchFiles = 'src/**/*.styl'

var jsSRC = [
  'src/components/**/*.js',
  'src/globals/js/*.js'
]

var jsVendorSRC = [
  'node_modules/jquery/dist/jquery.min.js',
  'src/vendors/**/*'
]

var cssSRC = [
  'src/globals/css/*.styl',
  '!src/**/*.print.styl',
  '!src/**/*.ie.styl',
  '!src/**/*.ie8.styl',
  'src/components/**/*.styl'
]

var printSRC = 'src/**/*.print.styl'
var ieSRC = 'src/**/*.ie8.styl'
var cssASSETS = 'assets/css/*.css'
var fontSRC = 'src/globals/fonts/**/*'
var iconSRC = 'src/globals/images/icons/*'

var imgSRC = [
  'src/globals/images/*.png', 
  'src/globals/images/*.jpg', 
  'src/globals/images/*.gif'
]

// build

function js() {
  return gulp.src(jsSRC)
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
  .pipe(gulp.dest('assets/js'))
}

function css() {
  return gulp.src(cssSRC)
  .pipe(plumber())
  .pipe(stylus({
    'include css': true,
    use: [jeet(), rupture()]
  }))
  .pipe(concat('core.css'))
  .pipe(gulp.dest('assets/css'))
}

function print() {
  return gulp.src(printSRC)
  .pipe(plumber())
  .pipe(stylus({
    'include css': true
  }))
  .pipe(concat('print.css'))
  .pipe(gulp.dest('assets/css'))
}

function ie() {
  return gulp.src(ieSRC)
  .pipe(plumber())
  .pipe(stylus({
    'include css': true,
    use: [jeet(), rupture()]
  }))
  .pipe(concat('ie8.css'))
  .pipe(gulp.dest('assets/css'))
}

// format final CSS file to spec
function formatCSS() {
  return gulp.src(cssASSETS)
  .pipe(plumber())
  .pipe(cmq())
  .pipe(csso())
  .pipe(cssnano({
    autoprefixer: { browsers: supportedBrowsers, add: true }
  }))
  .pipe(rename({ suffix: '.min' }))
  .pipe(gulp.dest('assets/css'))
}

function clean() {
  return del(['assets/css']);
}

// move files to required locations

// function files() {
//   return gulp.src(clarityFiles)
//   .pipe(gulp.dest('../../../docker/bedrock_volume/web/app/themes/intranet-theme-clarity'))
//   .pipe(notifier.notify({ title: 'Moved PHP :|', message: 'moved files' }))
// }

function jsVendor() {
  return gulp.src(jsVendorSRC)
  .pipe(plumber())
  .pipe(gulp.dest('assets/vendors'))
}

function fonts() {
  return gulp.src(fontSRC)
  .pipe(plumber())
  .pipe(gulp.dest('assets/fonts'))
}

function icons() {
  return gulp.src(iconSRC)
  .pipe(plumber())
  .pipe(gulp.dest('assets/icons'))
}

function images() {
  return gulp.src(imgSRC)
  .pipe(plumber())
  .pipe(gulp.dest('assets/images'))
}

function watch() {
  // watching
  gulp.watch(styleWatchFiles, gulp.series([clean, css, ie, print, formatCSS])) // watch and process files in order
  gulp.watch(jsSRC, js) // (source,function)

  // moving
  gulp.watch(jsVendorSRC, jsVendor)
  gulp.watch(fontSRC, fonts)
  gulp.watch(iconSRC, icons)
  gulp.watch(imgSRC, images)

  notifier.notify({ title: 'Watching :|', message: '...for the file changes' })
}

exports.js = js
exports.css = css
exports.print = print
exports.ie = ie
exports.clean = clean
exports.formatCSS = formatCSS
//exports.files = files
exports.jsVendor = jsVendor
exports.fonts = fonts
exports.icons = icons
exports.images = images

var build = gulp.parallel(watch)
gulp.task('default', build)