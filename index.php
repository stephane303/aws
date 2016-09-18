<?php
require 'vendor/autoload.php';


$allConfig = array();

// elesdyzer303
$config = array();
$config['key'] = 'AKIAJADEQPQTKAQSMOFQ';
$config['secret'] = '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';
$config['region'] = 'us-west-2';
$config['ami'] = 'ami-6ef72a0e';
//$allConfig[] = $config;
    
    
// artakan303
$config = array();
$config['key'] = 'AKIAJLK2MD6H7QFG2W2A';
$config['secret'] = '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';
$config['region'] = 'us-west-2';
$config['ami'] = 'ami-e7fa2787';
//$allConfig[] = $config;

// artakan304
$config = array();
$config['key'] = 'AKIAJR6DHDKXOR4QP75Q';
$config['secret'] = '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';
$config['region'] = 'us-west-2';
$config['ami'] = 'ami-8cf72aec';
//$allConfig[] = $config;

// artakan305
$config = array();
$config['key'] = 'AKIAJUGANS4IHZB5U6LA';
$config['secret'] = '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';
$config['region'] = 'us-west-2';
$config['ami'] = 'ami-18ed3078';
//$allConfig[] = $config;


// artakan306
$config = array();
$config['key'] = 'AKIAI5RH7JAMGGDWKQJQ';
$config['secret'] = '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';
$config['region'] = 'us-west-2';
$config['ami'] = 'ami-43f22f23';
//$allConfig[] = $config;

// artakan10
$config = array();
$config['key'] = 'AKIAJFGLKCXG75KYXVWQ';
$config['secret'] = '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';
$config['region'] = 'us-west-2';
$config['ami'] = 'ami-5bec313b';
//$allConfig[] = $config;

// artakan11 onze
$config = array();
$config['key'] = 'AKIAIGJGXOJRTTENFPAQ';
$config['secret'] = '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';
$config['region'] = 'us-west-2';
$config['ami'] = 'ami-c4ed30a4';
$allConfig[] = $config;


// artakan12
$config = array();
$config['key'] = 'AKIAJBOBRBNG6N47YTEA';
$config['secret'] = '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';
$config['region'] = 'us-west-2';
$config['ami'] = 'ami-75f22f15';
//$allConfig[] = $config;





  
function getInstances ($ec2Client) {
    $arrayOfInstances = array();
    $result = $ec2Client->DescribeInstances(array());

    $reservations = $result['Reservations'];
    $counter = 0;
    foreach ($reservations as $reservation) {
        $instances = $reservation['Instances'];
        foreach ($instances as $instance) {
            echo '---> State: ' . $instance['State']['Name'] . PHP_EOL;
            echo '---> Instance ID: ' . $instance['InstanceId'] . PHP_EOL;
            $arrayOfInstances[] = $instance['InstanceId'];
            echo '---> Image ID: ' . $instance['ImageId'] . PHP_EOL;
            echo '---> Private Dns Name: ' . $instance['PrivateDnsName'] . PHP_EOL;
            echo '---> Instance Type: ' . $instance['InstanceType'] . PHP_EOL;

            echo PHP_EOL;
            $counter++;
        }
    }
    echo $counter;
    return $arrayOfInstances;
}

echo '<pre>';
foreach($allConfig as $config){
    $credentials = new \Aws\Credentials\Credentials($config['key'], $config['secret']);
    $ec2Client = \Aws\Ec2\Ec2Client::factory(array(
        'credentials' => $credentials,
        'region' => $config['region'],
        'scheme' => 'http',
        'version' => 'latest'
    ));    
    


    $instances = getInstances($ec2Client);



    $res = $ec2Client->terminateInstances(array(
        'InstanceIds' => $instances // REQUIRED         
    ));
    
    $ec2Client->waitUntil('InstanceTerminated', ['InstanceIds' => $instances]);
    

    
    
    //sleep(10);

    $res =$ec2Client->runInstances(array(
        'ImageId' => $config['ami'], // REQUIRED   
        'MaxCount' => 20, // REQUIRED
        'MinCount' => 20, // REQUIRED    
        'InstanceType' => 't2.micro'
    ));
    
    print_r($res);

}






