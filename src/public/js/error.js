(function(d, o) {window.onerror = function (e) {
console.error.call(null, arguments);
d.querySelector('#main').appendChild(o = d.createElement('div'));
o.setAttribute('class', 'error');
o.innerHTML = 'Leider ist bei der Verarbeitung ein Fehler aufgetreten.<br/>Das geschieht z.B. durch die Verwendung veralteter oder nicht unterstützter Browser.';
e && (o.innerHTML += '<br/><br/>Folgender Fehler wird vom Browser mitgeteilt:<br/>' + e);
};})(document);

