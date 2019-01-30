<?php
/**
 * Customer attribute add/edit form main tab
 * 
 * @category    Clarion
 * @package     Clarion_Customerattribute
 * @author      Clarion Magento Team
 */

class  Clarion_Customerattribute_Block_Adminhtml_Customerattribute_Edit_Tab_Main extends Clarion_Customerattribute_Block_Adminhtml_Customerattribute_Edit_Main_Main
{
    
    /**
     * Preparing default form elements for editing attribute
     *
     * @return Clarion_Customerattribute_Block_Adminhtml_Customerattribute_Edit_Tab_Main
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();
        $attributeObject = $this->getAttributeObject();
        /* @var $form Varien_Data_Form */
        $form = $this->getForm();
        /* @var $fieldset Varien_Data_Form_Element_Fieldset */
        $fieldset = $form->getElement('base_fieldset');
        
        // frontend properties fieldset
        $fieldset = $form->addFieldset('front_fieldset', array('legend'=>Mage::helper('clarion_customerattribute')->__('Frontend Properties')));
        $fieldset->addField('sort_order', 'text', array(
            'name' => 'sort_order',
            'label' => Mage::helper('clarion_customerattribute')->__('Sort Order'),
            'title' => Mage::helper('clarion_customerattribute')->__('Sort Order'),
            'note' => Mage::helper('clarion_customerattribute')->__('The order to display attribute on the frontend'),
            'class' => 'validate-digits',
        ));
        
        $usedInForms = $attributeObject->getUsedInForms();
        $availableForms = Mage::helper('clarion_customerattribute')->getAllForms($attributeObject->getId());

        $fieldset->addField('customer_account_create', 'checkbox', array(
            'name' => 'customer_account_create',
            'checked'   => in_array('customer_account_create', $usedInForms) ? true : false,
            'value'     => '1',
            'label' => Mage::helper('clarion_customerattribute')->__('Show on the Customer Account Create Page'),
            'title' => Mage::helper('clarion_customerattribute')->__('Show on the Customer Account Create Page'),
        ));
        
        $fieldset->addField('customer_account_edit', 'checkbox', array(
            'name' => 'customer_account_edit',
            'checked'   => in_array('customer_account_edit', $usedInForms) ? true : false,
            'value'     => '1',
            'label' => Mage::helper('clarion_customerattribute')->__('Show on the Customer Account Edit Page'),
            'title' => Mage::helper('clarion_customerattribute')->__('Show on the Customer Account Edit Page'),
        ));
        
        $fieldset->addField('adminhtml_customer', 'checkbox', array(
            'name' => 'adminhtml_customer',
            'checked'   => in_array('adminhtml_customer', $usedInForms) ? true : false,
            'value'     => '1',
            'label' => Mage::helper('clarion_customerattribute')->__('Show on the Admin Manage Customers'),
            'title' => Mage::helper('clarion_customerattribute')->__('Show on the Admin Manage Customers'),
            'note' => Mage::helper('clarion_customerattribute')->__('Show on the Admin Manage Customers Add and Edit customer Page'),
        ));

        foreach ($availableForms as $form) {
            $fieldset->addField("availableForms[$form]",'hidden', ['name'=>"availableForms[$form]", 'value'=>1]);
            if(in_array($form, ['customer_account_create', 'customer_account_edit', 'adminhtml_customer'])){
                continue;
            }
            $fieldset->addField($form, 'checkbox', array(
                'name' => $form,
                'checked'   => in_array($form, $usedInForms) ? true : false,
                'value'     => '1',
                'label' => $form,
                'title' => '',
                'note' => '',
            ));
        }
        
         // define field dependencies
        /*
        $this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
            ->addFieldMap("frontend_input", 'frontend_input_type')
        );
        */
        return $this;
    }
}
