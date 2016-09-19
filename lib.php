<?php
function getNonTerminatedInstances ($ec2Client) {
    $arrayOfInstances = array();
    $reservations = $ec2Client->getIterator('DescribeInstances', array(
    'Filters' => [
        [
            'Name' => 'instance-state-name',
            'Values' => ['pending', 'running', 'shutting-down', 'stopping' ,'stopped']
        ]]       
    ));
    foreach($reservations as $reservation){
        $instances = $reservation['Instances'];
        foreach($instances as $instance){
            $arrayOfInstances[]=$instance['InstanceId'];
        }
    }

    return $arrayOfInstances;
}

function terminateAllInstances ($account, $dryRun = false){
    $instances = getNonTerminatedInstances($account['client']);

    if (!empty($instances)){
        $res = $account['client']->terminateInstances(array(
            'InstanceIds' => $instances,
            'DryRun' => $dryRun
        ));
        echo($account['name'].' termination'.PHP_EOL);
    }    
}


function startAllInstances (&$account, $dryRun = false){
    try {
        $account['Exception'] = false;
        echo($account['name'].' starting'.PHP_EOL);
        $instances = getNonTerminatedInstances($account['client']);
        if (!empty($instances)) {
            echo 'Waiting for termination of '.$account['name'].PHP_EOL;
            $account['client']->waitUntil('InstanceTerminated', ['InstanceIds' => $instances]);
            echo (count($instances).' instance terminated'.PHP_EOL);
        }

        $res =$account['client']->runInstances(array(
            'ImageId' => $account['ami'], 
            'MaxCount' => 20, 
            'MinCount' => 20, 
            'InstanceType' => 't2.micro',
            'DryRun' => $dryRun
        ));
        echo($account['name'].' started'.PHP_EOL);
    }
    catch (Exception $ex) {
        echo $ex->getMessage().PHP_EOL;
	echo 'Exception:Try again later....'.PHP_EOL;
        $account['Exception'] = true;
    }    
}

