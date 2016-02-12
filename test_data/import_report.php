<?php

$filename = "./report_sample.csv";
$handle = fopen($filename,"r");
while(!feof($handle)) {
    $line = fgets($handle,2048);
    if(empty($line))break;
    $lineArray = explode(',',$line);
    $tickerObj = explode('.',$lineArray[0]);
    $flag = 0;
    if($lineArray[4] == '是') {
        $flag = 1;
    }
    $sql_report = array(
                      'ticker' => $tickerObj[0],
                      'house' => $tickerObj[1],
                      'industry_id' => '',
                      'subindustry_id' => '',
                      'flag' => $flag,
                      'market_cup' => $lineArray[5],
                      'ttm' => $lineArray[6],
                      'report_year' => substr($lineArray[7],0,1),
                      'report_type' => substr($lineArray[7],2),
                      'release_date' => $lineArray[8],
                  );
}
fclose($handle);
