<?php
require 'vendor/autoload.php';
require 'config.php';
require 'lib.php';


foreach($allAccounts as $account){
        $account['client'] =
            \Aws\Iam\IamClient::factory(array(
               'credentials' =>  new \Aws\Credentials\Credentials($account['key'], $account['secret']),
               'region' => $account['region'],
               'scheme' => 'https',
               'version' => '2010-05-08'
           ));


         
         $res=$account['client']->getUser();
         echo $res['User']['UserId'].PHP_EOL;
}
