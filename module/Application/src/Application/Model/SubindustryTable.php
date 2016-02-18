<?php

namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;

class SubindustryTable
{

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }
    
    public function fetchAll() {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    public function fetchByName($name){
        return $this->tableGateway->select(array('name = ?'=>$name));
    }

    public function fetchByIndustryid($id){
        return $this->tableGateway->select(array('industry_id = ?'=>$id));
    }

    public function lastId() {
        return $this->tableGateway->lastInsertValue;
    }

    public function addSubindustry($data){
        $this->tableGateway->insert($data);
    }

    public function updateSubindustry($data,$where,$value) {
        $this->tableGateway->update($data,array($where => $value));
    }

    public function deleteSubindustry($id) {
       $this->tableGateway->delete(array('id' => $id));
    }
}
