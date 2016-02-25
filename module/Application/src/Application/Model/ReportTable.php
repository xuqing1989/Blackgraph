<?php

namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

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
        $queryWhere = array('release_date = ?' => $date,
                            'industry_id = ?' => $industry,
                            'subindustry_id = ?' => $subindustry,
                            'flag = ?' => $flag);
        foreach($queryWhere as $key => $value) {
            if($value == 'all') {
                unset($queryWhere[$key]);
            }
        }
        $resultSet = $this->tableGateway->select($queryWhere);
        return $resultSet;
    }

    public function searchReport($text) {
        $resultSet = $this->tableGateway->select(function (Select $select) use($text) {
            $select->where->like('ticker', '%'.$text.'%')
                          ->or
                          ->like('name','%'.$text.'%');
        });
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
