<?php
header("Access-Control-Allow-Origin: *");
// Load the XML source
$xml = new DOMDocument;
// Create a stream
function download_page($path){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$path);
    curl_setopt($ch, CURLOPT_USERAGENT, "PHP/".PHP_VERSION );
    curl_setopt($ch, CURLOPT_FAILONERROR,1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $retValue = curl_exec($ch);          
    curl_close($ch);
    return $retValue;
}
// Open the file using the HTTP headers set above
$links = $_GET["link"];
$stack = [];

if($links) {
  foreach ($links as &$value) {
    $file = download_page($value);
    $xml=simplexml_load_string($file);
    array_push($stack, array(
       "name" => (string)$xml->SagsNr, 
       "link" => $value,
       "image" => (string)$xml->Media->Fotos->Foto[0]->ProtectedFilename,
       "images" => $xml->Media->Fotos,
       "title" => (string)$xml->Marketing->AnnonceOverskrift,
       "labelText" => (string)$xml->Marketing->LabelTekst->Tekst,
       "description" => (string)$xml->Marketing->AnnonceTekst,
       "finance" => $xml->Finansiering,
       "property" => $xml->Ejendom
    ));
  }

  echo json_encode($stack);
}
else {
  echo "i received:";
  echo json_encode($_GET);
  echo "missing links";
}