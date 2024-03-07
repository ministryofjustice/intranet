#!/usr/bin/env node

/* GenReg v1.0.0
   Author: Alex Foxleigh (github.com/foxleigh81)
   Description: A tool to find all component.json files in the project and use them to generate
   a register of components.
   Usage: 'node genreg'
 */

var glob = require('glob-fs')({ gitignore: true })
var chalk = require('chalk')
var fs = require('fs')
var pretty = require('pretty')

// A string containing the header for the component register
var regHeader = '<!DOCTYPE html><html lang="en"><head> <meta charset="UTF-8"> <title>Clarity component register</title> <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous"> <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css"> <style>body{font-family: ariel, helvetica, sans-serif; padding: 10px 10%}table{width: 100%; margin-bottom: 20px; border: 1px solid #595959; margin-top: 20px}  caption {caption-side: top;} thead th{background-color: #4169E1; font-weight: bold; color: #fff; padding: 10px; border-bottom: 1px solid #595959;}tr:nth-child(even){background-color: #f4f4f4}td, th{padding: 10px}h1{font-size: 40px; font-weight: bold; margin-bottom: 20px; text-transform: uppercase;}p{line-height: 1.8; margin-bottom: 18px;}strong{font-weight: bold;}.icon-cell{padding: 0; text-align: center;}.alert{border-radius: 0; margin-bottom: 0; border: 0;}.component-details{display: none; background: #fff; position: absolute; top: 0; right: 0; z-index: 100;}.component-details table{margin: 0; width: 500px;}.revealer{position: relative; cursor: pointer; background-color: #333333; border-bottom: 1px solid #595959;}.revealer i{color: #fff;}.revealer:hover .component-details{display: block;}</style> <script type="text/javascript"></script></head><body> <h1>Component Register</h1> <p>This document is a list of all of the components that the clarity theme currently has available. Please do not edit this file, it is generated automatically. To regenerate it type `node genreg` in the project root.</p><table> <thead> <tr> <th>Component Name</th> <th>Latest version</th> <th>Author</th> <th>Description</th> <th>Status</th> <th>?</th> </tr></thead> <tbody>'
// A string containing the footer for the component register
var regFooter = '</tbody></table><p><strong>Please note:</strong> This file relies on a component being correctly documented and may not be 100% accurate</p></body></html>'

// Loop through all the files in the component folder
var getFiles = function() {
    var files = glob.readdirSync('src/components/**/component.json')
    var string = '<caption>Showing a total of ' + files.length + ' components</caption>'
    for (var i = 0; i < files.length; i++) {
        string = string + readFiles(files[i])
    }
    return string
}

// Reach each file and pass the contents to createRows()
var readFiles = function(file) {
    var obj = JSON.parse(fs.readFileSync(file, 'utf8'))
    return createRows(obj)
}

// Generate a HMTL table row with all the component data
var createRows = function(obj) {
    var status = ''
    if (obj.integrated) {
        if (obj.is_working) {
            status = '<td class="alert alert-success icon-cell"><i class="fa fa-check-square" aria-hidden="true" ></i></td>'
        } else {
            status = '<td class="alert alert-danger icon-cell"><i class="fa fa-check-exclamation" aria-hidden="true" ></i></td>'
        }
    } else {
        status = '<td class="alert alert-success icon-cell"><i class="fa fa-puzzle-piece" aria-hidden="true" ></i></td>'
    }
    // Get the last item in the changelog
    var log = obj.changelog
    for (var item in log) {
        if (log.hasOwnProperty(item)) {
            var changelog = '<tr><th>Last update</th><td><span title="' + log[item].description + '">' + log[item].author + ' on ' + log[item].date + '</span></td></tr>'
        }
    }
    try {
        var thumbURL = fs.readFileSync('./src/components/' + obj.class + '/thumbnail.png', 'utf8')
        var thumb = '<img style="width:300px;height:auto" src="src/components/' + obj.class + '/thumbnail.png" alt="Thumbnail for ' + obj.name + '" />'
    } catch (err) {
        if (err.code === 'ENOENT') {
            var thumb = '<img style="width:300px;height:auto" src="scaffold/no-thumbnail.png" alt="No thumbnail found for ' + obj.name + '" />'
        } else {
            throw err;
        }
    }


    var exampleLink = (obj.example_link) ? '<th> Example link : </th><td><a href="' + obj.example_link + '?devtools=true&show=components">' + obj.example_link + '</a> </td>' : ''

    var hasJs = (obj.has_js) ? 'yes' : 'no'

    return ('<tr><td>' + obj.name + '</td><td>' + obj.version + '</td><td><a href="http://github.com/' + obj.creator.github_username + '">' + obj.creator.name + '</a></td><td>' + obj.description + '</td>' + status + '<td class="icon-cell revealer"><i title="See more..." class="fa fa-ellipsis-v" aria-hidden="true"></i><div class="component-details"><table><tr><th>Thumbnail:</th><td>' + thumb + '</td></tr><tr><th>Class Name:</th><td>.' + obj.class + '</td></tr><tr>' + exampleLink + '</tr><tr><th>Uses JS</th><td>' + hasJs + '</td></tr><tr>' + changelog + '</table></div></td></tr>')
}

// merge the header and footer with the data from getFiles()
var createFile = function() {
    return pretty(regHeader + getFiles() + regFooter)
}

// Create the component-register file
fs.writeFile('component-register.html', createFile(), function(err) {
    if (err) return console.log(chalk.red(err))
    console.log(chalk.green('Component register updated!'))
})