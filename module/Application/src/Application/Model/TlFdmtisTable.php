<?php

namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

class TlFdmtisTable
{
    //合并利润表

    protected $tableGateway;

    public $apiBase = '/api/fundamental/getFdmtIS.json?';
    public $apiSubindustry = 'all';

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }
    
    public function fetchAll() {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    public function lastId() {
        return $this->tableGateway->lastInsertValue;
    }

    public function insertData($data){
        return $this->tableGateway->insert($data);
    }

    public function fetchByKey($keyData) {
        return $this->tableGateway->select($keyData);
    }
    
    public function fetchForChart($ticker){
        $resultSet = $this->tableGateway->select(function (Select $select) use($ticker) {
            $select -> where(array('ticker = ?' => $ticker))
                    -> order(array('endDate DESC'));
        });
        return $resultSet;
    }

    public function fetchForChartJoinBs($ticker){
        $resultSet = $this->tableGateway->select(function (Select $select) use($ticker) {
            $select -> join(
                'tl_fdmtbs',
                new \Zend\Db\Sql\Expression(
                    'tl_fdmtbs.ticker = tl_fdmtis.ticker and 
                    tl_fdmtbs.endDate = tl_fdmtis.endDate and 
                    tl_fdmtbs.reportType = tl_fdmtis.reportType'
                ), 
                 array('AR','inventories'),
                'left'
            )
            -> where(array('tl_fdmtis.ticker = ?' => $ticker))
            -> order(array('endDate DESC'));
        });
        return $resultSet;
    }
}
