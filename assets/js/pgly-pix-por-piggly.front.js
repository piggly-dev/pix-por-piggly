/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
var __webpack_exports__ = {};
/*!**************************!*\
  !*** ./js/front/main.ts ***!
  \**************************/

window.pixCopyText = function (value, id) {
    if (navigator.clipboard) {
        navigator
            .clipboard
            .writeText(value)
            .then(function () { window.pixCopied(id); })
            .catch(function () { window.pixCopyFallback(value, id); });
    }
    else {
        window.pixCopyFallback(value, id);
    }
};
window.pixCopyFallback = function (value, id) {
    var el = document.createElement('textarea');
    el.value = value;
    el.setAttribute('readonly', '');
    el.style.position = 'absolute';
    el.style.left = '-9999px';
    document.body.appendChild(el);
    el.select();
    el.setSelectionRange(0, 99999);
    var copy = document.execCommand('copy');
    document.body.removeChild(el);
    if (copy) {
        window.pixCopied(id);
    }
    if (window.getSelection() !== null && window.getSelection() !== undefined) {
        window.getSelection().removeAllRanges();
    }
};
window.pixCopied = function (id) {
    var el = document.getElementById(id);
    var cn = el.innerHTML;
    el.innerHTML = 'Copiado';
    setTimeout(function () { el.innerHTML = cn; }, 1500);
};

/******/ })()
;
//# sourceMappingURL=pgly-pix-por-piggly.front.js.map