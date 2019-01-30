<?php
/**
 * Customer attribute edit block
 * 
 * @category    Clarion
 * @package     Clarion_Customerattribute
 * @author      Clarion Magento Team
 */
class Clarion_Customerattribute_Block_Adminhtml_Customerattribute_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        /**
         * This variable is used in the form URL’s. 
         * This variable has the forms entity primary key, e.g the delete button URL would be 
         * module/controller/action/$this->_objectid/3
         */
        $this->_objectId = 'attribute_id';
        
        /*
         * There two variables are very important, these variables are used to find FORM tags php file.
         * i.e the path of the form tags php file should be 
         * {$this->_blockGroup . ‘/’ . $this->_controller . ‘_’ . $this->_mode . ‘_form’}.
         * The value of $this->_mode by default is ‘edit’. So the path of the php file which contains
         *  the form tag in our case would be ‘clarion_customerattribute/adminhtml_customerattribute_edit_form’. 
         */
        $this->_blockGroup = 'clarion_customerattribute';
        $this->_controller = 'adminhtml_customerattribute';
        
        parent::__construct();
         
        $this->_updateButton('save', 'label', Mage::helper('clarion_customerattribute')->__('Save Attribute'));
        $this->_updateButton('save', 'onclick', 'saveAttribute()');
        
        if (! Mage::registry('customerattribute_data')->getIsUserDefined()) {
            $this->_removeButton('delete');
        } else {
            $this->_updateButton('delete', 'label', Mage::helper('clarion_customerattribute')->__('Delete Attribute'));
        }
         
        $this->_addButton(
            'saveandcontinue', 
            array(
                'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
                'onclick'   => 'saveAndContinueEdit()',
                'class'     => 'save',
            ), 
            100
         );
       /*
        $this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
       */ 
    }
 
    /**
     * This function return’s the Text to display as the form header.
     */
    public function getHeaderText()
    {
        if (Mage::registry('customerattribute_data')->getId()) {
            $frontendLabel = Mage::registry('customerattribute_data')->getFrontendLabel();
            if (is_array($frontendLabel)) {
                $frontendLabel = $frontendLabel[0];
            }
            return Mage::helper('clarion_customerattribute')->__('Edit Customer Attribute "%s"', $this->escapeHtml($frontendLabel));
        }
        else {
            return Mage::helper('clarion_customerattribute')->__('New Customer Attribute');
        }
    }
    
    public function getValidationUrl()
    {
        return $this->getUrl('*/*/validate', array('_current'=>true));
    }

    public function getSaveUrl()
    {
        return $this->getUrl('*/'.$this->_controller.'/save', array('_current'=>true, 'back'=>null));
    }
}