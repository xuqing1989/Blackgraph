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

class GraphController extends AbstractActionController
{
    protected $tableArray = array();

    public function indexAction()
    {
        $this->viewModel = new ViewModel();
        $this->viewModel->setTerminal(true);
        return $this->viewModel;
    }

    public function sankeyAction()
    {
        $request = $this -> getRequest();
        $divId = $request -> getPost('divId');
        $this->viewModel = new ViewModel();
        $this->viewModel->setVariables(array('divId' => $divId))
                        ->setTerminal(true);
        return $this->viewModel;
    }

    public function coststructureAction()
    {
        $request = $this -> getRequest();
        $divId = $request -> getPost('divId');
        $this->viewModel = new ViewModel();
        $this->viewModel->setVariables(array('divId' => $divId))
                        ->setTerminal(true);
        return $this->viewModel;
    }

    public function turnoverdaysAction()
    {
        $request = $this -> getRequest();
        $divId = $request -> getPost('divId');
        $this->viewModel = new ViewModel();
        $this->viewModel->setVariables(array('divId' => $divId))
                        ->setTerminal(true);
        return $this->viewModel;
    }

    public function noncurrentliabilityAction()
    {
        $request = $this -> getRequest();
        $divId = $request -> getPost('divId');
        $this->viewModel = new ViewModel();
        $this->viewModel->setVariables(array('divId' => $divId))
                        ->setTerminal(true);
        return $this->viewModel;
    }

    public function noncurrentassetsAction()
    {
        $request = $this -> getRequest();
        $divId = $request -> getPost('divId');
        $this->viewModel = new ViewModel();
        $this->viewModel->setVariables(array('divId' => $divId))
                        ->setTerminal(true);
        return $this->viewModel;
    }

    public function assetsliabilityrateAction()
    {
        $request = $this -> getRequest();
        $divId = $request -> getPost('divId');
        $apiData = json_decode($request -> getPost('apiData'));
        $xAris = array();
        $data1 = array();
        $data2 = array();
        foreach($apiData as $year => $season) {
            foreach($season as $key => $value) {
                $value = $value->data;
                array_push($xAris,substr($year,2,2).'Q'.$key);
                array_push($data1,round(($value->TLiab / $value->TAssets)*100,2));
                array_push($data2,round(($value->TNCL / $value->TAssets)*100,2));
            }
        }
        $this->viewModel = new ViewModel();
        $this->viewModel->setVariables(array('divId' => $divId,
                                             'xAris' => $xAris,
                                             'data1' => $data1,
                                             'data2' => $data2,
                                      ))
                        ->setTerminal(true);
        return $this->viewModel;
    }

    public function earnedprofitAction()
    {
        $request = $this -> getRequest();
        $divId = $request -> getPost('divId');
        $this->viewModel = new ViewModel();
        $this->viewModel->setVariables(array('divId' => $divId))
                        ->setTerminal(true);
        return $this->viewModel;
    }

    public function netmarginAction()
    {
        $request = $this -> getRequest();
        $divId = $request -> getPost('divId');
        $this->viewModel = new ViewModel();
        $this->viewModel->setVariables(array('divId' => $divId))
                        ->setTerminal(true);
        return $this->viewModel;
    }

    public function profitmarginAction()
    {
        $request = $this -> getRequest();
        $divId = $request -> getPost('divId');
        $apiData = json_decode($request -> getPost('apiData'));
        $xAris = array();
        $data1 = array();
        $data2 = array();
        $data3 = array();
        foreach($apiData as $year => $season) {
            foreach($season as $key => $value) {
                $value = $value->data;
                array_push($xAris,substr($year,2,2).'Q'.$key);
                array_push($data1,round(($value->NIncomeAttrP / $value->tRevenue)*100,2));
                array_push($data2,round(($value->operateProfit / $value->tRevenue)*100,2));
                array_push($data3,round((($value->tRevenue - $value->COGS) / $value->tRevenue)*100,2));
            }
        }
        $this->viewModel = new ViewModel();
        $this->viewModel->setVariables(array('divId' => $divId,
                                             'xAris' => $xAris,
                                             'data1' => $data1,
                                             'data2' => $data2,
                                             'data3' => $data3,
                                      ))
                        ->setTerminal(true);
        return $this->viewModel;
    }

    public function cashliabilityAction()
    {
        $request = $this -> getRequest();
        $divId = $request -> getPost('divId');
        $this->viewModel = new ViewModel();
        $this->viewModel->setVariables(array('divId' => $divId))
                        ->setTerminal(true);
        return $this->viewModel;
    }

    public function liquidityAction()
    {
        $request = $this -> getRequest();
        $divId = $request -> getPost('divId');
        $apiData = json_decode($request -> getPost('apiData'));
        $xAris = array();
        $data1 = array();
        $data2 = array();
        foreach($apiData as $year => $season) {
            foreach($season as $key => $value) {
                $value = $value->data;
                array_push($xAris,substr($year,2,2).'Q'.$key);
                array_push($data1,round(($value->TCA / $value->TAssets)*100,2));
                array_push($data2,round((($value->TCA - $value->inventories) / $value->TAssets)*100,2));
            }
        }
        $this->viewModel = new ViewModel();
        $this->viewModel->setVariables(array('divId' => $divId,
                                             'xAris' => $xAris,
                                             'data1' => $data1,
                                             'data2' => $data2,
                                      ))
                        ->setTerminal(true);
        return $this->viewModel;
    }

    public function currentassetsAction()
    {
        $request = $this -> getRequest();
        $divId = $request -> getPost('divId');
        $this->viewModel = new ViewModel();
        $this->viewModel->setVariables(array('divId' => $divId))
                        ->setTerminal(true);
        return $this->viewModel;
    }

    public function incomeAction()
    {
        $request = $this -> getRequest();
        $divId = $request -> getPost('divId');
        $this->viewModel = new ViewModel();
        $this->viewModel->setVariables(array('divId' => $divId))
                        ->setTerminal(true);
        return $this->viewModel;
    }

    public function assetsrateAction()
    {
        $request = $this -> getRequest();
        $divId = $request -> getPost('divId');
        $this->viewModel = new ViewModel();
        $this->viewModel->setVariables(array('divId' => $divId))
                        ->setTerminal(true);
        return $this->viewModel;
    }

    public function loandepositrateAction()
    {
        $request = $this -> getRequest();
        $divId = $request -> getPost('divId');
        $this->viewModel = new ViewModel();
        $this->viewModel->setVariables(array('divId' => $divId))
                        ->setTerminal(true);
        return $this->viewModel;
    }

    public function cashflowrateAction()
    {
        $request = $this -> getRequest();
        $divId = $request -> getPost('divId');
        $this->viewModel = new ViewModel();
        $this->viewModel->setVariables(array('divId' => $divId))
                        ->setTerminal(true);
        return $this->viewModel;
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
