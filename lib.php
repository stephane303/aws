<?php
function getInstances ($ec2Client) {
    $arrayOfInstances = array();
    $reservations = $ec2Client->getIterator('DescribeInstances', array());
    foreach($reservations as $reservation){
        $instances = $reservation['Instances'];
        foreach($instances as $instance){
            $arrayOfInstances[]=$instance['InstanceId'];
        }
    }

    return $arrayOfInstances;

}

function terminateAllInstances ($account){
    $instances = getInstances($account['client']);

    if (!empty($instances)){
        $res = $account['client']->terminateInstances(array(
            'InstanceIds' => $instances 
        ));
        echo($account['name'].' termination'.PHP_EOL);
    }    
}


function startAllInstances (&$account){
    try {
        echo($account['name'].' starting'.PHP_EOL);
        $instances = getInstances($account['client']);
        if (!empty($instances)) {
            echo 'Waiting for termination of '.$account['name'].PHP_EOL;
            $account['client']->waitUntil('InstanceTerminated', ['InstanceIds' => $instances]);
            echo (count($instances).' instance terminated'.PHP_EOL);
        }

        $res =$account['client']->runInstances(array(
            'ImageId' => $account['ami'], 
            'MaxCount' => 20, 
            'MinCount' => 20, 
            'InstanceType' => 't2.micro'
        ));
        echo($account['name'].' started'.PHP_EOL);
    }
    catch (Exception $ex) {
        echo $ex->getMessage().PHP_EOL;
	echo 'Exception:Try again later....'.PHP_EOL;
        $account['Exception'] = true;
    }    
}

