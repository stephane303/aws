<?php
echo '===============START================='.PHP_EOL;
echo date("F j, Y, g:i a").PHP_EOL;
if ( php_sapi_name() !== 'cli') {
    echo 'Only cli';die;
}
require 'vendor/autoload.php';
require 'config.php';
require 'lib2.php';

$regions =
array (
"us-east-1" => 'ami-3d146f2a',
"us-west-1" => 'ami-0e2b656e',
"us-west-2" => 'ami-a134e8c1',
"eu-west-1" => 'ami-11f98362',
"ap-northeast-1" => 'ami-56ff2d37',
"ap-northeast-2" => 'ami-de4d98b0',
"ap-southeast-1" => 'ami-ea7fda89',
"ap-southeast-2" => 'ami-53e9d930',
"ap-south-1" => 'ami-efcdb880',
"sa-east-1" => 'ami-915fccfd',
"eu-central-1" => 'ami-1ebb4771'
);

$maxinstancesA =
array (
"us-east-1" => 20,
"us-west-1" => 10,
"us-west-2" => 20,
"eu-west-1" => 10,
"ap-northeast-1" => 10,
"ap-northeast-2" => 10,
"ap-southeast-1" => 10,
"ap-southeast-2" => 5,
"ap-south-1" => 5,
"sa-east-1" => 5,
"eu-central-1" => 5
);

foreach($allAccounts as $account){

    $pid = pcntl_fork();
    //$pid = 0;
    if ($pid === -1) {
         die('could not fork');
    } else if ($pid === 0) {
        // We are the child process. Pass a chunk of items to process.
        foreach($regions as $region => $ami){
            $pid1 = pcntl_fork();
            if ($pid1 === -1) {
                die ('could not fork');
            } else if ($pid1 === 0) {
                
            // We are the child process
                echo $region.PHP_EOL;
                $account['region'] = $region;
                $account['client'] = 
                    \Aws\Ec2\Ec2Client::factory(array(
                       'credentials' =>  new \Aws\Credentials\Credentials($account['key'], $account['secret']),
                       'region' => $region,
                       'scheme' => 'http',
                       'version' => '2016-04-01'
                   ));
                //$res = $account['client']->describeAccountAttributes();
                $maxInstances = $maxinstancesA[$region];
                terminateAllInstances($account);
		exit(0);
                $account['ami'] = $ami;

                startAllInstances($account, $maxInstances);

                for($i=0; $i<=4; $i++){
                    if ($account['Exception']) {
                        // On dort un peu et on recommence une fois
                        sleep($i*30+30);
                        terminateAllInstances($account );  
                        startAllInstances($account, $maxInstances); 
                        if (!$account['Exception']) break;                
                    }
                }
                exit(0);
            } else {
                $children1[] = $pid1;
            }
            
        }
        foreach ($children1 as $pid1){
            pcntl_waitpid($pid1,$status);
        }
        exit(0);
        
    } else {
        // We are the parent.
        $children[] = $pid;
    } 
    
}

// Wait for children to finish.
 foreach ($children as $pid) {
     // We are still the parent.
     pcntl_waitpid($pid, $status);
 }
 
echo date("F j, Y, g:i a").PHP_EOL;
echo '===============END==================='.PHP_EOL;
