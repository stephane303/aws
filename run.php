<?php
echo '===============START================='.PHP_EOL;
echo date("F j, Y, g:i a").PHP_EOL; 
if ( php_sapi_name() !== 'cli') {
    echo 'Only cli';die;
}
require 'vendor/autoload.php';
require 'config.php';
require 'lib1.php';

foreach($allAccounts as &$account){

    $account['client'] = 
        \Aws\Ec2\Ec2Client::factory(array(
           'credentials' =>  new \Aws\Credentials\Credentials($account['key'], $account['secret']),
           'region' => $account['region'],
           'scheme' => 'http',
           'version' => '2016-04-01'
       ));
    startAllInstances($account);

}
