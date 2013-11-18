<?php 
if(!empty($data)) {
foreach($data as $product) {
  echo $product['Service']['libelle']. '|' .$product['Service']['id'] . "\n";
 }
}
else {
 echo 'No results';
}
?>
