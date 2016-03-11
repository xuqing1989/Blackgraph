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
use Application\Model\TlFdmtcfTable;

use Application\Model\TlFdmtisbankTable;
use Application\Model\TlFdmtbsbankTable;
use Application\Model\TlFdmtcfbankTable;

use Application\Model\TlFdmtissecuTable;
use Application\Model\TlFdmtbssecuTable;
use Application\Model\TlFdmtcfsecuTable;

use Application\Model\TlFdmtisinsuTable;
use Application\Model\TlFdmtbsinsuTable;
use Application\Model\TlFdmtcfinsuTable;

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
                'Application\Model\TlFdmtcfTable' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $tableGateway = new TableGateway('tl_fdmtcf',$dbAdapter);
                    $table = new TlFdmtcfTable($tableGateway);
                    return $table;
                },

                'Application\Model\TlFdmtisbankTable' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $tableGateway = new TableGateway('tl_fdmtisbank',$dbAdapter);
                    $table = new TlFdmtisbankTable($tableGateway);
                    return $table;
                },
                'Application\Model\TlFdmtbsbankTable' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $tableGateway = new TableGateway('tl_fdmtbsbank',$dbAdapter);
                    $table = new TlFdmtbsbankTable($tableGateway);
                    return $table;
                },
                'Application\Model\TlFdmtcfbankTable' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $tableGateway = new TableGateway('tl_fdmtcfbank',$dbAdapter);
                    $table = new TlFdmtcfbankTable($tableGateway);
                    return $table;
                },

				'Application\Model\TlFdmtissecuTable' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $tableGateway = new TableGateway('tl_fdmtissecu',$dbAdapter);
                    $table = new TlFdmtissecuTable($tableGateway);
                    return $table;
                },
                'Application\Model\TlFdmtbssecuTable' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $tableGateway = new TableGateway('tl_fdmtbssecu',$dbAdapter);
                    $table = new TlFdmtbssecuTable($tableGateway);
                    return $table;
                },
                'Application\Model\TlFdmtcfsecuTable' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $tableGateway = new TableGateway('tl_fdmtcfsecu',$dbAdapter);
                    $table = new TlFdmtcfsecuTable($tableGateway);
                    return $table;
                },

				'Application\Model\TlFdmtisinsuTable' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $tableGateway = new TableGateway('tl_fdmtisinsu',$dbAdapter);
                    $table = new TlFdmtisinsuTable($tableGateway);
                    return $table;
                },
                'Application\Model\TlFdmtbsinsuTable' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $tableGateway = new TableGateway('tl_fdmtbsinsu',$dbAdapter);
                    $table = new TlFdmtbsinsuTable($tableGateway);
                    return $table;
                },
                'Application\Model\TlFdmtcfinsuTable' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $tableGateway = new TableGateway('tl_fdmtcfinsu',$dbAdapter);
                    $table = new TlFdmtcfinsuTable($tableGateway);
                    return $table;
                },
            ),
        );
    }
}
