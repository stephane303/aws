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
        echo($account['name'].' '.$account['region'].' termination'.PHP_EOL);
    }    
}


function startAllInstances (&$account,$howmany){
    try {
        $account['Exception'] = false;
        echo($account['name']." starting ".$account['region']." $howmany".PHP_EOL);
        $instances = getNonTerminatedInstances($account['client']);
        if (!empty($instances)) {
            echo $account['region'].' Waiting for termination of '.$account['name'].PHP_EOL;
            $account['client']->waitUntil('InstanceTerminated', ['InstanceIds' => $instances]);
            echo ($account['region'].' '.$account['name'].' '.count($instances).' instance terminated'.PHP_EOL);
        }

        $res =$account['client']->runInstances(array(
            'ImageId' => $account['ami'], 
            'MaxCount' => $howmany, 
            'MinCount' => $howmany, 
            'InstanceType' => 't2.micro'
        ));
        echo($account['region'].' '.$account['name'].' started'.PHP_EOL);
    }
    catch (Exception $ex) {
        echo $ex->getMessage().PHP_EOL;
	echo 'Exception:Try again later....'.PHP_EOL;
        $account['Exception'] = true;
    }    
}

