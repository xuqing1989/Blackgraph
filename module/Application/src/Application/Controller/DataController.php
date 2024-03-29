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

class DataController extends AbstractActionController
{
    protected $tableArray = array();

    public function calendarAction()
    {
        $request = $this -> getRequest();
        $startDate = $request->getQuery('startDate');
        $endDate = $request->getQuery('endDate');
        $industry = $request->getQuery('industry');
        $subindustry = $request->getQuery('subindustry');
        $flag = $request->getQuery('flag');
        $result = array();
        //fix the last day lost bug
        $endDate += 24*3600;
        for($start=$startDate; $start <= $endDate; $start += 24*3600) {
            $reportCount = $this->getReportTable() -> fetchReport(date("Y-m-d",$start),$industry,$subindustry,$flag)->count();
            if($reportCount) {
                $result[$start] = $reportCount;
            }
        }
        echo json_encode($result);
        return $this->response;
    }

    public function reportAction()
    {
        $request = $this -> getRequest();
        $date = $request->getQuery('date');
        $industry = $request->getQuery('industry');
        $subindustry = $request->getQuery('subindustry');
        $orderKey = $request->getQuery('order1');
        $orderValue = $request->getQuery('order2')?'ASC':'DESC';
        $orderString = $orderKey.' '.$orderValue;
        $flag = $request->getQuery('flag');
        $page = $request->getQuery('page');
        //config
        $countPerPage = 50;

        $report_list = $this->getReportTable()->fetchReport($date,$industry,$subindustry,$flag,$orderString)->toArray();
        $total_num = count($report_list);
        if($page != 'all') {
            $report_list = array_splice($report_list,($page-1)*$countPerPage,50);
        }
        foreach($report_list as $key=>$value) {
            $tickerPara .= strtolower($value['house']).$value['ticker'].',';
        }
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => "http://hq.sinajs.cn/list=".$tickerPara,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        $sinaApi = explode(';',$response);
        array_pop($sinaApi);
        foreach($sinaApi as $skey=>$svalue){
            $sinaPredata = explode('"',$svalue);
            $sinaData = explode(',',$sinaPredata[1]);
            $report_list[$skey]['price_now'] = $sinaData[3];
            $rate = abs((($sinaData[3]-$sinaData[2])/$sinaData[2])*100);
            $rate = sprintf('%.2f',$rate).'%';
            if($sinaData[3] == '0.00' || $sinaData[9] == '0.00') {
                $report_list[$skey]['color'] = '';
                $rate = '停牌';
                $report_list[$skey]['price_now'] = $sinaData[2];
            }
            else {
                if($sinaData[3] > $sinaData[2]){
                    $report_list[$skey]['color'] = 'red';
                    $rate = '+'.$rate;
                }
                else {
                    $report_list[$skey]['color'] = 'green';
                    $rate = '-'.$rate;
                }
            }
            $report_list[$skey]['price_rate'] = $rate;
            $report_list[$skey]['volumn'] = $sinaData[9]/10000000;
        }
        
        $this->viewModel = new ViewModel(array('report_list'=>$report_list,
                                               'page' => (int)$page,
                                               'total_num' => $total_num,
                                               'countPerPage' => $countPerPage,
                                               'orderString' => $orderString,
                                              ));
        $this->viewModel->setTerminal(true);
        return $this->viewModel;
    }

    public function searchAction() {
        $request = $this -> getRequest();
        $text = $request->getQuery('text');
        $searchResult = $this->getReportTable()->searchReport($text)->toArray();
        echo json_encode($searchResult);
        return $this->response;
    }

    public function sinaAction(){
        $request = $this -> getRequest();
        $ticker = $request->getQuery('ticker');
        $house = $request->getQuery('house');
        $company_data = array();
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://hq.sinajs.cn/list=".strtolower($house).$ticker,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        $sinaApi = explode(';',$response);
        array_pop($sinaApi);
        $sinaPredata = explode('"',$sinaApi[0]);
        $sinaData = explode(',',$sinaPredata[1]);
        $company_data['price_now'] = sprintf('%.2f',$sinaData[3]);
        $company_data['price_diff'] = sprintf('%.2f',abs($sinaData[3]-$sinaData[2]));
        $rate = abs((($sinaData[3]-$sinaData[2])/$sinaData[2])*100);
        $rate = sprintf('%.2f',$rate).'%';
        if($sinaData[3] == '0.00' || $sinaData[9] == '0.00') {
            $rate = '停牌';
            $company_data['price_diff'] = '停牌';
        }
        else {
            if($sinaData[3] > $sinaData[2]){
                $rate = '+'.$rate;
                $company_data['price_diff'] = '+' . $company_data['price_diff'];
                $company_data['isP'] = true;
            }
            else {
                $rate = '-'.$rate;
                $company_data['price_diff'] = '-' . $company_data['price_diff'];
                $company_data['isP'] = false;
            }
        }
        $company_data['price_rate'] = $rate;
        $company_data['volumn'] = round($sinaData[9]/10000000,2).'万股';
        $company_data['count'] = round($sinaData[8]/10000,2).'亿';
        
        echo json_encode($company_data);
        return $this->response;
    }

    public function getReportTable()
    {
        if(!$this->reportTable) {
            $sm = $this->getServiceLocator();
            $this->reportTable = $sm -> get('Application\Model\ReportTable');
        }
        return $this->reportTable;
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
