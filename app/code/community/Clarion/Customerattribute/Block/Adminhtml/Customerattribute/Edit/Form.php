<?php
/**
 * Customer attribute add/edit form block
 * 
 * @category    Clarion
 * @package     Clarion_Customerattribute
 * @author      Clarion Magento Team
 */
class Clarion_Customerattribute_Block_Adminhtml_Customerattribute_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
    $form = new Varien_Data_Form(array(
                                  'id' => 'edit_form',
                                  'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
                                  'method' => 'post',
                                  'enctype' => 'multipart/form-data'
                               )
    );
     
    $form->setUseContainer(true);// form renderer to output the surrounding <form> tags
    $this->setForm($form);
    return parent::_prepareForm();
  }
}