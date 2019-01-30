<?php
/**
 * Manage Customer Attribute data helper
 * 
 * @category    Clarion
 * @package     Clarion_Customerattribute
 * @author      Clarion Magento Team
 */
class Clarion_Customerattribute_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * XML path to input types validator data in config
     */
    const XML_PATH_VALIDATOR_DATA_INPUT_TYPES = 'general/validator_data/input_types';

    protected $_attributesLockedFields = array();

    protected $_entityTypeFrontendClasses = array();

    /**
     * Return default frontend classes value labal array
     *
     * @return array
     */
    protected function _getDefaultFrontendClasses()
    {
        return array(
            array(
                'value' => '',
                'label' => Mage::helper('eav')->__('None')
            ),
            array(
                'value' => 'validate-number',
                'label' => Mage::helper('eav')->__('Decimal Number')
            ),
            array(
                'value' => 'validate-digits',
                'label' => Mage::helper('eav')->__('Integer Number')
            ),
            array(
                'value' => 'validate-email',
                'label' => Mage::helper('eav')->__('Email')
            ),
            array(
                'value' => 'validate-url',
                'label' => Mage::helper('eav')->__('URL')
            ),
            array(
                'value' => 'validate-alpha',
                'label' => Mage::helper('eav')->__('Letters')
            ),
            array(
                'value' => 'validate-alphanum',
                'label' => Mage::helper('eav')->__('Letters (a-z, A-Z) or Numbers (0-9)')
            )
        );
    }

    /**
     * Return merged default and entity type frontend classes value label array
     *
     * @param string $entityTypeCode
     * @return array
     */
    public function getFrontendClasses($entityTypeCode)
    {
        $_defaultClasses = $this->_getDefaultFrontendClasses();
        if (isset($this->_entityTypeFrontendClasses[$entityTypeCode])) {
            return array_merge(
                $_defaultClasses,
                $this->_entityTypeFrontendClasses[$entityTypeCode]
            );
        }
        $_entityTypeClasses = Mage::app()->getConfig()
            ->getNode('global/eav_frontendclasses/' . $entityTypeCode);
        if ($_entityTypeClasses) {
            foreach ($_entityTypeClasses->children() as $item) {
                $this->_entityTypeFrontendClasses[$entityTypeCode][] = array(
                    'value' => (string)$item->value,
                    'label' => (string)$item->label
                );
            }
            return array_merge(
                $_defaultClasses,
                $this->_entityTypeFrontendClasses[$entityTypeCode]
            );
        }
        return $_defaultClasses;
    }

    /**
     * Retrieve attributes locked fields to edit
     *
     * @param string $entityTypeCode
     * @return array
     */
    public function getAttributeLockedFields($entityTypeCode)
    {
        if (!$entityTypeCode) {
            return array();
        }
        if (isset($this->_attributesLockedFields[$entityTypeCode])) {
            return $this->_attributesLockedFields[$entityTypeCode];
        }
        $_data = Mage::app()->getConfig()->getNode('global/eav_attributes/' . $entityTypeCode);
        if ($_data) {
            foreach ($_data->children() as $attribute) {
                $this->_attributesLockedFields[$entityTypeCode][(string)$attribute->code] =
                    array_keys($attribute->locked_fields->asArray());
            }
            return $this->_attributesLockedFields[$entityTypeCode];
        }
        return array();
    }

    /**
     * Get input types validator data
     *
     * @return array
     */
    public function getInputTypesValidatorData()
    {
        return Mage::getStoreConfig(self::XML_PATH_VALIDATOR_DATA_INPUT_TYPES);
    }
    
    /**
     * Retrieve attribute hidden fields
     *
     * @return array
     */
    public function getAttributeHiddenFields()
    {
        if (Mage::registry('attribute_type_hidden_fields')) {
            return Mage::registry('attribute_type_hidden_fields');
        } else {
            return array();
        }
    }

    /**
     * Retrieve attribute disabled types
     *
     * @return array
     */
    public function getAttributeDisabledTypes()
    {
        if (Mage::registry('attribute_type_disabled_types')) {
            return Mage::registry('attribute_type_disabled_types');
        } else {
            return array();
        }
    }

    public function getAllForms($attributeId)
    {
        $coreResource = Mage::getSingleton('core/resource');

        /** @var $conn Varien_Db_Adapter_Pdo_Mysql */
        $conn = $coreResource->getConnection('core_read');

        $select = $conn->select()->from($coreResource->getTableName('customer/form_attribute'))
        ->where('attribute_id = ' . intval($attributeId));
        $result = $conn->fetchAll($select);

        $forms = [];
        foreach ($result as $form) {
            $forms[] = $form['form_code'];
        }
        return $forms;
    }
}
