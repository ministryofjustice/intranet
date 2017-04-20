/*
Gulpfile.js file for the tutorial:
Using Gulp, Stylus and Browser-Sync for your front end web development

Steps:

1. Install gulp globally:
npm install --global gulp

2. Type 'gulp' and start developing

*/

/* Needed gulp config */
var gulp = require('gulp')
var stylus = require('gulp-stylus')
var uglify = require('gulp-uglify')
var rename = require('gulp-rename')
var notifier = require('node-notifier')
var standard = require('gulp-standard')
var cmq = require('gulp-merge-media-queries')
var cssnano = require('gulp-cssnano')
var jeet = require('jeet')
var rupture = require('rupture')
var csso = require('gulp-csso')
var concat = require('gulp-concat')
var babel = require('gulp-babel')
var del = require('del')
var plumber = require('gulp-plumber')
var argv = require('yargs').argv
var exec = require('child_process').exec
var runSequence = require('run-sequence')
var browserSync = require('browser-sync')
var reload = browserSync.reload

// required for the autoprefixer function in the 'styles' task
var supportedBrowsers = [
  'last 2 versions',
  'safari >= 8',
  'ie >= 7',
  'ff >= 20',
  'ios 6',
  'android 4'
]

/* Scripts task */
gulp.task('scripts', function () {
  return gulp.src([
    /* Add your JS files here, they will be combined in this order */
    'src/globals/**/*.js',
    'src/components/**/*.js'
  ])
  // Plumber is there to catch errors in the pipes
  .pipe(plumber())
  // Lint the code using standardjs
  .pipe(standard())
  .pipe(standard.reporter('default', {
    breakOnError: true,
    quiet: true
  }))
  // combine all the files to a single file
  .pipe(concat('core.js'))
  // Add .min to the end
  .pipe(rename({ suffix: '.min' }))
  .pipe(babel({
    presets: ['es2015']
  }))
  // Compress the file
  .pipe(uglify())
  // Move the file to the assets folder
  .pipe(gulp.dest('assets/js'))
  // Reload the browser JS after every change
  .pipe(reload({ stream: true }))
})

/* styles task */
gulp.task('stylus', function () {
  return gulp.src([
    // add your Stylus files here, they will be combined in this order (exclude print and ie styles)
    'src/globals/css/*.styl',
    '!src/**/*.print.styl',
    '!src/**/*.ie.styl',
    'src/components/**/*.styl'
  ])
    // Plumber is there to catch errors in the pipes
    .pipe(plumber())
    // Process the css using stylus
    .pipe(stylus({
      'include css': true,
      use: [jeet(), rupture()]
    }))
    // combine all the files to a single file
    .pipe(concat('core.css'))
    // move the file to the assets folder
    .pipe(gulp.dest('assets/css'))
    // Reload the browser CSS after every change
    .pipe(reload({ stream: true }))
})

gulp.task('print', function () {
 // Process print styles as seperate stylesheets
  return gulp.src('src/**/*.print.styl')
    .pipe(plumber())
    .pipe(stylus({
      'include css': true
    }))
    .pipe(concat('print.css'))
    // move the file to the assets folder
    .pipe(gulp.dest('assets/css'))
    // Reload the browser CSS after every change
    .pipe(reload({ stream: true }))
})

gulp.task('ie', function () {
 // Process ie styles as seperate stylesheets
  return gulp.src('src/**/*.ie.styl')
    .pipe(plumber())
    .pipe(stylus({
      'include css': true,
      use: [jeet(), rupture()]
    }))
    .pipe(concat('ie.css'))
    // move the file to the assets folder
    .pipe(gulp.dest('assets/css'))
    // Reload the browser CSS after every change
    .pipe(reload({ stream: true }))
})

