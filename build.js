const fs = require('fs');
const webpack = require('webpack');
const CleanCss = require('clean-css');

const error = function (msg) {
    console.error(msg);
    process.exit(1);
};

let index = fs.readFileSync('/build/src/index.html').toString();

// Build JavaScript with webpack and babel. Use polyfills and babel to support Sailfish Browser.
webpack(
    {
        mode: 'production',
        entry: ['@babel/polyfill', 'whatwg-fetch', '/build/src/js/app.js'],
        output: {
            path: '/build/dist/js/',
            filename: 'app.js',
        },
        module: {
            rules: [
                {
                    use: {
                        loader: 'babel-loader',
                        options: {
                            presets: ['@babel/preset-env'],
                        },
                    },
                },
            ],
        },
    },
    (err, stats) => (err || stats.hasErrors()) && error(err || stats.toString({chunks: false})),
);

// Minify CSS. Start with a local import to have relative imports inside ready to be found.
let style = new CleanCss({level: 2}).minify('@import url(src/css/style.css);');
style.errors.length && error(style.errors);

// Load templates and create a cache inside the index.html file.
let cache = '', files = fs.readdirSync('/build/src/templates/');
files.forEach((file) => {
    file = file.toString();
    let id = file.replace(/\.html$/, '');
    let content = fs.readFileSync('/build/src/templates/' + file);
    cache += `<template id="${id}">${content.toString()}</template>`;
});
index = index.replace(/<!-- #templates# .*? -->/, cache);

// Replace the module type for browsers not supporting it.
index = index.replace(/type="module"/, 'type="text/javascript"');

// Write all files not already written by the corresponding modules.
fs.writeFileSync('/build/dist/css/style.css', style.styles);
fs.writeFileSync('/build/dist/index.html', index);
// There is no need to compress this file.
fs.copyFileSync('/build/src/js/error.js', '/build/dist/js/error.js');
