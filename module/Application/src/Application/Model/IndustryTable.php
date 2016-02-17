<?php

namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;

class IndustryTable
{

    protected $tableGateway;

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

    public function fetchByName($name){
        $resultSet = $this->tableGateway->select(array('name = ?' => $name));
        return $resultSet;
    }

    public function addIndustry($data){
        return $this->tableGateway->insert($data);
    }

    public function updateIndustry($data,$where,$value) {
        $this->tableGateway->update($data,array($where => $value));
    }

    public function deleteIndustry($id) {
       $this->tableGateway->delete(array('id' => $id));
    }
}
