<?php
/**
 * Ice core form abstract class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;
use Ice\Core;

/**
 * Class Form
 *
 * Core Form class
 *
 * @see Ice\Core\Container
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Core
 *
 * @version 0.1
 * @since 0.0
 */
abstract class Form extends Container
{
    use Core;

    const FIELD_HIDDEN = 'Hidden';
    const FIELD_TEXT = 'Text';
    const FIELD_DATE = 'Date';
    const FIELD_CHECKBOX = 'Checkbox';
    const FIELD_GEO = 'Geo';
    const FIELD_NUMBER = 'Number';
    const FIELD_PASSWORD = 'Password';
    const FIELD_TEXTAREA = 'Textarea';
    const NAME_MODEL = 'Model';
    const NAME_SIMPLE = 'Simple';



    /**
     * Fields - form parts
     *
     * @var array
     */
    protected $_fields = [];

    /**
     * Not ignored fields
     *
     * @var array
     */
    protected $_filterFields = [];

    /**
     * Validate scheme for validate fields
     *
     * @var array
     */
    protected $_validateScheme = [];

    /**
     * Binds values
     *
     * @var array
     */
    protected $_values = [];

    private $_key = null;

    /**
     * Constructor for model forms
     *
     * @param string $key
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.1
     * @since 0.1
     */
    protected function __construct($key)
    {
        $this->_key = $key;
    }

    /**
     * Return instance of Form
     *
     * @param null $key
     * @param null $ttl
     * @return Form
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.1
     * @since 0.1
     */
    public static function getInstance($key = null, $ttl = null)
    {
        return parent::getInstance($key, $ttl);
    }

    /**
     * Create new instance of form
     *
     * @param $key
     * @param null $hash
     * @return Form
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    protected static function create($key, $hash = null)
    {
        /** @var Form $class */
        $class = self::getClass();

        if ($class == __CLASS__) {
            $class = 'Ice\Form\\' . $key;
        }

