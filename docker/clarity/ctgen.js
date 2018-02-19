#!/usr/bin/env node

/* Ctgen v1.0.0
   Author: Alex Foxleigh (github.com/foxleigh81)
   Description: A tool to automatically generate a component and it's associated folder structure.
   Usage: 'node ctgen'
 */

var chalk = require('chalk')
var inquirer = require('inquirer')
var exec = require('child_process').exec
var fs = require('fs')
var path = require('path')
var yml = require('js-yaml')

// Initialise a config variable
var config

// Later on we'll need to convert a string to camel case. This function will do that nicely.
function camelize (str) {
  return str.replace(/(?:^\w|[A-Z]|\b\w|\s+)/g, function (match, index) {
    if (+match === 0) return '' // or if (/\s+/.test(match)) for white spaces
    return index === 0 ? match.toLowerCase() : match.toUpperCase()
  })
}

// 1: User types 'ctgen' the welecome message is immediately displayed

console.log(chalk.magenta('============================================='))
console.log(chalk.white(' Clarity Toolkit Component Generator (ctgen) '))
console.log(chalk.magenta('=============================================\n\n'))

// 2: ctgen checks that it is being called in the root folder and displays an error otherwise
fs.stat(path.join('clarity.yml'), function (err, stat) {
  if (err === null) {
    console.log(chalk.green('clarity.yml detected!\nLoading your preferences...\n\n'))
    config = yml.safeLoad(fs.readFileSync(path.join('clarity.yml'), 'utf8'))
    questionTime()
  } else if (err.code === 'ENOENT') {
    console.error(chalk.red('clarity.yml not found. Please add one to the root of your project. A template can be found at https://git.io/v5Tt2 \nProcess aborted with errors'))
    process.exit(1)
  }
})

var questions = [
  {
    type: 'input',
    name: 'componentName',
    message: 'What is your component called?',
    default: 'My New Component',
    validate: function (value) {
      var pass = value.length > 0
      if (pass) return true
      return 'Component name cannot be empty'
    }
  },
  {
    type: 'input',
    name: 'componentDesc',
    message: 'Please describe your component',
    validate: function (value) {
      var pass = value.length > 0
      if (pass) return true
      return 'Description cannot be empty'
    }
  },
  {
    type: 'confirm',
    name: 'usesJavaScript',
    message: 'Does your component use JavaScript?',
    default: false
  },
  {
    type: 'confirm',
    name: 'createPrintSheets',
    message: 'Do you want to add print stylesheets?',
    default: false
  },
  {
    type: 'confirm',
    name: 'createIESheets',
    message: 'Do you want to add IE specifc stylesheets?',
    default: false
  }
]

// Ask the user the predefined questions
var questionTime = function () {
  console.log(chalk.yellow('Please answer the following:\n\n'))
  inquirer.prompt(questions).then(function (answers) {
    fileGen(answers)
  })
}

var createFile = function (fileName, componentMeta, type, answers) {
  // Tell the user what is happening
  console.log(chalk.blue('\rGenerating file from', fileName, '...'))
  // Bring in the scaffold file
  var scaffold = path.join('./scaffold/', fileName)
  fs.readFile(scaffold, 'utf8', function (err, data) {
    if (err) return console.log(chalk.red(err))
    // Replace %cname% with component name in dashed format
    var result = data.replace(/%cname%/g, componentMeta.name)
    // Replace %ccname% with component name in camelCase format
    result = result.replace(/%namecc%/g, componentMeta.namecc)
    // Replace %classes% with the correct classes string
    result = result.replace(/%classes%/g, componentMeta.classes)

    if (type === 'doc') {
      var d = new Date()
      result = result.replace(/%cfname%/g, answers.componentName)
      result = result.replace(/%cdesc%/g, answers.componentDesc)
      result = result.replace(/%cauthor%/g, config.author.name)
      result = result.replace(/%cagithub%/g, config.author.giturl)
      result = (answers.usesJavaScript) ? result.replace(/%has_js%/g, true) : result.replace(/%has_js%/g, false)
      // BUG: getMonth() has returned the wrong month.
      result = result.replace(/%creationdate%/g, d.getDate() + '/' + (d.getMonth() + 1) + '/' + d.getFullYear())
    }

    var compiledFileName = (type === 'view') ? fileName.replace('ext', config.defaults.viewType) : fileName

    fs.writeFile(path.join('./src/components/' + componentMeta.name + '/' + compiledFileName), result, function (err) {
      if (err) return console.log(chalk.red(err))
    })
  })
  return console.log(chalk.green('Done'))
}

var fileGen = function (answers) {
  console.log(chalk.blue('\n\nCreating folders for', answers.componentName, 'component'))
  var componentName = 'c-' + answers.componentName.toLowerCase().split(' ').join('-')
  var componentNameCamel = camelize(answers.componentName)
  var jsCname = componentName.replace('c-', 'js-')
  var classList = (answers.usesJavaScript) ? componentName + ' ' + jsCname : componentName
  var componentMeta = {
    name: componentName,
    namecc: componentNameCamel,
    classes: classList
  }
  var componentFolder = path.join('./src/components/', componentName)

  // 4: A folder is created in /src/components with the component name
  if (!fs.existsSync(componentFolder)) {
    try {
      fs.mkdirSync(componentFolder)
    } catch (exception) {
      console.log(exception)
    }
  }
  // 5: The following files are created in that folder
  // - view.ext
  createFile('view.ext', componentMeta, 'view', answers)
  // - style.styl
  createFile('style.styl', componentMeta)
  // - script.js (if the user answered yes to Q5)
  if (answers.usesJavaScript) createFile('rename_me.js', componentMeta)
  // - style.print.styl (if the user answered yes to Q6)
  if (answers.createPrintSheets) createFile('style.print.styl', componentMeta)
  // - style.ie.styl (if the user answered yes to Q7)
  if (answers.createIESheets) createFile('style.ie.styl', componentMeta)
  // - component.json (This works a little differently and takes the answers as well as the componentMeta data)
  createFile('component.json', componentMeta, 'doc', answers)
  // - finish up
  setTimeout(function () {
    finishUp(componentName, answers, exec('node genreg'))
  }, 500)
}

var finishUp = function (componentName, answers) {
  console.log(chalk.green('Component "', componentName, '" has been created'))
  // 7. A message is displayed to the user that component [component name] has been created and is ready to edit
  console.log(chalk.white.bgRed('\n  ** Please note: **  '))
  console.warn(chalk.white('\nThis has generated most of the documentation required in component.json but please ensure you keep it up to date and add any additional information to it.'))
  if (answers.usesJavaScript) console.warn(chalk.white('\nAs you have specified JavaScript is being used, an example plugin has been created for you. Please ensure you rename this file to the name of your function and keep to the "one function per file" principle\n'))
}
