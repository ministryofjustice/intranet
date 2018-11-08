<?php

// Force non-PDF documents served via Document Revisions to download instead of opening in the browser
function dw_force_document_download() {
  $document_URI = $_SERVER['REQUEST_URI'];
  $document_extension_array = explode('.', $document_URI);
  $document_extension = end($document_extension_array);
  if(strtolower($document_extension)=='pdf') {
    return true;
  } else {
    return false;
  }
}
add_filter( 'document_content_disposition_inline', 'dw_force_document_download');

?>
