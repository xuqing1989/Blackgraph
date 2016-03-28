<?php

namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

class TlFdmtisbankTable
{

    protected $tableGateway;

    public $apiBase = '/api/fundamental/getFdmtISBank.json?';
    public $apiSubindustry = '银行';

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
                    -> order(array('endDate ASC'));
        });
        return $resultSet;
    }
}
