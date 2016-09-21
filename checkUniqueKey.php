<?php 
require 'config.php';

foreach ($allAccounts as $account){
  $a[$account['key']] = 1;
}

echo count($a);
