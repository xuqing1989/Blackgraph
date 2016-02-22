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

class CompanyController extends AbstractActionController
{
    protected $industryTable;
    protected $subindustryTable;

    public function indexAction()
    {
        $layout = '[["采掘","电气设备","电子","国防军工","传媒","房地产","纺织服装"],
                     ["有色金属","钢铁","化工","机械设备","计算机","家用电器","汽车"],
                     [null,"建筑材料","建筑装饰","农林牧渔","商业贸易","食品饮料","休闲服务"],
                     [null,"轻工制造",null,null,"医药生物",null,null]]';
        $layout = json_decode($layout);
        for($i=0;$i<count($layout);$i++) {
            for($j=0;$j<count($layout[$i]);$j++){
                if($layout[$i][$j]) {
                    $layout[$i][$j] = $this->getIndustryTable()->fetchByName($layout[$i][$j])->toArray();
                    $layout[$i][$j] = $layout[$i][$j][0];
                    $layout[$i][$j]['sub'] = $this->getSubindustryTable()->fetchByIndustryid($layout[$i][$j]['id'])->toArray();
                }
            }
        }
        return new ViewModel(array('layout'=>$layout));
    }

    public function detailAction()
    {
        return new ViewModel();
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
