<?php
echo '===============START================='.PHP_EOL;
echo date("F j, Y, g:i a").PHP_EOL;
if ( php_sapi_name() !== 'cli') {
    echo 'Only cli';die;
}
require 'vendor/autoload.php';
require 'config.php';
require 'lib.php';

foreach($allAccounts as $account){

    $pid = pcntl_fork();
    if ($pid === -1) {
         die('could not fork');
    } else if ($pid === 0) {
        // We are the child process. Pass a chunk of items to process.
        $account['client'] = 
            \Aws\Ec2\Ec2Client::factory(array(
               'credentials' =>  new \Aws\Credentials\Credentials($account['key'], $account['secret']),
               'region' => $account['region'],
               'scheme' => 'http',
               'version' => '2016-04-01'
           ));
        terminateAllInstances($account);
        exit(0);
        startAllInstances($account);
        
        for($i=0; $i<=4; $i++){
            if ($account['Exception']) {
                // On dort un peu et on recommence une fois
                sleep($i*30+30);
                terminateAllInstances($account );  
                startAllInstances($account); 
                if (!$account['Exception']) break;                
            }
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
