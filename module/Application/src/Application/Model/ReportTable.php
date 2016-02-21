<?php

namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;

class ReportTable
{

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }
    
    public function fetchAll() {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    public function reportCountByDate($date) {
        $resultSet = $this->tableGateway->select(array('release_date = ?' => $date));
        return $resultSet->count();
    }

    public function fetchReport($date,$industry,$subindustry,$flag) {
        if($flag==0) {
            if($industry == 'all') {
                $resultSet = $this->tableGateway->select(array('release_date = ?' => $date));
            }
            else if($subindustry == 'all') {
                $resultSet = $this->tableGateway->select(array('release_date = ?' => $date,'industry_id = ?' => $industry));
            }
            else {
                $resultSet = $this->tableGateway->select(array('release_date = ?' => $date,'industry_id = ?' => $industry,'subindustry_id = ?' => $subindustry));
            }
        }
        else {
            if($industry == 'all') {
                $resultSet = $this->tableGateway->select(array('release_date = ?' => $date,'flag = ?'=>$flag));
            }
            else if($subindustry == 'all') {
                $resultSet = $this->tableGateway->select(array('release_date = ?' => $date,'industry_id = ?' => $industry,'flag = ?'=>$flag));
            }
            else {
                $resultSet = $this->tableGateway->select(array('release_date = ?' => $date,'industry_id = ?' => $industry,'subindustry_id = ?' => $subindustry,'flag = ?'=>$flag));
            }
        }
        return $resultSet;
    }

    public function addReport($data){
        $this->tableGateway->insert($data);
    }

    public function updateReport($data,$where,$value) {
        $this->tableGateway->update($data,array($where => $value));
    }

    public function deleteReport($ticker) {
       $this->tableGateway->delete(array('ticker' => $ticker));
    }
}
