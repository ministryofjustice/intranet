#!/usr/bin/env node

 /*
    Childtheme switcher v1.0.0
    Author: Alex Foxleigh
    Description: The rebuild requires the use of a child-theme for production and front-end
    development but this causes problems in backend development.
    This will fix that by turning the child theme on and off.
    Usage: 'node childtheme --on' and 'node childtheme --off'
*/

 var fs = require('fs')
 var chalk = require('chalk')
 var argv = require('yargs').argv
 var pjson = require('./package.json')

 var contents = '/*\n  Theme Name: Clarity Theme\n  Theme URI: http://example.com/\n  Description: Th   e new theme for MOJ Intranet\n  Author: Alex Foxleigh & Irune Itoiz\n  Author URI: http://www.alexward.me.uk & http://irune.io\n'
 var templateOn = '  Template: mojintranet\n'
 var versionString = '  Version: ' + pjson.version + '\n*/'

 var createFile = function () {
   var footer = (argv.on) ? templateOn + versionString : versionString
   return contents + footer
 }

// Create the component-register file
 fs.writeFile('style.css', createFile(), function (err) {
   if (err) return console.log(chalk.red(err))
   console.log(chalk.green('The style.css file was generated!'))
 })

 if (!argv.on) {
   fs.writeFile('index.php', '', function (err) {
     if (err) return console.log(chalk.red(err))
     console.log(chalk.green('The index.php file was generated!'))
   })
 }
