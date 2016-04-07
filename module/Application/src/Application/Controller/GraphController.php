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
    const ONEMILLION = 1000000;
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
        $graphType = $request -> getPost('graphType');
        $ticker = $request -> getPost('ticker');

        $rawData = $this->getTable('TlfdmtisTable')->fetchForChartJoinBs($ticker)->toArray();
        $sortData = $this->sortData($rawData);
        $xAris = array();
        $data1 = array();//存货周转天数
        $data2 = array();//应收周转天数
        $hideData1 = array();//平均存货
        $hideData2 = array();//日均营业成本
        $hideData3 = array();//平均应收账款
        $hideData4 = array();//日均营业收入
        $typeToSeason = array(
            'Q1' => 'Q1',
            'S1' => 'Q2',
            'Q3' => 'Q3',
            'A' => 'Q4',
        );
        $preSeason = array(
            'Q1' => 'A',
            'S1' => 'Q1',
            'Q3' => 'S1',
            'A' => 'Q3',
        );
        $seasonOrder = ['A','Q3','S1','Q1'];
        if($graphType == 'season') {
            $counter = 0;
            $cq3Value = array();
            foreach($sortData as $year => $seasons) {
                $stopSign = false;
                foreach($seasonOrder as $season) {
                    if(isset($seasons[$season])) {
                        $value = $seasons[$season];
                        $calValue = array();
                        $calValue['endDate'] = $value['endDate'];
                        $calValue['reportType'] = $value['reportType'];
                        if($season == 'S1'){
                            //S1: S1-Q1
                            $calValue['COGS'] = $value['COGS'] - $seasons['Q1']['COGS'];
                            $calValue['tRevenue'] = $value['tRevenue'] - $seasons['Q1']['tRevenue'];
                        }
                        else if($season == 'A'){
                            //A: A-CQ3
                            $calValue['COGS'] = $value['COGS'] - $seasons['CQ3']['COGS'];
                            $calValue['tRevenue'] = $value['tRevenue'] - $seasons['CQ3']['tRevenue'];
                        }
                        else {
                            $calValue['COGS'] = $value['COGS'];
                            $calValue['tRevenue'] = $value['tRevenue'];
                        }
                        if($season != 'Q1') {
                            $calValue['inventories'] = ($value['inventories']+$seasons[$preSeason[$value['reportType']]]['inventories'])/2;
                            $calValue['AR'] = ($value['AR']+$seasons[$preSeason[$value['reportType']]]['AR'])/2;
                        }
                        else {
                            $preYear = $year - 1;
                            $calValue['inventories'] = ($value['inventories']+$sortData[$preYear]['A']['inventories'])/2;
                            $calValue['AR'] = ($value['AR']+$sortData[$preYear]['A']['AR'])/2;
                        }
                        array_push($xAris,substr($calValue['endDate'],2,2).$typeToSeason[$calValue['reportType']]);
                        array_push($data1,sprintf('%.1f',$calValue['inventories']/($calValue['COGS']/90)));
                        array_push($hideData1,sprintf('%.1f',$calValue['inventories']/self::ONEMILLION));
                        array_push($hideData2,sprintf('%.1f',($calValue['COGS']/90)/self::ONEMILLION));
                        array_push($data2,sprintf('%.1f',$calValue['AR']/($calValue['tRevenue']/90)));
                        array_push($hideData3,sprintf('%.1f',$calValue['AR']/self::ONEMILLION));
                        array_push($hideData4,sprintf('%.1f',($calValue['tRevenue']/90)/self::ONEMILLION));
                        $counter++;
                        if($counter==20) {
                            $stopSign = true;
                            break;
                        }
                    }
                }
                if($stopSign) break;
            }
        }
        else if($graphType == 'year') {
            $counter = 0;
            //deal with first data
            $firstCol = true;
            //count season num for the first data
            $firstSeaNum = 4;
            foreach($sortData as $year => $seasons) {
                $stopSign = false;
                if($firstCol) {
                    foreach($seasonOrder as $season){
                        if(isset($sortData[$year][$season])){
                            $calValue = array();
                            $calValue['endDate'] = $seasons[$season]['endDate'];
                            $calValue['reportType'] = $seasons[$season]['reportType'];
                            $calValue['inventories'] = ($sortData[$year][$season]['inventories'] +
                                $sortData[$year-1]['A']['inventories'])/2;
                            $calValue['AR'] = ($sortData[$year][$season]['AR'] +
                                $sortData[$year-1]['A']['AR'])/2;
                            if($firstSeaNum == 3){
                                $calValue['COGS'] = $sortData[$year]['CQ3']['COGS'];
                                $calValue['tRevenue'] = $sortData[$year]['CQ3']['tRevenue'];
                            }
                            else {
                                $calValue['COGS'] = $sortData[$year][$season]['COGS'];
                                $calValue['tRevenue'] = $sortData[$year][$season]['tRevenue'];
                            }
                            if($season != 'A') {
                                array_push($xAris,substr($calValue['endDate'],2,2).$typeToSeason[$calValue['reportType']]);
                            }
                            else {
                                array_push($xAris,substr($calValue['endDate'],0,4));
                            }
                            array_push($data1,sprintf('%.1f',$calValue['inventories']/($calValue['COGS']/(90*$firstSeaNum))));
                            array_push($hideData1,sprintf('%.1f',$calValue['inventories']/self::ONEMILLION));
                            array_push($hideData2,sprintf('%.1f',($calValue['COGS']/(90*$firstSeaNum))/self::ONEMILLION));
                            array_push($data2,sprintf('%.1f',$calValue['AR']/($calValue['tRevenue']/(90*$firstSeaNum))));
                            array_push($hideData3,sprintf('%.1f',$calValue['AR']/self::ONEMILLION));
                            array_push($hideData4,sprintf('%.1f',($calValue['tRevenue']/(90*$firstSeaNum))/self::ONEMILLION));
                            $firstCol = false;
                            $counter++;
                            break;
                        }
                        $firstSeaNum --;
                    }
                }
                else {
                    $calValue = array();
                    $calValue['inventories'] = ($sortData[$year]['A']['inventories'] + $sortData[$year-1]['A']['inventories'])/2;
                    $calValue['AR'] = ($sortData[$year]['A']['AR'] + $sortData[$year-1]['A']['AR'])/2;
                    $calValue['COGS'] = $sortData[$year]['A']['COGS'];
                    $calValue['tRevenue'] = $sortData[$year]['A']['tRevenue'];
                    array_push($xAris,substr($year,0,4));
                    array_push($data1,sprintf('%.1f',$calValue['inventories']/($calValue['COGS']/360)));
                    array_push($hideData1,sprintf('%.1f',$calValue['inventories']/self::ONEMILLION));
                    array_push($hideData2,sprintf('%.1f',($calValue['COGS']/360)/self::ONEMILLION));
                    array_push($data2,sprintf('%.1f',$calValue['AR']/($calValue['tRevenue']/360)));
                    array_push($hideData3,sprintf('%.1f',$calValue['AR']/self::ONEMILLION));
                    array_push($hideData4,sprintf('%.1f',($calValue['tRevenue']/360)/self::ONEMILLION));
                    $counter++;
                    if($counter == 5){
                        $stopSign = true;
                        break;
                    }
                }
                if($stopSign) break;
            }
        }
        $this->viewModel = new ViewModel();
        $this->viewModel->setVariables(array(
            'divId' => $divId,
            'graphType' => $graphType,
            'xAris' => array_reverse($xAris),
            'data1' => array_reverse($data1),
            'data2' => array_reverse($data2),
            'hideData1' => array_reverse($hideData1),
            'hideData2' => array_reverse($hideData2),
            'hideData3' => array_reverse($hideData3),
            'hideData4' => array_reverse($hideData4),
        ))
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
        $graphType = $request -> getPost('graphType');
        $ticker = $request -> getPost('ticker');
        $subName = $this -> getSubindustryName($ticker);
        switch($subName) {
        case '银行':
            $rawData = $this->getTable('TlfdmtisbankTable')->fetchForChart($ticker)->toArray();
            break;
        case '证券':
            $rawData = $this->getTable('TlfdmtissecuTable')->fetchForChart($ticker)->toArray();
            break;
        case '保险':
            $rawData = $this->getTable('TlfdmtisinsuTable')->fetchForChart($ticker)->toArray();
            break;
        default:
            $rawData = $this->getTable('TlfdmtisTable')->fetchForChart($ticker)->toArray();
        }
        $xAris = array();
        $data1 = array();//净利润
        $data2 = array();//经营利润率
        $data3 = array();//毛利率
        $hideData1 = array();//毛利
        $hideData2 = array();//营业收入
        $hideData3 = array();//营业利润
        $hideData4 = array();//营业总收入
        $hideData5 = array();//净利润(母公司)
        $typeToSeason = array(
            '03-31' => 'Q1',
            '06-30' => 'Q2',
            '09-30' => 'Q3',
            '12-31' => 'Q4',
        );
        if($graphType == 'season') {
            $counter = 0;
            $cq3Value = array();
	        foreach($rawData as $key => $value) {
                if($value['reportType'] == 'CQ3'){
                    continue;
                }
                else if($value['reportType'] == 'S1'){
                    $calValue['revenue'] = $value['revenue'] - $rawData[$key+1]['revenue'];
                    $calValue['COGS'] = $value['COGS'] - $rawData[$key+1]['COGS'];
                    $calValue['NIncomeAttrP'] = $value['NIncomeAttrP'] - $rawData[$key+1]['NIncomeAttrP'];
                    $calValue['operateProfit'] = $value['operateProfit'] - $rawData[$key+1]['operateProfit'];
                    if(isset($value['tRevenue'])){
                        $calValue['tRevenue'] = $value['tRevenue'] - $rawData[$key+1]['tRevenue'];
                    }
                }
                else if($value['reportType'] == 'A'){
                    //find next CQ3 Data
                    $skey = $key;
                    while($rawData[$skey]['reportType'] != 'CQ3'){
                        $skey++;
                    }
                    $cq3Value = $rawData[$skey];
                    $calValue['revenue'] = $value['revenue'] - $cq3Value['revenue'];
                    $calValue['COGS'] = $value['COGS'] - $cq3Value['COGS'];
                    $calValue['NIncomeAttrP'] = $value['NIncomeAttrP'] - $cq3Value['NIncomeAttrP'];
                    $calValue['operateProfit'] = $value['operateProfit'] - $cq3Value['operateProfit'];
                    if(isset($value['tRevenue'])){
                        $calValue['tRevenue'] = $value['tRevenue'] - $cq3Value['tRevenue'];
                    }
                }
                else {
                    $calValue = $value;
                }
                $calValue['endDate'] = $value['endDate'];
                array_push($xAris,substr($calValue['endDate'],2,2).$typeToSeason[substr($calValue['endDate'],5)]);
                array_push($data3,sprintf('%.1f',(($calValue['revenue']-$calValue['COGS'])/$calValue['revenue'])*100));
                array_push($hideData1,sprintf('%d',($calValue['revenue']-$calValue['COGS'])/self::ONEMILLION));
                array_push($hideData2,sprintf('%d',$calValue['revenue']/self::ONEMILLION));
                array_push($hideData3,sprintf('%d',$calValue['operateProfit']/self::ONEMILLION));
                array_push($hideData5,sprintf('%d',$calValue['NIncomeAttrP']/self::ONEMILLION));
                switch($subName) {
                case '银行':
                case '证券':
                case '保险':
                    //经营利润率 is the same as 毛利率 in these three subindustry
                    array_push($data1,sprintf('%.1f',($calValue['NIncomeAttrP']/$calValue['revenue'])*100));
                    array_push($hideData4,sprintf('%d',$calValue['revenue']/self::ONEMILLION));
                    break;
                default:
                    array_push($data2,sprintf('%.1f',($calValue['operateProfit']/$calValue['tRevenue'])*100));
                    array_push($data1,sprintf('%.1f',($calValue['NIncomeAttrP']/$calValue['tRevenue'])*100));
                    array_push($hideData4,sprintf('%d',$calValue['tRevenue']/self::ONEMILLION));
                }
                $counter++;
                if($counter==20) break;
            }
        }
        else if($graphType == 'year') {
            $counter = 0;
            foreach($rawData as $key => $value) {
                //deal with Q3 and CQ3
                if($key == 0 && $value['reportType']=='Q3'){
                    continue;
                }
                if($key == 1 && $value['reportType']=='CQ3') {
                    //do nothing
                }
                else if($key != 0 && $value['reportType'] != 'A'){
                    continue;
                }
                $calValue = $value;
                if(substr($calValue['endDate'],5) == '12-31') {
                    array_push($xAris,substr($calValue['endDate'],0,4));
                }
                else {
                    array_push($xAris,substr($calValue['endDate'],0,4).$typeToSeason[substr($calValue['endDate'],5)]);
                }
                array_push($data3,sprintf('%.1f',(($calValue['revenue']-$calValue['COGS'])/$calValue['revenue'])*100));
                array_push($hideData1,sprintf('%d',($calValue['revenue']-$calValue['COGS'])/self::ONEMILLION));
                array_push($hideData2,sprintf('%d',$calValue['revenue']/self::ONEMILLION));
                array_push($hideData3,sprintf('%d',$calValue['operateProfit']/self::ONEMILLION));
                array_push($hideData5,sprintf('%d',$calValue['NIncomeAttrP']/self::ONEMILLION));
                switch($subName) {
                case '银行':
                case '证券':
                case '保险':
                    //经营利润率 is the same as 毛利率 in these three subindustry
                    array_push($data1,sprintf('%.1f',($calValue['NIncomeAttrP']/$calValue['revenue'])*100));
                    array_push($hideData5,sprintf('%d',$calValue['revenue']/self::ONEMILLION));
                    break;
                default:
                    array_push($data2,sprintf('%.1f',($calValue['operateProfit']/$calValue['tRevenue'])*100));
                    array_push($data1,sprintf('%.1f',($calValue['NIncomeAttrP']/$calValue['tRevenue'])*100));
                    array_push($hideData5,sprintf('%d',$calValue['tRevenue']/self::ONEMILLION));
                }
                $counter++;
                if($counter==5) break;
            }
        }
        $this->viewModel = new ViewModel();
        $this->viewModel->setVariables(array('divId' => $divId,
                                             'graphType' => $graphType,
                                             'xAris' => array_reverse($xAris),
                                             'data1' => array_reverse($data1),
                                             'data2' => array_reverse($data2),
                                             'data3' => array_reverse($data3),
                                             'hideData1' => array_reverse($hideData1),
                                             'hideData2' => array_reverse($hideData2),
                                             'hideData3' => array_reverse($hideData3),
                                             'hideData4' => array_reverse($hideData4),
                                             'hideData5' => array_reverse($hideData5),
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
        $graphType = $request -> getPost('graphType');
        $ticker = $request -> getPost('ticker');
        $rawData = $this->getTable('TlfdmtbsTable')->fetchForChart($ticker)->toArray();
        $sortData = $this->sortData($rawData);
        $typeToSeason = array(
            'Q1' => 'Q1',
            'S1' => 'Q2',
            'Q3' => 'Q3',
            'A' => 'Q4',
        );
        $seasonOrder = ['A','Q3','S1','Q1'];
        $xAris = array();
        $data1 = array();//流动比率
        $data2 = array();//速动比率
        $hideData1 = array();//流动资产
        $hideData2 = array();//流动负债
        $hideData3 = array();//流动资产-存货
        if($graphType == 'season') {
            $counter = 0;
            foreach($sortData as $year => $seasons) {
                $stopSign = false;
                foreach($seasonOrder as $season) {
                    if(isset($seasons[$season])) {
                        $value = $seasons[$season];
                        array_push($data1,sprintf('%.1f',($value['TCA']/$value['TCL'])*100));
                        array_push($data2,sprintf('%.1f',(($value['TCA']-$value['inventories'])/$value['TCL'])*100));
                        array_push($hideData1,sprintf('%.1f',$value['TCA']/self::ONEMILLION));
                        array_push($hideData2,sprintf('%.1f',$value['TCL']/self::ONEMILLION));
                        array_push($hideData3,sprintf('%.1f',($value['TCL']-$value['inventories'])/self::ONEMILLION));
                        array_push($xAris,substr($value['endDate'],2,2).$typeToSeason[$value['reportType']]);
                        $counter++;
                        if($counter==20) {
                            $stopSign = true;
                            break;
                        }
                    }
                }
            }
        }
        else if($graphType == 'year') {
            $counter = 0;
            //deal with first data
            $firstCol = true;
            foreach($sortData as $year => $seasons) {
                if($firstCol) {
                    foreach($seasonOrder as $season){
                        if(isset($sortData[$year][$season])){
                            $value = $seasons[$season];
                            array_push($data1,sprintf('%.1f',($value['TCA']/$value['TCL'])*100));
                            array_push($data2,sprintf('%.1f',(($value['TCA']-$value['inventories'])/$value['TCL'])*100));
                            array_push($hideData1,sprintf('%.1f',$value['TCA']/self::ONEMILLION));
                            array_push($hideData2,sprintf('%.1f',$value['TCL']/self::ONEMILLION));
                            array_push($hideData3,sprintf('%.1f',($value['TCL']-$value['inventories'])/self::ONEMILLION));
                            if($value['reportType'] != 'A') {
                                array_push($xAris,substr($value['endDate'],2,2).$typeToSeason[$value['reportType']]);
                            }
                            else {
                                array_push($xAris,substr($value['endDate'],0,4));
                            }
                            break;
                            $counter++;
                            $firstCol = false;
                        }
                    }
                }
                else {
                    $value = $seasons['A'];
                    array_push($data1,sprintf('%.1f',($value['TCA']/$value['TCL'])*100));
                    array_push($data2,sprintf('%.1f',(($value['TCA']-$value['inventories'])/$value['TCL'])*100));
                    array_push($hideData1,sprintf('%.1f',$value['TCA']/self::ONEMILLION));
                    array_push($hideData2,sprintf('%.1f',$value['TCL']/self::ONEMILLION));
                    array_push($hideData3,sprintf('%.1f',($value['TCL']-$value['inventories'])/self::ONEMILLION));
                    array_push($xAris,substr($value['endDate'],0,4));
                    $counter++;
                    if($counter == 5){
                        $stopSign = true;
                        break;
                    }
                }
            }
        }
        $this->viewModel = new ViewModel();
        $this->viewModel->setVariables(array('divId' => $divId,
                                             'graphType' => $graphType,
                                             'xAris' => array_reverse($xAris),
                                             'data1' => array_reverse($data1),
                                             'data2' => array_reverse($data2),
                                             'hideData1' => array_reverse($hideData1),
                                             'hideData2' => array_reverse($hideData2),
                                             'hideData3' => array_reverse($hideData3),
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

    public function getSubindustryName($ticker) {
        $company_data = $this->getTable('ReportTable')->fetchByTicker($ticker)->toArray();
        $company_data = $company_data[0];
        $company_data['subindustry'] = $this->getTable('SubindustryTable')->fetchById($company_data['subindustry_id'])->toArray();
        $subName = $company_data['subindustry'][0]['name'];
        return $subName;
    }

    public function sortData($dataArray){
        $result = array();
        foreach($dataArray as $key => $value){
            $year = substr($value['endDate'],0,4);
            $type = $value['reportType'];
            if(!isset($result[$year])){
                $result[$year] = array();
            }
            if(!isset($result[$year][$type])){
                $result[$year][$type] = array();
            }
            $result[$year][$type] = $value;
        }
        return $result;
    }
}
