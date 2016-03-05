<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Application\Model\ReportTable;
use Application\Model\IndustryTable;
use Application\Model\SubindustryTable;
use Application\Model\TlFdmtisTable;
use Application\Model\TlFdmtbsTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getServiceConfig()
    {
        return array (
            'factories' => array (
                'Application\Model\ReportTable' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $tableGateway = new TableGateway('report_info',$dbAdapter);
                    $table = new ReportTable($tableGateway);
                    return $table;
                },
                'Application\Model\IndustryTable' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $tableGateway = new TableGateway('industry',$dbAdapter);
                    $table = new IndustryTable($tableGateway);
                    return $table;
                },
                'Application\Model\SubindustryTable' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $tableGateway = new TableGateway('subindustry',$dbAdapter);
                    $table = new SubindustryTable($tableGateway);
                    return $table;
                },
                'Application\Model\TlFdmtisTable' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $tableGateway = new TableGateway('tl_fdmtis',$dbAdapter);
                    $table = new TlFdmtisTable($tableGateway);
                    return $table;
                },
                'Application\Model\TlFdmtbsTable' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $tableGateway = new TableGateway('tl_fdmtbs',$dbAdapter);
                    $table = new TlFdmtbsTable($tableGateway);
                    return $table;
                },
            ),
        );
    }
}
