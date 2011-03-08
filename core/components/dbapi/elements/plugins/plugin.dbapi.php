<?php
/**
 * Plugin to load Legacy Database API class.
 *
 * @author Jason Coward <jason@modx.com>
 * @package modx
 * @subpackage dbapi
 */
$modx->addPackage('modx.dbapi', $modx->getOption('components_path', $scriptProperties, MODX_CORE_PATH . 'components/') . 'dbapi/model/');
$modx->getService('db', 'DBAPI');
