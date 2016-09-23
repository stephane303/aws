<?php
require 'vendor/autoload.php';
require 'config.php';
require 'lib.php';

$regions = ['12','12'];
foreach
$origClient = 
            \Aws\Iam\IamClient::factory(array(
               'credentials' =>  new \Aws\Credentials\Credentials($origAccount['key'], $origAccount['secret']),
               'region' => $region,
               'scheme' => 'https',
               'version' => '2010-05-08'
           ));

foreach($allAccounts as $account){
        $account['client'] =
            \Aws\Iam\IamClient::factory(array(
               'credentials' =>  new \Aws\Credentials\Credentials($account['key'], $account['secret']),
               'region' => $account['region'],
               'scheme' => 'https',
               'version' => '2010-05-08'
           ));


         
         $res=$account['client']->getUser();
         echo $account['name'].':'.$res['User']['UserId'].PHP_EOL;
}
