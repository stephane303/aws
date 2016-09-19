<?php
echo '===============START================='.PHP_EOL;
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
unset($account);

foreach($allAccounts as &$account){
    startAllInstances($account);
}
unset($account);

// Traitement des accounts en erreur
echo 'Traitement des erreurs:'.PHP_EOL;
foreach($allAccounts as $account){
    echo $account['name'].':'.@$account['Exception'].PHP_EOL;
    if (isset($account['Exception'])) {
        // On refait tout le binz
        terminateAllInstances($account );  
        startAllInstances($account);        
    }
}

echo date("F j, Y, g:i a").PHP_EOL;
echo '===============END==================='.PHP_EOL;

