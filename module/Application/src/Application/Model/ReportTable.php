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

    public function deleteReport($ticker) {
       $this->tableGateway->delete(array(’ticker’ => $ticker));
    }
}
