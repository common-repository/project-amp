<?php

function amp_bbpress_change_redirect($locations) {
  $locations[0] = 'amp/bbpress';
  $locations[1] = 'amp/forums';
  return $locations;
}
