var wrapper = document.getElementById("signature-pad");
var cursoId = document.getElementById("signature-pad-curso").value;
var modId = document.getElementById("signature-pad-mod").value;
var empresa = document.getElementById("signature-pad-emp").value;
var dni = document.getElementById("signature-pad-dni").value;

var clearButton = wrapper.querySelector("[data-action=clear]");
var changeColorButton = wrapper.querySelector("[data-action=change-color]");
var undoButton = wrapper.querySelector("[data-action=undo]");
var savePNGButton = wrapper.querySelector("[data-action=save-png]");
var saveJPGButton = wrapper.querySelector("[data-action=save-jpg]");
var saveSVGButton = wrapper.querySelector("[data-action=save-svg]");
var btnGuardarFirma = wrapper.querySelector("[data-action=guardar-firma]");
var canvas = wrapper.querySelector("canvas");
var signaturePad = new SignaturePad(canvas, {
    // It's Necessary to use an opaque color when saving image as JPEG;
    // this option can be omitted if only saving as PNG or SVG
    backgroundColor: 'rgb(255, 255, 255)'
});

// Adjust canvas coordinate space taking into account pixel ratio,
// to make it look crisp on mobile devices.
// This also causes canvas to be cleared.
function resizeCanvas() {
    // When zoomed out to less than 100%, for some very strange reason,
    // some browsers report devicePixelRatio as less than 1
    // and only part of the canvas is cleared then.
    var ratio =  Math.max(window.devicePixelRatio || 1, 1);

    // This part causes the canvas to be cleared
    canvas.width = canvas.offsetWidth * ratio;
    canvas.height = canvas.offsetHeight * ratio;
    canvas.getContext("2d").scale(ratio, ratio);

    // This library does not listen for canvas changes, so after the canvas is automatically
    // cleared by the browser, SignaturePad#isEmpty might still return false, even though the
    // canvas looks empty, because the internal data of this library wasn't cleared. To make sure
    // that the state of this library is consistent with visual state of the canvas, you
    // have to clear it manually.
    signaturePad.clear();
}

// On mobile devices it might make more sense to listen to orientation change,
// rather than window resize events.
//window.onresize = resizeCanvas;
resizeCanvas();

function download(dataURL, filename) {
    if (navigator.userAgent.indexOf("Safari") > -1 && navigator.userAgent.indexOf("Chrome") === -1) {
        window.open(dataURL);
    } else {
        var blob = dataURLToBlob(dataURL);
        var url = window.URL.createObjectURL(blob);

        var a = document.createElement("a");
        a.style = "display: none";
        a.href = url;
        a.download = filename;

        document.body.appendChild(a);
        a.click();

        window.URL.revokeObjectURL(url);
    }
}

function guardarFirma(dataURL) {
    var blobObject = dataURLToBlob(dataURL);
    var fdataobj = new FormData();
    fdataobj.append("firmaFile", blobObject);
    fdataobj.append("request_type", 'guardarFirma');
    fdataobj.append("cursoId", cursoId);
    fdataobj.append("modId", modId);
    fdataobj.append("empresa", empresa);
    fdataobj.append("dni", dni);

    const XHR = new XMLHttpRequest();

    // Define what happens on successful data submission
    XHR.addEventListener( 'load', function() {
        alert( 'Se cargó la firma con éxito' );
        window.location.href = '/course/view.php?id=28';
    } );

    // Define what happens in case of error
    XHR.addEventListener(' error', function() {
        alert( 'Oops! Algo salió mal.' );
    } );

    // Set up our request
    XHR.open( 'POST', 'https://www.campusqroma.com/local/qroma_front/api/ajax_controller_qroma.php' );

    // Send our FormData object; HTTP headers are set automatically
    XHR.send( fdataobj );

}

// One could simply use Canvas#toBlob method instead, but it's just to show
// that it can be done using result of SignaturePad#toDataURL.
function dataURLToBlob(dataURL) {
    // Code taken from https://github.com/ebidel/filer.js
    var parts = dataURL.split(';base64,');
    var contentType = parts[0].split(":")[1];
    var raw = window.atob(parts[1]);
    var rawLength = raw.length;
    var uInt8Array = new Uint8Array(rawLength);

    for (var i = 0; i < rawLength; ++i) {
        uInt8Array[i] = raw.charCodeAt(i);
    }

    return new Blob([uInt8Array], { type: contentType });
}

clearButton.addEventListener("click", function (event) {
    signaturePad.clear();
});

undoButton.addEventListener("click", function (event) {
    var data = signaturePad.toData();

    if (data) {
        data.pop(); // remove the last dot or line
        signaturePad.fromData(data);
    }
});

changeColorButton.addEventListener("click", function (event) {
    var r = Math.round(Math.random() * 255);
    var g = Math.round(Math.random() * 255);
    var b = Math.round(Math.random() * 255);
    var color = "rgb(" + r + "," + g + "," + b +")";

    signaturePad.penColor = color;
});

btnGuardarFirma.addEventListener("click", function (event) {
    if (signaturePad.isEmpty()) {
        alert("Por favor ingrese una firma");
    } else {
        var dataURL = signaturePad.toDataURL();
        guardarFirma(dataURL, "firma.png");
    }
});

savePNGButton.addEventListener("click", function (event) {
    if (signaturePad.isEmpty()) {
        alert("Por favor ingrese una firma");
    } else {
        var dataURL = signaturePad.toDataURL();
        download(dataURL, "signature.png");
    }
});

saveJPGButton.addEventListener("click", function (event) {
    if (signaturePad.isEmpty()) {
        alert("Por favor ingrese una firma");
    } else {
        var dataURL = signaturePad.toDataURL("image/jpeg");
        download(dataURL, "signature.jpg");
    }
});

saveSVGButton.addEventListener("click", function (event) {
    if (signaturePad.isEmpty()) {
        alert("Por favor ingrese una firma");
    } else {
        var dataURL = signaturePad.toDataURL('image/svg+xml');
        download(dataURL, "signature.svg");
    }
});