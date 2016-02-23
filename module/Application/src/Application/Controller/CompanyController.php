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
        $layout1 = '[["采掘","电气设备","电子","国防军工","传媒","房地产","纺织服装"],
                     ["有色金属","钢铁","化工","机械设备","计算机","家用电器","汽车"],
                     [null,"建筑材料","建筑装饰","农林牧渔","商业贸易","食品饮料","休闲服务"],
                     [null,"轻工制造",null,null,"医药生物",null,null]]';
        $layout1 = json_decode($layout1);
        for($i=0;$i<count($layout1);$i++) {
            for($j=0;$j<count($layout1[$i]);$j++){
                if($layout1[$i][$j]) {
                    $layout1[$i][$j] = $this->getIndustryTable()->fetchByName($layout1[$i][$j])->toArray();
                    $layout1[$i][$j] = $layout1[$i][$j][0];
                    $layout1[$i][$j]['sub'] = $this->getSubindustryTable()->fetchByIndustryid($layout1[$i][$j]['id'])->toArray();
                }
            }
        }

        $layout2 = '["银行","非银金融","公用事业","交通运输","通信","综合"]';
        $layout2 = json_decode($layout2);
        for($i=0;$i<count($layout2);$i++) {
            $layout2[$i] = $this->getIndustryTable()->fetchByName($layout2[$i])->toArray();
            $layout2[$i] = $layout2[$i][0];
            $layout2[$i]['sub'] = $this->getSubindustryTable()->fetchByIndustryid($layout2[$i]['id'])->toArray();
        }
        return new ViewModel(array('layout1'=>$layout1,'layout2'=>$layout2));
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
