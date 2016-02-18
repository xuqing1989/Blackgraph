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

class IndexController extends AbstractActionController
{
    protected $reportTable;
    protected $industryTable;
    protected $subindustryTable;

    public function indexAction()
    {
        $industry = $this->getIndustryTable()->fetchAll()->toArray();
        foreach($industry as $key => $value){
            $industry[$key]['sub'] = $this->getSubindustryTable()->fetchByIndustryid($value['id'])->toArray();
        }
        return new ViewModel(array(
            'industry_data' => $industry,
        ));
    }

    public function testAction()
    {
        $host = "https://api.wmcloud.com/data/v1";
        $api = "/api/fundamental/getFdmtBS.json";
        $ch = curl_init();
        $timeout = 5;
        curl_setopt ($ch, CURLOPT_HTTPHEADER, Array("Authorization: Bearer 0f2fb0eb5ab384166febe5e243e2be6ea53c279220fcb0746f2fd79cde38091a"));
        curl_setopt ($ch, CURLOPT_URL, 'https://api.wmcloud.com/data/v1/api/fundamental/getFdmtBS.json?ticker=600887&beginDate=20141231&endDate=20151231');
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $file_contents = curl_exec($ch);
        curl_close($ch);
        echo $file_contents;
        $file_contents = json_decode($file_contents);
        $this->viewModel = new ViewModel;
        $this->viewModel->setTerminal(true);
        return $this->viewModel;
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
            $industry_query = $this->getIndustryTable()->fetchByName($lineArray[2])->toArray();
            if(count($industry_query) == 0){
                $this->getIndustryTable()->addIndustry(array('name'=>$lineArray[2]));
                $industry_id = $this->getIndustryTable()->lastId();
            }
            else {
                $industry_id = $industry_query[0]['id'];
            }
            $subindustry_query = $this->getSubindustryTable()->fetchByName($lineArray[3])->toArray();
            if(count($subindustry_query) == 0) {
                $this->getSubindustryTable()->addSubindustry(array('name'=>$lineArray[3],'industry_id'=>$industry_id));
                $subindustry_id = $this->getSubindustryTable()->lastId();
            }
            else {
                $subindustry_id = $subindustry_query[0]['id'];
            }
            $sql_report = array(
                              'ticker' => $tickerObj[0],
                              'house' => $tickerObj[1],
                              'name' => $lineArray[1],
                              'industry_id' => $industry_id,
                              'subindustry_id' => $subindustry_id,
                              'flag' => $flag,
                              'market_cup' => $lineArray[5],
                              'ttm' => $lineArray[6],
                              'report_year' => substr($lineArray[7],0,2),
                              'report_type' => substr($lineArray[7],2),
                              'release_date' => $lineArray[8],
                          );
            $this -> getReportTable() -> addReport($sql_report);
        }
        $this -> getReportTable() -> updateReport(array('ticker'=>'000001'),'name = ?','平安银行');
        fclose($handle);
        $this->viewModel = new ViewModel();
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

    public function getIndustryTable()
    {
        if(!$this->industryTable) {
            $sm = $this->getServiceLocator();
            $this->industryTable = $sm -> get('Application\Model\IndustryTable');
        }
        return $this->industryTable;
    }

    public function getSubindustryTable()
    {
        if(!$this->subindustryTable) {
            $sm = $this->getServiceLocator();
            $this->subindustryTable = $sm -> get('Application\Model\SubindustryTable');
        }
        return $this->subindustryTable;
    }
}
