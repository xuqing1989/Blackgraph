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
use Zend\Console\Request as ConsoleRequest;
use RuntimeException;

class ConsoleController extends AbstractActionController
{
    protected $tableArray = array();

    protected $token = 'Authorization: Bearer 0f2fb0eb5ab384166febe5e243e2be6ea53c279220fcb0746f2fd79cde38091a';
    protected $host = 'https://api.wmcloud.com/data/v1';

    public function fetchFromApi($api,$params)
    {
        //$api = '/api/fundamental/getFdmtIS.json?ticker=600887&secID=&beginDate=20131231&endDate=20141231';

        $api .= '?';
        foreach($params as $key => $value) {
            $api .= $key.'='.$value.'&';
        }
        $ch = curl_init();
        $timeout = 15;
        curl_setopt ($ch, CURLOPT_HTTPHEADER, Array($this->token));
        curl_setopt ($ch, CURLOPT_URL, ($this->host).$api);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $api_contents = curl_exec($ch);
        curl_close($ch);
        return json_decode($api_contents);
    }

    public function inittableAction()
    {
        $request = $this->getRequest();

        // Make sure that we are running in a console and the user has not tricked our
        // application into running this action from a public web server.
        if (!$request instanceof ConsoleRequest) {
            throw new RuntimeException('You can only use this action from a console!');
        }
        $tableModel = $request->getParam('tableModel');
        $beginDate = $request->getParam('beginDate','20100101');
        $endDate = $request->getParam('endDate',date('Ymd'));

        $apiBase = $this->getTable($tableModel)->apiBase;

        $stockList = $this->getTable('ReportTable')->fetchAll()->toArray();
        foreach($stockList as $key => $stockValue) {
            $apiData = $this->fetchFromApi($apiBase,array('ticker'=>$stockValue['ticker'],'beginDate'=>$beginDate,'endDate'=>$endDate));
            $errorCounter = 0;
            while(!isset($apiData->data) && $errorCounter < 5) {
                $errorCounter ++;
                echo "Fail ".$errorCounter." retry...\r\n";
                $apiData = $this->fetchFromApi($apiBase,array('ticker'=>$stockValue['ticker'],'beginDate'=>$beginDate,'endDate'=>$endDate));
            }
            if($errorCounter == 5) {
                echo "abort! Last tikcer: ".$stockValue['ticker'];
                return;
            }
            $apiData = $apiData->data;
            foreach($apiData as $value) {
                $checkDupKey = array('ticker'=>$value->ticker,'endDate'=>$value->endDate,'reportType'=>$value->reportType);
                if(!count($this->getTable($tableModel)->fetchByKey($checkDupKey)->toArray())) {
                    $this->getTable($tableModel)->insertData((array)$value);
                }
            }
            echo $stockValue['ticker']." completed!\r\n";
        }
    }

    public function getTable($tableModelName)
    {
        if(!isset($this->tableArray[$tableModelName])) {
            $sm = $this->getServiceLocator();
            $this->tableArray[$tableModelName] = $sm -> get('Application\Model\\'.$tableModelName);
        }
        return $this->tableArray[$tableModelName];
    }
}
