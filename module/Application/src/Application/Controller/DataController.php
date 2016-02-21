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
        $this->viewModel = new ViewModel();
        $this->viewModel->setTerminal(true);
        return $this->viewModel;
    }

    public function reportAction()
    {
        $request = $this -> getRequest();
        $date = $request->getQuery('date');
        $industry = $request->getQuery('industry');
        $subindustry = $request->getQuery('subindustry');
        $flag = $request->getQuery('flag');
        $report_list = $this->getReportTable()->fetchReport($date,$industry,$subindustry,$flag)->toArray();
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
            if($sinaData[3] > $sinaData[2]){
                $report_list[$skey]['color'] = 'red';
                $rate = '&uarr;&nbsp;'.$rate;
            }
            else {
                $report_list[$skey]['color'] = 'green';
                $rate = '&darr;&nbsp;'.$rate;
            }
            $report_list[$skey]['price_rate'] = $rate;
            $report_list[$skey]['volumn'] = $sinaData[9]/10000000;
        }
        
        $this->viewModel = new ViewModel(array('report_list'=>$report_list));
        $this->viewModel->setTerminal(true);
        return $this->viewModel;
    }

    public function getReportTable()
    {
        if(!$this->reportTable) {
            $sm = $this->getServiceLocator();
            $this->reportTable = $sm -> get('Application\Model\ReportTable');
        }
        return $this->reportTable;
    }
}
