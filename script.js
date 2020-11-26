jQuery(function() {

    /* DOKUWIKI:include script/WbCookie.js */
    /* DOKUWIKI:include script/WbToggle.js */

});
/* -------------------------------------------------------------------- */
function Copy2Clipboard(inElementId, inWidth="960px"){ // TODO width not working
    var aux = document.createElement("div");
    aux.setAttribute("contentEditable", true);
    aux.style.backgroundColor ='#fff'; // Otherwise body background is taken
    aux.style.width = "960px"; // inWidth;
    aux.innerHTML = document.getElementById(inElementId).innerHTML;
    aux.setAttribute("onfocus", "document.execCommand('selectAll',false,null)");
    document.body.appendChild(aux);
    aux.focus();
    document.execCommand("copy");
    document.body.removeChild(aux);
    alert("Content copied to clipboard.");
}
/* -------------------------------------------------------------------- */