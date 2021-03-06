<?php
/**
 * Ice core model scheme container class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use Ice;
use Ice\Core;
use Ice\Exception\File_Not_Found;
use Ice\Form\Model as Form_Model;
use Ice\Helper\Arrays;
use Ice\Helper\Date;
use Ice\Helper\File;
use Ice\Helper\Model as Helper_Model;
use Ice\Helper\Object;
use Ice\Helper\String;

/**
 * Class Model_Scheme
 *
 * Core model scheme container class
 *
 * @see Ice\Core\Container
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Core
 *
 * @version 0.0
 * @since 0.0
 */
class Model_Scheme extends Container
{
    use Core;

    /**
     * Model schame
     *
     * @var array
     */
    private $_modelScheme = null;

    /**
     * Private constructor for model scheme
     *
     * @param $dataScheme
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    private function __construct($dataScheme)
    {
        $this->_modelScheme = [
            'scheme' => $dataScheme
        ];
    }

    /**
     * Synchronization local model scheme with remote data source table scheme
     *
     * @param $tableName
     * @param $schemeData
     * @param bool $force
     * @return mixed
     * @throws Exception
     * @throws File_Not_Found
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function update($tableName, $schemeData, $force = false)
    {
        $schemeData['modelClass'] = Helper_Model::getModelClassByTableName($tableName);
        $schemeData['prefix'] = Helper_Model::getTablePrefix($tableName);

        $diff = self::diff($schemeData['scheme'], $tableName);

        if (empty($diff['added']) && empty($diff['deleted']) && !$force) {
            return self::getFilePathData($schemeData['modelClass']);
        }

        $schemeData['columns'] = Data_Source::getInstance($schemeData['scheme'])->getColumns($tableName);
        $schemeData['indexes'] = Data_Source::getInstance($schemeData['scheme'])->getIndexes($tableName);

        $modelMapping = [];
        $validators = [];
        $form = [];

        $primaryKeys = $schemeData['indexes']['PRIMARY KEY']['PRIMARY'];
        $foreignKeys = Arrays::column($schemeData['indexes']['FOREIGN KEY'], 0, '');

        foreach ($schemeData['columns'] as $columnName => &$column) {
            $fieldName = strtolower($columnName);

            $column['is_primary'] = false;
            $column['is_foreign'] = false;

            if (in_array($columnName, $foreignKeys)) {
                $column['is_foreign'] = true;
                if (substr($fieldName, -4, 4) != '__fk') {
                    $fieldName = String::trim($fieldName, ['__id', '_id', 'id'], String::TRIM_TYPE_RIGHT) . '__fk';
                }
            } else if (in_array($columnName, $primaryKeys)) {
                $column['is_primary'] = true;
                if (substr($fieldName, -3, 3) != '_pk') {
                    $fieldName = strtolower(Object::getName($schemeData['modelClass']));
                    do { // some primary fields
                        $fieldName .= '_pk';
                    } while (isset($modelMapping[$fieldName]));
                }
            }

            $modelMapping[$fieldName] = $columnName;

            $fieldType = isset(Form_Model::$typeMap[$column['dataType']]) ? Form_Model::$typeMap[$column['dataType']] : 'text';

            $form[$fieldName] = $fieldType;

            $validators[$fieldName] = [];

            switch ($fieldType) {
                case 'text':
                case 'textarea':
                    $validators[$fieldName]['Ice:Length_Max'] = (int)$column['length'];
                    break;
                default:
            }

            if ($column['nullable'] === false) {
                $validators[$fieldName][] = 'Ice:Not_Null';
            }
        }

        $modelClass = $schemeData['modelClass'];

        Model::getCodeGenerator()->generate([$modelClass, array_keys($modelMapping)]);

        $modelConfigData = Config::getInstance($schemeData['modelClass'])->gets(null, false);

        if (empty($modelConfigData)) {
            $modelConfigData = [];
        }

        $modelConfigData['mapping'] = $modelMapping;
        $modelConfigData[Validator::getClass()] = $validators;
        $modelConfigData[Form::getClass()] = $form;

        $modelMappingFile = Loader::getFilePath($modelClass, '.php', 'Config/', false, true);
        File::createData($modelMappingFile, $modelConfigData);

        $prevRevision = self::getFilePathData($modelClass)['revision'];
        $prevModelSchemeFile = Loader::getFilePath($modelClass . '/' . $prevRevision, '.php', 'Var/', false, true);

        $modelSchemeFile = Loader::getFilePath($modelClass, '.php', 'Var/', false, true);

        File::move($modelSchemeFile, $prevModelSchemeFile);

        Model_Scheme::getLogger()->info(['Update scheme for model: {$0}', $modelClass], Logger::SUCCESS, true);

        return File::createData($modelSchemeFile, $schemeData);
    }

    /**
     * Return different between local model scheme with remote data source table scheme
     *
     * @param $scheme
     * @param $tableName
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function diff($scheme, $tableName)
    {
        return Arrays::diff(
            self::getFilePathData(Helper_Model::getModelClassByTableName($tableName))['columns'],
            Data_Source::getInstance($scheme)->getColumns($tableName)
        );
    }

    /**
     * Create new instance of model scheme
     *
     * @param $modelClass
     * @param null $hash
     * @return Model_Scheme
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    protected static function create($modelClass, $hash = null)
    {
        return new Model_Scheme(self::getFilePathData($modelClass));
    }

    /**
     * Return path of model scheme data
     *
     * @param $modelClass
     * @return mixed
     * @throws File_Not_Found
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public static function getFilePathData($modelClass)
    {
        $filePath = Loader::getFilePath($modelClass, '.php', 'Var/', false, true);

        if (file_exists($filePath)) {
            return File::loadData($filePath);
        }

        $data = [
            'time' => Date::get(),
            'revision' => date('00000000'),
            'columns' => []
        ];

        return File::createData($filePath, $data);
    }

    /**
     * Return columns with their column schemes
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getColumnNames()
    {
        return $this->_modelScheme['scheme']['columns'];
    }

    /**
     * Scheme name of this model scheme
     *
     * @return string
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getScheme()
    {
        return $this->_modelScheme['scheme']['scheme'];
    }

    /**
     * Return indexes
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getIndexes()
    {
        return $this->_modelScheme['scheme']['indexes'];
    }
}