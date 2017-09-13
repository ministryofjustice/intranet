module.exports = function(grunt) {
  "use strict";

  var srcDir = grunt.option('source-path') || 'bedrock/web/app/themes';
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

  var jsHintDefaultOptions = {
    // Enforcing
    "bitwise" : true, // true: Prohibit bitwise operators (&, |, ^, etc.)
    "camelcase" : false, // true: Identifiers must be in camelCase
    "curly" : true, // true: Require {} for every new block or scope
    "eqeqeq" : true, // true: Require triple equals (===) for comparison
    "forin" : true, // true: Require filtering for..in loops with obj.hasOwnProperty()
    "freeze" : true, // true: prohibits overwriting prototypes of native objects such as Array, Date etc.
    "immed" : true, // true: Require immediate invocations to be wrapped in parens e.g. `(function () { } ());`
    "indent" : 4, // {int} Number of spaces to use for indentation
    "latedef" : true, // true: Require variables/functions to be defined before being used
    "newcap" : true, // true: Require capitalization of all constructor functions e.g. `new F()`
    "noarg" : true, // true: Prohibit use of `arguments.caller` and `arguments.callee`
    "noempty" : true, // true: Prohibit use of empty blocks
    "nonbsp" : true, // true: Prohibit "non-breaking whitespace" characters.
    "nonew" : true, // true: Prohibit use of constructors for side-effects (without assignment)
    "plusplus" : false, // true: Prohibit use of `++` & `--`
    "quotmark" : false, // Quotation mark consistency:
      // false : do nothing (default)
      // true : ensure whatever is used is consistent
      // "single" : require single quotes
      // "double" : require double quotes
    "undef" : true, // true: Require all non-global variables to be declared (prevents global leaks)
    "unused" : false, // true: Require all defined variables be used
    "strict" : true, // true: Requires all functions run in ES5 Strict Mode
    "maxparams" : false, // {int} Max number of formal params allowed per function
    "maxdepth" : false, // {int} Max depth of nested blocks (within functions)
    "maxstatements" : false, // {int} Max number statements per function
    "maxcomplexity" : false, // {int} Max cyclomatic complexity per function
    "maxlen" : false, // {int} Max number of characters per line
    // Relaxing
    "asi" : false, // true: Tolerate Automatic Semicolon Insertion (no semicolons)
		"reporterOutput": "",
    "boss" : false, // true: Tolerate assignments where comparisons would be expected
    "debug" : false, // true: Allow debugger statements e.g. browser breakpoints.
    "eqnull" : false, // true: Tolerate use of `== null`
    "es5" : false, // true: Allow ES5 syntax (ex: getters and setters)
    "esnext" : false, // true: Allow ES.next (ES6) syntax (ex: `const`)
    "moz" : false, // true: Allow Mozilla specific syntax (extends and overrides esnext features)
    // (ex: `for each`, multiple try/catch, function expression�)
    "evil" : false, // true: Tolerate use of `eval` and `new Function()`
    "expr" : false, // true: Tolerate `ExpressionStatement` as Programs
    "funcscope" : false, // true: Tolerate defining variables inside control statements
    "globalstrict" : false, // true: Allow global "use strict" (also enables 'strict')
    "iterator" : false, // true: Tolerate using the `__iterator__` property
    "lastsemic" : false, // true: Tolerate omitting a semicolon for the last statement of a 1-line block
    "laxbreak" : false, // true: Tolerate possibly unsafe line breakings
    "laxcomma" : false, // true: Tolerate comma-first style coding
    "loopfunc" : false, // true: Tolerate functions being defined in loops
    "multistr" : false, // true: Tolerate multi-line strings
    "noyield" : false, // true: Tolerate generator functions with no yield statement in them.
    "notypeof" : false, // true: Tolerate invalid typeof operator values
    "proto" : false, // true: Tolerate using the `__proto__` property
    "scripturl" : false, // true: Tolerate script-targeted URLs
    "shadow" : false, // true: Allows re-define variables later in code e.g. `var x=1; x=2;`
    "sub" : false, // true: Tolerate using `[]` notation when it can still be expressed in dot notation
    "supernew" : false, // true: Tolerate `new function () { ... };` and `new Object;`
    "validthis" : false, // true: Tolerate using this in a non-constructor function
    // Environments
    "browser" : true, // Web Browser (window, document, etc)
    "browserify" : false, // Browserify (node.js code in the browser)
    "couch" : false, // CouchDB
    "devel" : true, // Development/debugging (alert, confirm, etc)
    "dojo" : false, // Dojo Toolkit
    "jasmine" : false, // Jasmine
    "jquery" : false, // jQuery
    "mocha" : true, // Mocha
    "mootools" : false, // MooTools
    "node" : false, // Node.js
    "nonstandard" : false, // Widely adopted globals (escape, unescape, etc)
    "prototypejs" : false, // Prototype and Scriptaculous
    "qunit" : false, // QUnit
    "rhino" : false, // Rhino
    "shelljs" : false, // ShellJS
    "worker" : false, // Web Workers
    "wsh" : false, // Windows Scripting Host
    "yui" : false // Yahoo User Interface
  };

  var jsHintDistOptions = Object.create(jsHintDefaultOptions);
  jsHintDistOptions.devel = false;

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
    casperjs: {
      all: {
        files: {
          'test/casper-results-deploy-only.xml': 'test/journeys/deploy_only/*.js',
          'test/casper-results-shared.xml': 'test/journeys/*.js'
        }
      },
      prod: {
        files: {
          'test/casper-results-shared.xml': 'test/journeys/*.js'
        }
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
    jshint: {
      options: {
        newcap: true,
        globals: {
          jQuery: true,
          App: true
        }
      },
      dev: {
        options: jsHintDefaultOptions,
        files: {
          src: [jsSrcListBase, jsSrcListMain]
        }
      },
      dist: {
        options: jsHintDistOptions,
        files: {
          src: [jsSrcListBase, jsSrcListMain]
        }
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
    watch: {
      scripts: {
        files: jsSrcListBase.concat(jsSrcListMain).concat([srcDir + themePath + 'assets/css/src/**/*.scss']),
        tasks: ['default'],
        options: {
          interrupt: true
        }
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
    jasmine: {
      app: {
        src: [
          srcDir + themePath + 'assets/js/base.js',
          srcDir + themePath + 'assets/js/main.js'
        ],
        options: {
          display: 'full',
          host: 'http://127.0.0.1:8000/',
          specs: ['test/specs/*_spec.js'],
          vendor: [srcDir + 'wp-includes/js/jquery/jquery.js'],
          outfile: 'test/specs/spec_runner.html',
          '--web-security': false,
          keepRunner: true
        }
      }
    },
    hipchat_notifier: {
      options: {
        authToken: "3cc43bcc5fea3e06b36eaee1eb07bf", // Create an authToken at https://hipchat.com/admin/api
        roomId: "818640" // Numeric Hipchat roomId or room name
      },

      deploy_success: {
        options: {
          message: "Successfully deployed to " + (grunt.option('env') || "???") + " - " + (grunt.option('branch') || "commit unknown") + " by " + (grunt.option('user')+"." || "magic!"), // A message to send
          from: "Grunt", // Name for the sender
          color: "green", // Color of the message
          message_format: "html" // Can either be 'text' or 'html' format
        }
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

  grunt.loadNpmTasks('grunt-casperjs');
  grunt.loadNpmTasks('grunt-contrib-jshint');
  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-contrib-sass');
  grunt.loadNpmTasks('grunt-hipchat-notifier');
  grunt.loadNpmTasks('grunt-contrib-jasmine');
  grunt.loadNpmTasks('grunt-contrib-csslint');
  grunt.loadNpmTasks('grunt-contrib-connect');
  grunt.loadNpmTasks('grunt-cachebuster');
  grunt.loadNpmTasks('grunt-notify');
  grunt.loadNpmTasks('grunt-env');
  grunt.loadNpmTasks('grunt-git-describe');

  grunt.registerTask('save-git-revision', 'Saves the git revision as grunt property', function () {
    grunt.event.once('git-describe', function (rev) {
      grunt.option('gitRevision', rev.object);
      grunt.option('gitTag', rev.tag);
    });

    grunt.task.run('git-describe:trigger');
  });

  grunt.registerTask('generate-build-json', '', function() {
    grunt.file.write(srcDir + themePath + 'build.json', JSON.stringify({
      version_number: "unknown",
      commit_id: grunt.option('gitRevision'),
      build_date: grunt.template.today(),
      build_tag: grunt.option('gitTag')
    }) + "\n");
  });

  // Default task(s).

  grunt.registerTask('default', [
    'jshint:dev',
    'concat',
    'sass:dev',
    'uglify',
    'cachebuster',
    'connect:test'
  ]);

  grunt.registerTask('pre_deploy', [
    'jshint:dist',
    'concat',
    'sass:dev',
    //'uglify',
    'cachebuster',
    //'save-git-revision',
    'generate-build-json'
  ]);

  grunt.registerTask('css-strict', [
    'sass',
    'csslint'
  ]);

  grunt.registerTask('prod_build', [
    'env',
    'jshint:dist',
    'concat',
    'sass:dist',
    'uglify',
    'cachebuster',
    'connect:test',
    'jasmine',
    'casperjs:all'
    /*,
    'hipchat_notifier:deploy_status'*/
  ]);

  grunt.registerTask('ci', [
    'env',
    'jshint',
    'concat',
    'uglify',
    'connect:test',
    'jasmine',
    'casperjs:all'
  ]);

  grunt.registerTask('smoke_tests', [
    'env',
    'casperjs:prod'
  ]);
};