        return new $class($key);
    }

    /**
     * Add hidden type field
     *
     * @param $fieldName
     * @param $title
     * @param $placeholder
     * @param array $validators
     * @param null $value
     * @param string $template
     * @return Form
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.1
     * @since 0.0
     */
    public function hidden($fieldName, $title, $placeholder, array $validators = [], $value = null, $template = 'Ice:Field_Hidden')
    {
        return $this->addField($fieldName, Form::FIELD_HIDDEN, $title, $placeholder, $validators, $value, $template);
    }

    /**
     * Add field as form part
     *
     * @param $fieldName
     * @param $fieldType
     * @param $title
     * @param $placeholder
     * @param array $validators
     * @param $value
     * @param $template
     * @return Form
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.1
     * @since 0.0
     */
    private function addField($fieldName, $fieldType, $title, $placeholder, array $validators = [], $value, $template)
    {
        $this->_fields[$fieldName] = [
            'type' => $fieldType,
            'title' => $title,
            'placeholder' => $placeholder,
            'template' => $template
        ];

        $this->_validateScheme[$fieldName] = $validators;

        $this->bind($fieldName, $value);

        return $this;
    }

    /**
     * Bind value
     *
     * @param $key
     * @param null $value
     * @return $this
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function bind($key, $value = null)
    {
        if (is_array($key)) {
            foreach ($key as $v => $k) {
                $this->bind($v, $k);
            }

            return $this;
        }

        $this->_values[$key] = empty($value) ? null : $value;

        return $this;
    }

    /**
     * Add password type field
     *
     * @param $fieldName
     * @param $title
     * @param $placeholder
     * @param array $validators
     * @param null $value
     * @param string $template
     * @return Form
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.1
     * @since 0.1
     */
    public function password($fieldName, $title, $placeholder, array $validators = [], $value = null, $template = 'Ice:Field_Password')
    {
        return $this->addField($fieldName, Form::FIELD_PASSWORD, $title, $placeholder, $validators, $value, $template);
    }

    /**
     * Add number type field
     *
     * @param $fieldName
     * @param $title
     * @param $placeholder
     * @param array $validators
     * @param null $value
     * @param string $template
     * @return Form
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.1
     * @since 0.0
     */
    public function number($fieldName, $title, $placeholder, array $validators = [], $value = null, $template = 'Ice:Field_Number')
    {
        return $this->addField($fieldName, Form::FIELD_NUMBER, $title, $placeholder, $validators, $value, $template);
    }

    /**
     * Add text type field
     *
     * @param $fieldName
     * @param $title
     * @param $placeholder
     * @param array $validators
     * @param null $value
     * @param string $template
     * @return Form
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.1
     * @since 0.0
     */
    public function text($fieldName, $title, $placeholder, array $validators = [], $value = null, $template = 'Ice:Field_Text')
    {
        return $this->addField($fieldName, Form::FIELD_TEXT, $title, $placeholder, $validators, $value, $template);
    }

    /**
     * Add date type field
     *
     * @param $fieldName
     * @param $title
     * @param $placeholder
     * @param array $validators
     * @param null $value
     * @param string $template
     * @return Form
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.1
     * @since 0.0
     */
    public function date($fieldName, $title, $placeholder, array $validators = [], $value = null, $template = 'Ice:Field_Date')
    {
        return $this->addField($fieldName, Form::FIELD_DATE, $title, $placeholder, $validators, $value, $template);
    }

    /**
     * Add checkbox type field
     *
     * @param $fieldName
     * @param $title
     * @param $placeholder
     * @param array $validators
     * @param null $value
     * @param string $template
     * @return Form
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.1
     * @since 0.0
     */
    public function checkbox($fieldName, $title, $placeholder, array $validators = [], $value = null, $template = 'Ice:Field_Checkbox')
    {
        return $this->addField($fieldName, Form::FIELD_CHECKBOX, $title, $placeholder, $validators, $value, $template);
    }

    /**
     * Add map type field
     *
     * @param $fieldName
     * @param $title
     * @param $placeholder
     * @param array $validators
     * @param null $value
     * @param string $template
     * @return Form
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.1
     * @since 0.0
     */
    public function geo($fieldName, $title, $placeholder, array $validators = [], $value = null, $template = 'Ice:Field_Geo')
    {
        return $this->addField($fieldName, Form::FIELD_GEO, $title, $placeholder, $validators, $value, $template);
    }

    /**
     * Add textarea type field
     *
     * @param $fieldName
     * @param $title
     * @param $placeholder
     * @param array $validators
     * @param null $value
     * @param string $template
     * @return Form
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.1
     * @since 0.0
     */
    public function textarea($fieldName, $title, $placeholder, array $validators = [], $value = null, $template = 'Ice:Field_Textarea')
    {
        return $this->addField($fieldName, Form::FIELD_TEXTAREA, $title, $placeholder, $validators, $value, $template);
    }

    /**
     * Return fields - form parts
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getFields()
    {
        return $this->_fields;
    }

    /**
     * Validate form by validate scheme
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function validate()
    {
        $var = $this->getFilterFields(); // todo: may be !((bool) $this->getFilterFields())
        $validateScheme = empty($var)
            ? $this->getValidateScheme()
            : array_intersect_key($this->getValidateScheme(), array_flip($this->getFilterFields()));

        return Validator::validateByScheme($this->getValues(), $validateScheme);
    }

    /**
     * Return not ignored form fields
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getFilterFields()
    {
        return $this->_filterFields;
    }

    /**
     * Return validate scheme
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getValidateScheme()
    {
        return $this->_validateScheme;
    }

    /**
     * Return binded values
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getValues()
    {
        return $this->_values;
    }

    /**
     * Add accepted fields
     *
     * @param array $filterFields
     * @return Form
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function addFilterFields(array $filterFields)
    {
        if (empty($filterFields)) {
            return $this;
        }

        $this->_filterFields = array_merge($this->_filterFields, $filterFields);
        return $this;
    }

    /**
     * @return null|string
     */
    public function getKey()
    {
        return $this->_key;
    }

    /**
     * Submit form
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    abstract public function submit();
}