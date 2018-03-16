module.exports = function(grunt) {
  "use strict";

  var srcDir = grunt.option('source-path') || 'web/app/themes';
  var themePath = '/mojintranet/';

  var jsSrcListBase = [
    srcDir + themePath + 'assets/js/src/setup.js',
    srcDir + themePath + 'assets/js/src/tools.js',
    srcDir + themePath + 'assets/js/src/tools/**/*.js',
    srcDir + themePath + 'assets/js/src/widgets/**/*.js',
    srcDir + themePath + 'assets/js/src/skeleton_screens.js'
  ];

  var jsSrcListMain = [
    srcDir + themePath + 'assets/js/src/modules/**/*.js',
    srcDir + themePath + 'assets/js/src/init.js'
  ];

  var cssSrcList = {};
  cssSrcList[srcDir + themePath + 'assets/css/style.css'] = srcDir + themePath + 'assets/css/src/style.scss';
  cssSrcList[srcDir + themePath + 'assets/css/ie.css'] = srcDir + themePath + 'assets/css/src/ie.scss';
  cssSrcList[srcDir + themePath + 'assets/css/fonts.css'] = srcDir + themePath + 'assets/css/src/fonts.scss';

  // Project configuration.
  grunt.initConfig({
    env: {
      TEST_DOMAIN: process.env.TEST_DOMAIN || 'http://mojintranet'
    },
    pkg: grunt.file.readJSON('package.json'),
    connect: {
      test : {
        port : 8000
      }
    },
    uglify: {
      options: {
        banner: '/*! <%= pkg.name %> <%= grunt.template.today("yyyy-mm-dd") %> */\n'
      },
      build: {
        files: (function() {
          var obj = {};
          obj[srcDir + themePath + 'assets/js/base.js'] = [srcDir + themePath + 'assets/js/base.js'];
          obj[srcDir + themePath + 'assets/js/main.js'] = [srcDir + themePath + 'assets/js/main.js'];
          return obj;
        }())
      }
    },
    concat: {
      dist: {
        files: (function() {
          var obj = {};
          obj[srcDir + themePath + 'assets/js/base.js'] = [jsSrcListBase];
          obj[srcDir + themePath + 'assets/js/main.js'] = [jsSrcListMain];
          return obj;
        }()),
      }
    },
    sass: {
      dev: {
        options: {
          //          sourcemap: 'none',
          lineNumbers: true,
          unixNewlines: true
        },
        files: cssSrcList
      },
      dist: {
        options: {
          style: 'compressed',
          //          sourcemap: 'none',
          unixNewlines: true
        },
        files: cssSrcList
      }
    },
    csslint: {
      strict: {
        options: {
          'adjoining-classes': false,
          'overqualified-elements': false,
          'import': false,
          'empty-rules': false,
          'zero-units': false,
          'box-model': false,
          'compatible-vendor-prefixes': false
        },
        src: [
          srcDir + themePath + 'assets/css/style.css'
        ]
      }
    },
    cachebuster: {
      build: {
        options: {
          banner: '',
          format: 'json',
          basedir: srcDir
        },
        src: [
          srcDir + themePath + '/assets/js/base.js',
          srcDir + themePath + '/assets/js/main.js',
          srcDir + themePath + '/assets/css/style.css',
          srcDir + themePath + '/assets/css/ie.css'
        ],
        flatten: true,
        dest: srcDir + themePath + 'checksums.json'
      }
    },
    "git-describe": {
      options: {
        cwd: srcDir
      },
      trigger: {}
    }
  });

  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-contrib-sass');
  grunt.loadNpmTasks('grunt-cachebuster');

  grunt.registerTask('generate-build-json', '', function() {
    grunt.file.write(srcDir + themePath + 'build.json', JSON.stringify({
      version_number: "unknown",
      commit_id: grunt.option('gitRevision'),
      build_date: grunt.template.today(),
      build_tag: grunt.option('gitTag')
    }) + "\n");
  });

  grunt.registerTask('pre_deploy', [
    'concat',
    'sass:dev',
    'cachebuster',
    'generate-build-json'
  ]);
};
