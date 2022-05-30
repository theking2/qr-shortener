"use strict";

document.addEventListener("DOMContentLoaded", ev => {

  // initial qr
  doQR(ev);

  // set the handler for the ok button
  document.getElementById("do-qr").onclick = doQR;
  document.getElementById("container").onclick = downloadSVG;
  document.getElementById("bg-color").onchange = doQR;
  document.getElementById("color").onchange = doQR;
  document.getElementById("size").onchange = doQR;


  // select all on focus
  const url = document.getElementById('url');
  url.addEventListener('focus', ev => ev.target.select());

  // clear query_string
  history.pushState({}, '', '/');
  url.select();
  url.focus();

});

/**
 * shorten a url by creating a server request.
 */
const doShorten = ev => {

  const url = document.getElementById('url').value;
  window.location.assign = `<?=base_url?>/?url=${url}`;
  window.location.reload();

};

/**
 * create a qr code of the contents of url
 */
const doQR = ev => {
  const url = document.getElementById('url').value;

  if (url.length === 0) return; // no url, stop

  let qrcode = new QRCode({
    content: url,
    padding: 2,
    width: document.getElementById('size').value, height: document.getElementById('size').value,
    join: false,
    color: document.getElementById('color').value,
    background: document.getElementById('bg-color').value,
    ecl: "L"
  });

  document.getElementById("container").innerHTML = qrcode.svg();
  ev.preventDefault();
};

/**
 * create a download link
 */
const downloadSVG = () => {
  const svg = document.getElementById('container').outerHTML;
  const blob = new Blob([svg.toString()]);
  const element = document.createElement("a");
  try {
    const encode = new URL('', document.getElementById('shorten').dataset.fullUrl);
    element.download = encode.hostname.replaceAll('.', '_') + ".svg";
  } catch {
    // probably don't have a url
    element.download = 'qr.svg';
  }

  element.href = window.URL.createObjectURL(blob);
  element.click();
  element.remove();
}