gulp.task('postcss', function () {
  return gulp.src('assets/css/*.css')
  // Group all media queries together
  .pipe(cmq())
  // Use CSSO to remove redundant code and group the same code together
  .pipe(csso())
  // Use CCSNano to minify the code and add browser prefixes
  .pipe(
    cssnano({
      autoprefixer: { browsers: supportedBrowsers, add: true }
    })
  )
  // Add .min to the end
  .pipe(rename({ suffix: '.min' }))
  // move the file to the assets folder
  .pipe(gulp.dest('assets/css'))
})

gulp.task('styles', function (done) {
  runSequence('clean-styles', 'stylus', 'print', 'ie', 'postcss', function () {
    done()
  })
})

gulp.task('php', function () {
  // Move PHP components to assets folder
  return gulp.src('src/**/*.php')
    .pipe(gulp.dest('views/'))
})

// Move 3rd party libraries to the assets/vendors folder
// Note: This will probably be replaced with require.js
gulp.task('vendors', function () {
  // Move jQuery, ie.js and any files in src/vendors
  return gulp.src([
    'node_modules/jquery/dist/jquery.min.js',
    'node_modules/ie8-js/js/build/ie8-js-html5shiv.js',
    'src/vendors/**/*'
  ])
  .pipe(gulp.dest('assets/vendors'))
})

// Move static content to assets folder
gulp.task('static', function () {
  // Move fonts
  return gulp.src('src/globals/fonts/**/*')
    .pipe(gulp.dest('assets/fonts'))
})

/* Reload task */
gulp.task('bs-reload', function () {
  browserSync.reload()
})

/* Prepare Browser-sync for localhost */
gulp.task('browser-sync', function () {
  /* Initialise BrowserSync */
  browserSync.init(['assets/css/*.css', 'assets/js/*.js'], {
    proxy: 'mojintranet.test',
    port: 3000
  })
})

// delete generated styles
gulp.task('clean-styles', function (done) {
  // You can use multiple globbing patterns as you would with `gulp.src`
  del('assets/css')
  done()
})

// delete generated folders (useful for cleanup)
gulp.task('clean-all', function (done) {
  // You can use multiple globbing patterns as you would with `gulp.src`
  del(['assets/**/*', 'views/**/*'])
  done()
})

// enable the child theme
gulp.task('enable-child', function (done) {
  exec('node childtheme --on')
  done()
})

// disable the child theme
gulp.task('disable-child', function (done) {
  exec('node childtheme --off')
  done()
})

gulp.task('build', function (done) {
  return runSequence('clean-all', 'styles', 'scripts', 'php', 'vendors', 'static', function () {
    if (argv.nochild) {
      runSequence('disable-child')
    } else {
      runSequence('enable-child')
    }
    // Announce that the build is complete
    notifier.notify({ title: 'Build', message: 'Complete' })
    done()
  })
})

// If the theme has been updated this will do the updates to the package (Note: A prerelease update will be done each commit)
gulp.task('version-bump', function (done) {
  if (argv.major) {
    exec('npm version major')
  } else if (argv.minor) {
    exec('npm version minor')
  } else if (argv.minor) {
    exec('npm version patch')
  } else {
    exec('npm version prerelease')
  }
})

// Prepare the build for deployment
gulp.task('deploy-prep', function (done) {
  exec('node genreg')
  runSequence('build', 'enable-child')
  done()
})

/* Watch styles, js and php files, doing different things with each. - no browsersync */
gulp.task('watch', ['build'], function () {
  /* Watch styl files, run the styles task on change. */
  gulp.watch(['src/**/*.styl'], ['styles'])
  /* Watch js file, run the scripts task on change. */
  gulp.watch(['src/**/*.js'], ['scripts'])
  /* Watch php file, run the php task on change. */
  gulp.watch(['src/**/*.php'], ['php'])
  // Announce that the build is complete and that gulp is watching for changes
  notifier.notify({ title: 'Watching for changes...', message: 'You may dismiss this message.' })
})

/* Watch styles, js and php files, doing different things with each. - with browsersync */
gulp.task('default', ['watch', 'browser-sync'], function () {
  /* Watch .php files, run the bs-reload task on change. */
  gulp.watch(['*.php', '*.css', '*.js'], ['bs-reload'])
})
