#!/usr/bin/env node

 var chalk = require('chalk')
 var inquirer = require('inquirer')
 var argv = require('yargs').argv
 var exec = require('child_process').exec
 var pjson = require('./package.json')

/*
   Publish v1.0.0
   Author: Alex Foxleigh (github.com/foxleigh81)
   Description: A tool to automatically bump the version number for the theme and then commit and push the files
   Usage: 'node publish'
   Options:
    default: Updates the version with a pre-release version number (x.x.x-this). This is used for features and updates that are not immediately being released.
    --patch: Adds a patch to the version number (x.x.this). This is used for released hotfixes and minor updates
    --minor: Bumps the minor version (x.this.x). This is used for released features
    --major: Bumps the major version (this.x.x)/ This is used for major releases like redesigns etc...
    --message (-m): Allows you to add a commit log message without prompting
    --nopush (-x): Prevents Publish from pushing changes to the repo
    --force (-f): Skip the 'do you want to continue check'
*/

// TODO: Change the fucntion of this to just push files and merge two branches together.

 var continuePublish = function () {
   console.log(chalk.blue('Preparing files for publishing to github...'))

   var version = pjson.version

   switch (argv) {
     case 'patch' :
       console.log(chalk.blue('Adding patch update to version', version))
       exec('npm version patch', function (error, stdout, stderr) {
         if (error) console.log(chalk.red(error))
       })
       break
     case 'minor' :
       console.log(chalk.blue('Adding new minor update to version', version))
       exec('npm version minor', function (error, stdout, stderr) {
         if (error) console.log(chalk.red(error))
       })
       break
     case 'major' :
       console.log(chalk.blue('Adding new major update to version', version))
       exec('npm version major', function (error, stdout, stderr) {
         if (error) console.log(chalk.red(error))
       })
       break
     default:
       console.log(chalk.blue('Adding new prerelease update to version', version))
       exec('npm version prerelease', function (error, stdout, stderr) {
         if (error) console.log(chalk.red(error))
       })
       break
   }

   var checkCommitMessage = function (message) {
     if (!message) {
       inquirer.prompt([{
         type: 'input',
         message: 'Enter a message to add to the commit log',
         name: 'message',
         validate: function (value) {
           if (value.length >= 3) {
             return true
           }

           return 'Please enter a useful commit message'
         }
       }]).then(function (answers) {
         doCommit(answers.message)
       })
     } else {
       doCommit(argv.m)
     }
   }

   var doCommit = function (message) {
     console.log(chalk.blue('Updating package.json ...'))
     exec('git add package.json', function (error, stdout, stderr) {
       if (error) return console.log(chalk.red(error))
       console.log(chalk.green('Done'))
       console.log(chalk.blue('Committing staged changes to git...'))
       exec('git commit -m "' + message + '"', function (error, stdout, stderr) {
         if (error) {
           console.log(chalk.magenta('You have the following message from git: \n\n') + error + '\n\n' + stdout + '\n\n' + chalk.magenta('Nothing has been updated. \nProcess terminated.'))
         } else {
           console.log(chalk.green('Commit complete'))
           if (argv.x || argv.nopush) {
             console.log(chalk.yellow('Pushing aborted at user request'))
           } else {
             doPush()
           }
         }
       })
     })
   }

   var doPush = function () {
     console.log(chalk.blue('Retrieving latest changes from remote...'))
     exec('git pull', function (error, stdout, stderr) {
       if (error) return console.log(chalk.red(error))
       console.log(chalk.green('Done'))
       console.log(chalk.blue('Pushing latest version to remote...'))
       exec('git push', function (error, stdout, stderr) {
         if (error) return console.log(chalk.red(error))
         console.log(chalk.green('All files pushed to remote.\n\n') + chalk.black.bgGreen(' Operation Complete '))
       })
     })
   }

   if (argv.m !== undefined) {
     var message = argv.m || argv.message
     checkCommitMessage(message)
   } else {
     checkCommitMessage()
   }
 }

// Begin process
 console.log(chalk.green('\n\n\n\n**************************************'))
 console.log(chalk.green('**************************************'))
 console.log(chalk.green('**             PUBLISH              **'))
 console.log(chalk.green('**************************************'))
 console.log(chalk.green('**************************************\n\n'))
 console.log(chalk.white('This package will automatically increase the theme\'s version number and commit + push any staged changes to git\n\n'))

 if (argv.f || argv.force) {
   continuePublish()
 } else {
   inquirer.prompt([{
     type: 'confirm',
     message: 'Do you want to continue?',
     name: 'continue'
   }]).then(function (answers) {
     if (answers.continue === true) {
       continuePublish()
     } else {
       return console.log(chalk.red('Process terminated by user'))
     }
   })
 }
