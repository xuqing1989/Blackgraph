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
        $result = array();
        for($start=$startDate; $start <= $endDate; $start += 24*3600) {
            $reportCount = $this->getReportTable() -> reportCountByDate(date("Y-m-d",$start));
            if($reportCount) {
                $result[$start] = $reportCount;
            }
        }
        echo json_encode($result);
        $this->viewModel = new ViewModel;
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
