<?php
function amp_buddypress_change_redirect($locations) {
  $locations[0] = 'amp/buddypress';
  $locations[1] = 'amp/community';
  return $locations;
}
