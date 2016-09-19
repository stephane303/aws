<?php
echo date("F j, Y, g:i a").PHP_EOL; 
if ( php_sapi_name() !== 'cli') {
    echo 'Only cli';die;
}
require 'vendor/autoload.php';
require 'config.php';
require 'lib.php';

foreach($allAccounts as &$account){

    $account['client'] = 
        \Aws\Ec2\Ec2Client::factory(array(
           'credentials' =>  new \Aws\Credentials\Credentials($account['key'], $account['secret']),
           'region' => $account['region'],
           'scheme' => 'http',
           'version' => '2016-04-01'
       ));
       terminateAllInstances($account);
}

foreach($allAccounts as &$account){
    startAllInstances($account);
}

// Traitement des accounts en erreur
echo 'Traitement des erreurs:'.PHP_EOL;
foreach($allAccounts as $account){
    if (isset($account['Exception'])) {
        // On refait tous le binz
        terminateAllAccountInstances($account );  
        startAllInstances($account);        
    }
}

