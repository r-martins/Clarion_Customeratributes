<?php
/**
 * Customer attribute add/edit form main tab
 *
 * @category   Clarion
 * @package    Clarion_Customerattribute
 * @author     Clarion Magento Team
 */
class  Clarion_Customerattribute_Block_Adminhtml_Customerattribute_Edit_Main_Main extends Mage_Adminhtml_Block_Widget_Form
{
    protected $_attribute = null;

    public function setAttributeObject($attribute)
    {
        $this->_attribute = $attribute;
        return $this;
    }

    public function getAttributeObject()
    {
        if (null === $this->_attribute) {
            return Mage::registry('customerattribute_data');
        }
        return $this->_attribute;
    }

    /**
     * Preparing default form elements for editing attribute
     *
     * @return Clarion_Customerattribute_Block_Adminhtml_Customerattribute_Edit_Main_Main
     */
    protected function _prepareForm()
    {
        $attributeObject = $this->getAttributeObject();

        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getData('action'),
            'method' => 'post'
        ));

        $fieldset = $form->addFieldset('base_fieldset',
            array('legend'=>Mage::helper('eav')->__('Attribute Properties'))
        );
        if ($attributeObject->getAttributeId()) {
            $fieldset->addField('attribute_id', 'hidden', array(
                'name' => 'attribute_id',
            ));
        }

        $this->_addElementTypes($fieldset);

        $yesno = Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray();

        $validateClass = sprintf('validate-code validate-length maximum-length-%d',
            Mage_Eav_Model_Entity_Attribute::ATTRIBUTE_CODE_MAX_LENGTH);
        $fieldset->addField('attribute_code', 'text', array(
            'name'  => 'attribute_code',
            'label' => Mage::helper('eav')->__('Attribute Code'),
            'title' => Mage::helper('eav')->__('Attribute Code'),
            'note'  => Mage::helper('eav')->__('For internal use. Must be unique with no spaces. Maximum length of attribute code must be less then %s symbols', Mage_Eav_Model_Entity_Attribute::ATTRIBUTE_CODE_MAX_LENGTH),
            'class' => $validateClass,
            'required' => true,
        ));

        $inputTypes = Mage::getModel('eav/adminhtml_system_config_source_inputtype')->toOptionArray();

        $fieldset->addField('frontend_input', 'select', array(
            'name' => 'frontend_input',
            'label' => Mage::helper('eav')->__('Customer Input Type'),
            'title' => Mage::helper('eav')->__('Customer Input Type'),
            'value' => 'text',
            'values'=> $inputTypes
        ));

        $fieldset->addField('default_value_text', 'text', array(
            'name' => 'default_value_text',
            'label' => Mage::helper('eav')->__('Default Value'),
            'title' => Mage::helper('eav')->__('Default Value'),
            'value' => $attributeObject->getDefaultValue(),
        ));

        $fieldset->addField('default_value_yesno', 'select', array(
            'name' => 'default_value_yesno',
            'label' => Mage::helper('eav')->__('Default Value'),
            'title' => Mage::helper('eav')->__('Default Value'),
            'values' => $yesno,
            'value' => $attributeObject->getDefaultValue(),
        ));

        $dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
        $fieldset->addField('default_value_date', 'date', array(
            'name'   => 'default_value_date',
            'label'  => Mage::helper('eav')->__('Default Value'),
            'title'  => Mage::helper('eav')->__('Default Value'),
            'image'  => $this->getSkinUrl('images/grid-cal.gif'),
            'value'  => $attributeObject->getDefaultValue(),
            'format'       => $dateFormatIso
        ));

        $fieldset->addField('default_value_textarea', 'textarea', array(
            'name' => 'default_value_textarea',
            'label' => Mage::helper('eav')->__('Default Value'),
            'title' => Mage::helper('eav')->__('Default Value'),
            'value' => $attributeObject->getDefaultValue(),
        ));
        
        $fieldset->addField('is_visible', 'select', array(
            'name' => 'is_visible',
            'label' => Mage::helper('eav')->__('Visible on Frontend'),
            'title' => Mage::helper('eav')->__('Visible on Frontend)'),
            'note'  => Mage::helper('eav')->__('Display attribute on the frontend'),
            'values' => $yesno,
            'value' => '1',
        ));

        $fieldset->addField('is_unique', 'select', array(
            'name' => 'is_unique',
            'label' => Mage::helper('eav')->__('Unique Value'),
            'title' => Mage::helper('eav')->__('Unique Value (not shared with other customers)'),
            'note'  => Mage::helper('eav')->__('Not shared with other customers'),
            'values' => $yesno,
        ));

        $fieldset->addField('is_required', 'select', array(
            'name' => 'is_required',
            'label' => Mage::helper('eav')->__('Values Required'),
            'title' => Mage::helper('eav')->__('Values Required'),
            'values' => $yesno,
        ));

        
        $fieldset->addField('frontend_class', 'text', array(
            'name'  => 'frontend_class',
            'label' => Mage::helper('eav')->__('Input Validation'),
            'title' => Mage::helper('eav')->__('Input Validation'),
//            'values'=> Mage::helper('clarion_customerattribute')->getFrontendClasses($attributeObject->getEntityType()->getEntityTypeCode())
        ));
        
        if ($attributeObject->getId()) {
            $form->getElement('attribute_code')->setDisabled(1);
            $form->getElement('frontend_input')->setDisabled(1);
            if (!$attributeObject->getIsUserDefined()) {
                $form->getElement('is_unique')->setDisabled(1);
            }
        }

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Initialize form fileds values
     *
     * @return Clarion_Customerattribute_Block_Adminhtml_Customerattribute_Edit_Main_Main
     */
    protected function _initFormValues()
    {
      //  Mage::dispatchEvent('adminhtml_block_eav_attribute_edit_form_init', array('form' => $this->getForm()));
        $this->getForm()
            ->addValues($this->getAttributeObject()->getData());
        return parent::_initFormValues();
    }

    /**
     * This method is called before rendering HTML
     *
     * @return Clarion_Customerattribute_Block_Adminhtml_Customerattribute_Edit_Main_Main
     */
    protected function _beforeToHtml()
    {
        parent::_beforeToHtml();
        $attributeObject = $this->getAttributeObject();
        if ($attributeObject->getId()) {
            $form = $this->getForm();
            $disableAttributeFields = Mage::helper('clarion_customerattribute')
                ->getAttributeLockedFields($attributeObject->getEntityType()->getEntityTypeCode());
            if (isset($disableAttributeFields[$attributeObject->getAttributeCode()])) {
                foreach ($disableAttributeFields[$attributeObject->getAttributeCode()] as $field) {
                    if ($elm = $form->getElement($field)) {
                        $elm->setDisabled(1);
                        $elm->setReadonly(1);
                    }
                }
            }
        }
        return $this;
    }

    /**
     * Processing block html after rendering
     * Adding js block to the end of this block
     *
     * @param   string $html
     * @return  string
     */
    protected function _afterToHtml($html)
    {
        $jsScripts = $this->getLayout()
            ->createBlock('eav/adminhtml_attribute_edit_js')->toHtml();
        return $html.$jsScripts;
    }
}