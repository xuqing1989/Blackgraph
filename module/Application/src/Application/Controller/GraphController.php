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
    public function indexAction()
    {
        $this->viewModel = new ViewModel();
        $this->viewModel->setTerminal(true);
        return $this->viewModel;
    }

    public function coststructureAction()
    {
        $request = $this -> getRequest();
        $divId = $request -> getQuery('divId');
        $this->viewModel = new ViewModel();
        $this->viewModel->setVariables(array('divId' => $divId))
                        ->setTerminal(true);
        return $this->viewModel;
    }

    public function inventoryturnoverAction()
    {
        $request = $this -> getRequest();
        $divId = $request -> getQuery('divId');
        $this->viewModel = new ViewModel();
        $this->viewModel->setVariables(array('divId' => $divId))
                        ->setTerminal(true);
        return $this->viewModel;
    }

    public function noncurrentliabilityAction()
    {
        $request = $this -> getRequest();
        $divId = $request -> getQuery('divId');
        $this->viewModel = new ViewModel();
        $this->viewModel->setVariables(array('divId' => $divId))
                        ->setTerminal(true);
        return $this->viewModel;
    }

    public function noncurrentassetsAction()
    {
        $request = $this -> getRequest();
        $divId = $request -> getQuery('divId');
        $this->viewModel = new ViewModel();
        $this->viewModel->setVariables(array('divId' => $divId))
                        ->setTerminal(true);
        return $this->viewModel;
    }

    public function assetsliabilityrateAction()
    {
        $request = $this -> getRequest();
        $divId = $request -> getQuery('divId');
        $this->viewModel = new ViewModel();
        $this->viewModel->setVariables(array('divId' => $divId))
                        ->setTerminal(true);
        return $this->viewModel;
    }

    public function earnedprofitAction()
    {
        $request = $this -> getRequest();
        $divId = $request -> getQuery('divId');
        $this->viewModel = new ViewModel();
        $this->viewModel->setVariables(array('divId' => $divId))
                        ->setTerminal(true);
        return $this->viewModel;
    }

    public function netmarginAction()
    {
        $request = $this -> getRequest();
        $divId = $request -> getQuery('divId');
        $this->viewModel = new ViewModel();
        $this->viewModel->setVariables(array('divId' => $divId))
                        ->setTerminal(true);
        return $this->viewModel;
    }

    public function profitmarginAction()
    {
        $request = $this -> getRequest();
        $divId = $request -> getQuery('divId');
        $this->viewModel = new ViewModel();
        $this->viewModel->setVariables(array('divId' => $divId))
                        ->setTerminal(true);
        return $this->viewModel;
    }

    public function cashliabilityAction()
    {
        $request = $this -> getRequest();
        $divId = $request -> getQuery('divId');
        $this->viewModel = new ViewModel();
        $this->viewModel->setVariables(array('divId' => $divId))
                        ->setTerminal(true);
        return $this->viewModel;
    }

    public function liquidityAction()
    {
        $request = $this -> getRequest();
        $divId = $request -> getQuery('divId');
        $this->viewModel = new ViewModel();
        $this->viewModel->setVariables(array('divId' => $divId))
                        ->setTerminal(true);
        return $this->viewModel;
    }

    public function currentassetsAction()
    {
        $request = $this -> getRequest();
        $divId = $request -> getQuery('divId');
        $this->viewModel = new ViewModel();
        $this->viewModel->setVariables(array('divId' => $divId))
                        ->setTerminal(true);
        return $this->viewModel;
    }

    public function incomeAction()
    {
        $request = $this -> getRequest();
        $divId = $request -> getQuery('divId');
        $this->viewModel = new ViewModel();
        $this->viewModel->setVariables(array('divId' => $divId))
                        ->setTerminal(true);
        return $this->viewModel;
    }

    public function receivableturnoverAction()
    {
        $request = $this -> getRequest();
        $divId = $request -> getQuery('divId');
        $this->viewModel = new ViewModel();
        $this->viewModel->setVariables(array('divId' => $divId))
                        ->setTerminal(true);
        return $this->viewModel;
    }

    public function assetsrateAction()
    {
        $request = $this -> getRequest();
        $divId = $request -> getQuery('divId');
        $this->viewModel = new ViewModel();
        $this->viewModel->setVariables(array('divId' => $divId))
                        ->setTerminal(true);
        return $this->viewModel;
    }
}
