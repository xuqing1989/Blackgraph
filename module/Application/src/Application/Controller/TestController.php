<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class TestController extends AbstractActionController
{
    public function indexAction()
    {
        return new ViewModel();
    }

    public function graphAction()
    {
        return new ViewModel();
    }

    public function test1Action(){
        return new ViewModel();
    }

    public function test2Action(){
        $filename = "/var/www/html/test_data/report_sample.csv";
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
        $this->viewModel = new ViewModel();
        $this->viewModel->setTerminal(true);
        return $this->viewModel;
    }

    public function test3Action(){
        $curl = curl_init();
        
        curl_setopt_array($curl, array(
          CURLOPT_URL => "http://a.apix.cn/apixmoney/stockdata/stock?stockid=sz002230",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_HTTPHEADER => array(
            "accept: application/json",
            "apix-key: a613753089814fed586fa777bf97b179",
            "content-type: application/json"
          ),
        ));
        
        $response = curl_exec($curl);
        $err = curl_error($curl);
        
        curl_close($curl);
        echo $response;
        $this->viewModel = new ViewModel();
        $this->viewModel->setTerminal(true);
        return $this->viewModel;
    }

    public function testAction()
    {
        $host = "https://api.wmcloud.com/data/v1";
        $request = $this -> getRequest();
        $api = $request->getQuery('api');
        $ch = curl_init();
        $timeout = 15;
        //echo $host.$api."<br/><br/><br/>";
        curl_setopt ($ch, CURLOPT_HTTPHEADER, Array("Authorization: Bearer 0f2fb0eb5ab384166febe5e243e2be6ea53c279220fcb0746f2fd79cde38091a"));
        curl_setopt ($ch, CURLOPT_URL, $host.$api);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $file_contents = curl_exec($ch);
        curl_close($ch);
        echo $file_contents;
        $file_contents = json_decode($file_contents);
        $this->viewModel = new ViewModel();
        $this->viewModel->setTerminal(true);
        return $this->viewModel;
    }

}
