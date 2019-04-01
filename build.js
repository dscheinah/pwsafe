const css = require('clean-css');
const fs = require('fs');

// Minify CSS. Start with a local import to have relative imports inside ready to be found.
let out = new css({level: 2}).minify('@import url(/build/src/css/style.css);');
fs.writeFileSync('/build/dist/css/style.css', out.styles);

// Load templates and create a cache inside the index.html file.
let cache = '';
fs.readdir('/build/src/templates/', (error, files) => {
    files.forEach(function (file) {
        let id = file.replace(/\.html$/, '');
        let content = fs.readFileSync('/build/src/templates/' + file);
        cache += `<template id="${id}">${content.toString()}</template>`;
    });
    let index = fs.readFileSync('/build/src/index.html');
    fs.writeFileSync('/build/dist/index.html', index.toString().replace(/<!-- #templates# .*? -->/, cache));
});
