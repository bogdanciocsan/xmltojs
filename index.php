<?php
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
$domain = $_GET["source"];
$guid = $_GET["guid"];
$stack = [];

echo $guid;

if($domain && $guid) {
  foreach ($guid as &$value) {
    $file = download_page($domain . $value);
    $xml=simplexml_load_string($file);
    array_push($stack, array(
       "name" => (string)$xml->SagsNr, 
       "guid" => $value,
       "image" => (string)$xml->Media->Fotos->Foto[0]->ProtectedFilename,
       "title" => (string)$xml->Marketing->AnnonceOverskrift,
       "labelText" => (string)$xml->Marketing->LabelTekst->Tekst,
       "finance" => $xml->Finansiering,
       "property" => $xml->Ejendom
    ));
  }

  echo json_encode($stack);
}
else {
  echo "missing source and guid params";
}