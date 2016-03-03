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
    protected $reportTable;
    protected $industryTable;
    protected $subindustryTable;
    protected $tableArray;

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

    public function test2Action() {
        $this->getIndustryTable()->addIndustry(array('name'=>'test'));
        $temp = $this->getIndustryTable()->lastId();
        print_r($temp);
        $this->viewModel = new ViewModel();
        $this->viewModel->setTerminal(true);
        return $this->viewModel;
    }

    public function test3Action(){
        echo 'test console';
        $this->viewModel = new ViewModel();
        $this->viewModel->setTerminal(true);
        return $this->viewModel;
    }

    public function testAction()
    {
        $host = "https://api.wmcloud.com/data/v1";
        $request = $this -> getRequest();
        $api = $request->getQuery('api');
        $api = '/api/fundamental/getFdmtIS.json?ticker=600887&secID=&beginDate=20131231&endDate=20141231';
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

    public function getTable($tableModelName)
    {
        if(!$this->tableArray[$tableModelName]) {
            $sm = $this->getServiceLocator();
            $this->tableArray[$tableModelName] = $sm -> get('Application\Model\\'.$tableModelName);
        }
        return $this->tableArray[$tableModelName];
    }
}
