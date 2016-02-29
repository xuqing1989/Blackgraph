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

class ApiController extends AbstractActionController
{
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

    public function sortResult($rawData,$seasonCount) {
        $rawData = $rawData->data;
        $endDate = '0000-00-00';
        foreach($rawData as $key => $value) {
            if($value->endDate > $endDate) {
                $endDate = $value->endDate;
            }
        }
        $dateToSeason = array('3-31'=>1,'06-30'=>2,'09-30'=>3,'12-31'=>4);
        $end = array('year'=>substr($endDate,0,4),'season'=>$dateToSeason[substr($endDate,5)]);
        $counter = $seasonCount;
        $cur = $end;
        $result = array();
        while($counter > 0){
            if($cur['season']==0){
                $cur['year'] --;
                $cur['season'] = 4;
            }
            $result[$cur['year']][$cur['season']] = array();
            $cur['season'] --;
            $counter --;
        }
        $typeToSeason = array('Q1'=>1,'S1'=>2,'Q3'=>3,'A'=>4);
        foreach($rawData as $key => $value){
            $theYear = substr($value->endDate,0,4);
            if(isset($typeToSeason[$value->reportType])) {
                $theSeason = $typeToSeason[$value->reportType];
                if(isset($result[$theYear][$theSeason])) {
                    if(!isset($result[$theYear][$theSeason]['data'])){
                        $result[$theYear][$theSeason]['data'] = $value;
                    }
                }
            }
        }
        $result = array_reverse($result,true);
        foreach($result as $key=>$value) {
            $result[$key] = array_reverse($value,true);
        }
        return $result;
    }

    public function indexAction()
    {
        $this->viewModel = new ViewModel();
        $this->viewModel->setTerminal(true);
        return $this->viewModel;
    }

    public function fdmtisAction()
    {
        $request = $this -> getRequest();
        $endDate = $request->getQuery('endDate');
        $ticker = $request->getQuery('ticker');
        $beginDate = '20100101';
        $dataCount = 20;
        $apiBase = '/api/fundamental/getFdmtIS.json?';
        $apiData = $this->fetchFromApi($apiBase,array('ticker'=>$ticker,'beginDate'=>$beginDate,'endDate'=>$endDate));
        $apiData = $this->sortResult($apiData,$dataCount);
        echo json_encode($apiData);
        $this->viewModel = new ViewModel();
        $this->viewModel->setTerminal(true);
        return $this->viewModel;
    }

}
