<?php
function rollTheDice($throws = 1, $eyes = 6){
  $return = '';
  for($i = 0; $i < $throws; $i++){
    $result = rand(1, $eyes);
    $return .= $result;
    if($throws > 1){
      $return .= ', ';
    }
  }
  return $return;
}
?>
