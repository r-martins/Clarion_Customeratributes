<?php
/**
 * Manage Customer Attribute helper
 * 
 * @category    Clarion
 * @package     Clarion_Customerattribute
 * @author      Clarion Magento Team
 */
class Clarion_Customerattribute_Helper_Customerattribute extends Mage_Core_Helper_Abstract
{
    /**
     * Return information array of product attribute input types
     * Only a small number of settings returned, so we won't break anything in current dataflow
     * As soon as development process goes on we need to add there all possible settings
     *
     * @param string $inputType
     * @return array
     */
    public function getAttributeInputTypes($inputType = null)
    {
        /**
        * @todo specify there all relations for properties depending on input type
        */
        $inputTypes = array(
            'multiselect'   => array(
                'backend_model'     => 'eav/entity_attribute_backend_array',
                'source_model'     => 'eav/entity_attribute_source_table'
            ),
            'boolean'       => array(
                'source_model'      => 'eav/entity_attribute_source_boolean'
            )
        );

        if (is_null($inputType)) {
            return $inputTypes;
        } else if (isset($inputTypes[$inputType])) {
            return $inputTypes[$inputType];
        }
        return array();
    }

    /**
     * Return default attribute backend model by input type
     *
     * @param string $inputType
     * @return string|null
     */
    public function getAttributeBackendModelByInputType($inputType)
    {
        $inputTypes = $this->getAttributeInputTypes();
        if (!empty($inputTypes[$inputType]['backend_model'])) {
            return $inputTypes[$inputType]['backend_model'];
        }
        return null;
    }

    /**
     * Return default attribute source model by input type
     *
     * @param string $inputType
     * @return string|null
     */
    public function getAttributeSourceModelByInputType($inputType)
    {
        $inputTypes = $this->getAttributeInputTypes();
        if (!empty($inputTypes[$inputType]['source_model'])) {
            return $inputTypes[$inputType]['source_model'];
        }
        return null;
    }
    
    /**
     * Return user defined attributes attributs
     *
     * @return $collection
     */
    public function getUserDefinedAttribures()
    {
         $collection = Mage::getModel('clarion_customerattribute/customerattribute')
            ->setEntityTypeId(Mage::getModel('eav/entity')->setType(Mage::getModel('eav/config')->getEntityType('customer'))->getTypeId())->getCollection()
            ->addVisibleFilter()
            ->addFilter('is_user_defined', 1)
            ->addOrder('sort_order', 'ASC'); 
         //print $collection->getSelect();
         return $collection;
    }
    
    /**
     * check is attribute is for customer account create
     *
     * @return boolean 
     */
    public function isAttribureForCustomerAccountCreate($attributeCode)
    {
        $attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', $attributeCode);
        $usedInForms = $attribute->getUsedInForms();
        if (in_array('customer_account_create', $usedInForms)) {
            return true;
        }
         return false;
    }
    
    /**
     * check is attribute is for customer account edit
     *@param varchar $attributeCode attribute code
     * @return boolean 
     */
    public function isAttribureForCustomerAccountEdit($attributeCode)
    {
        $attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', $attributeCode);
        $usedInForms = $attribute->getUsedInForms();
        if (in_array('customer_account_edit', $usedInForms)) {
            return true;
        }
         return false;
    }
    
    /**
     * Get store id
     * 
     * @return int Store id
     */
    public function getStoreId()
    {
         return Mage::app()->getStore()->getId();;
    }
    
    /**
     * Get default value for date attribute
     * 
     * @param int $timestamp timestamp 
     * @return date formated date
     */
    public function getDefaultValueForDate($timestamp)
    {
        if(empty($timestamp)) {
            return;
        }
        
        return date('m-d-Y', $timestamp);
    }
    
    /**
     * Get formated date for customer
     * 
     * @param int $date  date
     * @return date formated date
     */
    public function getFormattedDate($date)
    {
        if(empty($date)) {
            return;
        }
        
        return date('m-d-Y', strtotime($date));
    }

}
