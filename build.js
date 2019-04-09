const fs = require('fs');
const webpack = require('webpack');
const css = require('clean-css');

let index = fs.readFileSync('/build/src/index.html').toString();

// Build JavaScript with webpack.
webpack(
    {
        mode: 'production',
        entry: '/build/src/js/app.js',
        output: {
            path: '/build/dist/js/',
            filename: 'app.js'
        }
    },
    (err, stats) => {
        if (err || stats.hasErrors()) {
            console.error(err || stats.toString({chunks: false}));
            process.exit(1);
        }
    }
);

// Minify CSS. Start with a local import to have relative imports inside ready to be found.
let out = new css({level: 2}).minify('@import url(/build/src/css/style.css);');
fs.writeFileSync('/build/dist/css/style.css', out.styles);

// Load templates and create a cache inside the index.html file.
let cache = '', files = fs.readdirSync('/build/src/templates/');
files.forEach(function (file) {
    file = file.toString();
    let id = file.replace(/\.html$/, '');
    let content = fs.readFileSync('/build/src/templates/' + file);
    cache += `<template id="${id}">${content.toString()}</template>`;
});
index = index.replace(/<!-- #templates# .*? -->/, cache);

// Replace the module type for browsers not supporting it.
index = index.replace(/type="module"/, 'type="text/javascript"');

fs.writeFileSync('/build/dist/index.html', index);

