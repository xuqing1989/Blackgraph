<?php

namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;

class TlFdmtbsinsuTable
{

    protected $tableGateway;

    public $apiBase = '/api/fundamental/getFdmtBSInsu.json?';
    public $apiSubindustry = '保险';

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

}
