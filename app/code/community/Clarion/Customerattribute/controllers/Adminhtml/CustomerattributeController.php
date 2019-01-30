<?php
/**
 * Manage Customer Attribute controller file
 * 
 * @category    Clarion
 * @package     Clarion_Customerattribute
 * @author      Clarion Magento Team
 */
class Clarion_Customerattribute_Adminhtml_CustomerattributeController extends Mage_Adminhtml_Controller_Action
{
    protected $_entityTypeId;
    
    public function preDispatch()
    {
        parent::preDispatch();
        $this->_entityTypeId = Mage::getModel('eav/entity')->setType(Mage::getModel('eav/config')->getEntityType('customer'))->getTypeId();
    }
    
    /**
     * Init actions
     *
     */
    protected function _initAction()
    {
         // load layout, set active menu and breadcrumbs
        $this->_title($this->__('Customer'))
             ->_title($this->__('Attributes'))
             ->_title($this->__('Manage Attributes'));

        if($this->getRequest()->getParam('popup')) {
            $this->loadLayout('popup');
        } else {
            $this->loadLayout()
                ->_setActiveMenu('customer/customer_attribure')
                ->_addBreadcrumb(Mage::helper('clarion_customerattribute')->__('Customer'), Mage::helper('clarion_customerattribute')->__('Customer'))
                ->_addBreadcrumb(
                    Mage::helper('clarion_customerattribute')->__('Manage Customer Attributes'),
                    Mage::helper('clarion_customerattribute')->__('Manage Customer Attributes'))
            ;
        }
        return $this;
    }
    /**
     * Index action method
     */
    public function indexAction() 
    {
        $this->_initAction()
            ->renderLayout();
    }
    
    /**
     * Code to display the form
     * 
     * The main form container gets added to the content and the tabs block gets added to left.
     */
    public function newAction()
    {
        // the same form is used to create and edit
        $this->_forward('edit');
    }
    
    /**
     * Edit Customer Attribute
     */
    public function editAction()
    {
         $id = $this->getRequest()->getParam('attribute_id');
        $model = Mage::getModel('clarion_customerattribute/customerattribute')
            ->setEntityTypeId($this->_entityTypeId);

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (! $model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('clarion_customerattribute')->__('This employee no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }
        
        // entity type check
            if ($model->getEntityTypeId() != $this->_entityTypeId) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('catalog')->__('This attribute cannot be edited.'));
                $this->_redirect('*/*/');
                return;
            }
        
         // Sets the window title
        $this->_title($id ? $model->getFrontendLabel() : $this->__('New Attribute'));
        
        // 3. Set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getCustomerattributeData(true);
        if (! empty($data)) {
            $model->setData($data);
        }
        
       // print_r($model->getUsedInForms());
        //print_r($model->getData());
      //  exit;

        // 4. Register model to use later in blocks
        Mage::register('customerattribute_data', $model);

        // 5. Build edit form
        $this->_initAction()
            ->_addBreadcrumb(
                $id ? Mage::helper('clarion_customerattribute')->__('Edit Customer Attribute')
                    : Mage::helper('clarion_customerattribute')->__('New Customer Attribute'),
                $id ? Mage::helper('clarion_customerattribute')->__('Edit Customer Attribute')
                    : Mage::helper('clarion_customerattribute')->__('New Customer Attribute'));
        
        $this->_addContent($this->getLayout()->createBlock('clarion_customerattribute/adminhtml_customerattribute_edit'))
                        ->_addLeft($this->getLayout()->createBlock('clarion_customerattribute/adminhtml_customerattribute_edit_tabs'));
        
        $this->getLayout()->getBlock('clarion_customerattribute_edit_js')
            ->setIsPopup((bool)$this->getRequest()->getParam('popup'));

        $this->renderLayout();
    }
    
    public function validateAction()
    {
        $response = new Varien_Object();
        $response->setError(false);

        $attributeCode  = $this->getRequest()->getParam('attribute_code');
        $attributeId    = $this->getRequest()->getParam('attribute_id');
        $attribute = Mage::getModel('clarion_customerattribute/customerattribute')
            ->loadByCode($this->_entityTypeId, $attributeCode);

        if ($attribute->getId() && !$attributeId) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('clarion_customerattribute')->__('Attribute with the same code already exists'));
            $this->_initLayoutMessages('adminhtml/session');
            $response->setError(true);
            $response->setMessage($this->getLayout()->getMessagesBlock()->getGroupedHtml());
        }

        $this->getResponse()->setBody($response->toJson());
    }
    
    /**
     * Filter post data
     *
     * @param array $data
     * @return array
     */
    protected function _filterPostData($data)
    {
        if ($data) {
            /** @var $helperCustomerattribute Clarion_Customerattribute_Helper_Data */
            $helperCustomerattribute = Mage::helper('clarion_customerattribute');
            //labels
            foreach ($data['frontend_label'] as & $value) {
                if ($value) {
                    $value = $helperCustomerattribute->stripTags($value);
                }
            }
        }
        return $data;
    }
    
    /**
     * 
     * Save customer attributes
     */
    public function saveAction()
    {
         $data = $this->getRequest()->getPost();
         if ($data) {
             /** @var $session Mage_Admin_Model_Session */
             $session = Mage::getSingleton('adminhtml/session');

             $redirectBack   = $this->getRequest()->getParam('back', false);
             /* @var $model Clarion_Customerattribute_Model_Customerattribute */
             $model = Mage::getModel('clarion_customerattribute/customerattribute');
             /* @var $helper Mage_Catalog_Helper_Product */
             $helper = Mage::helper('clarion_customerattribute/customerattribute');

             $id = $this->getRequest()->getParam('attribute_id');

             //validate attribute_code
             if (isset($data['attribute_code'])) {
                 $validatorAttrCode = new Zend_Validate_Regex(array('pattern' => '/^[a-z][a-z_0-9]{1,254}$/'));
                 if (!$validatorAttrCode->isValid($data['attribute_code'])) {
                     $session->addError(
                         Mage::helper('clarion_customerattribute')->__('Attribute code is invalid. Please use only letters (a-z), numbers (0-9) or underscore(_) in this field, first character should be a letter.')
                     );
                     $this->_redirect('*/*/edit', array('attribute_id' => $id, '_current' => true));
                     return;
                 }
             }


             //validate frontend_input
             if (isset($data['frontend_input'])) {
                 /** @var $validatorInputType Mage_Eav_Model_Adminhtml_System_Config_Source_Inputtype_Validator */
                 $validatorInputType = Mage::getModel('eav/adminhtml_system_config_source_inputtype_validator');
                 if (!$validatorInputType->isValid($data['frontend_input'])) {
                     foreach ($validatorInputType->getMessages() as $message) {
                         $session->addError($message);
                     }
                     $this->_redirect('*/*/edit', array('attribute_id' => $id, '_current' => true));
                     return;
                 }
             }

             if ($id) {
                 $model->load($id);

                 if (!$model->getId()) {
                     $session->addError(
                         Mage::helper('clarion_customerattribute')->__('This Attribute no longer exists'));
                     $this->_redirect('*/*/');
                     return;
                 }

                 // entity type check
                 if ($model->getEntityTypeId() != $this->_entityTypeId) {
                     $session->addError(
                         Mage::helper('clarion_customerattribute')->__('This attribute cannot be updated.'));
                     $session->setCustomerattributeData($data);
                     $this->_redirect('*/*/');
                     return;
                 }

                 $data['attribute_code'] = $model->getAttributeCode();
                 $data['is_user_defined'] = $model->getIsUserDefined();
                 $data['frontend_input'] = $model->getFrontendInput();
             } else {
                 /**
                 * @todo add to helper and specify all relations for properties
                 */
                 $data['source_model'] = $helper->getAttributeSourceModelByInputType($data['frontend_input']);
                 $data['backend_model'] = $helper->getAttributeBackendModelByInputType($data['frontend_input']);
             }

             $usedInForms = array();
             $availableForms = $data['availableForms'];

             foreach ($availableForms as $form => $v){
                 if (isset($data[$form])&& $data[$form] == 1) {
                     $usedInForms[] = $form;
                 }
             }
             /*
             if (isset($data['customer_account_create'])&& $data['customer_account_create'] == 1) {
                 $usedInForms[] = 'customer_account_create';
             }

             if (isset($data['customer_account_edit'])&& $data['customer_account_edit'] == 1) {
                 $usedInForms[] = 'customer_account_edit';
             }

             if (isset($data['adminhtml_customer'])&& $data['adminhtml_customer'] == 1) {
                 $usedInForms[] = 'adminhtml_customer';
             }*/

             $data['used_in_forms'] = $usedInForms;

             if (is_null($model->getIsUserDefined()) || $model->getIsUserDefined() != 0) {
                 $data['backend_type'] = $model->getBackendTypeByInput($data['frontend_input']);
             }

             $defaultValueField = $model->getDefaultValueByInput($data['frontend_input']);
             if ($defaultValueField) {
                 $data['default_value'] = $this->getRequest()->getParam($defaultValueField);
             }

             //filter
             $data = $this->_filterPostData($data);
             //print_r($data);
             //exit;
             $model->addData($data);

             if (!$id) {
                 $model->setEntityTypeId($this->_entityTypeId);
                 $model->setIsUserDefined(1);
             }

             try {
                 $model->save();
                 $session->addSuccess(
                     Mage::helper('clarion_customerattribute')->__('The customer attribute has been saved.'));

                 /**
                  * Clear translation cache because attribute labels are stored in translation
                  */
                 Mage::app()->cleanCache(array(Mage_Core_Model_Translate::CACHE_TAG));
                 $session->setCustomerattributeData(false);
                 if ($redirectBack) {
                     $this->_redirect('*/*/edit', array('attribute_id' => $model->getId(),'_current'=>true));
                 } else {
                     $this->_redirect('*/*/', array());
                 }
                 return;
             } catch (Exception $e) {
                 $session->addError($e->getMessage());
                 $session->setCustomerattributeData($data);
                 $this->_redirect('*/*/edit', array('attribute_id' => $id, '_current' => true));
                 return;
             }
         }
         $this->_redirect('*/*/');
     }
    
   /**
     * Delete customer attribute
     *
     * @return null
     */
   public function deleteAction()
   {
        if ($id = $this->getRequest()->getParam('attribute_id')) {
            $model = Mage::getModel('clarion_customerattribute/customerattribute');

            // entity type check
            $model->load($id);
            
            if ($model->getEntityTypeId() != $this->_entityTypeId) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('clarion_customerattribute')->__('This attribute cannot be deleted.'));
                $this->_redirect('*/*/');
                return;
            }

            try {
                $model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('clarion_customerattribute')->__('The customer attribute has been deleted.'));
                $this->_redirect('*/*/');
                return;
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('attribute_id' => $this->getRequest()->getParam('attribute_id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('clarion_customerattribute')->__('Unable to find an attribute to delete.'));
        $this->_redirect('*/*/');
    }
    
    /**
     * Check the permission to run it
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        switch ($this->getRequest()->getActionName()) {
            case 'new':
            case 'save':
                return Mage::getSingleton('admin/session')->isAllowed('customer/customer_attribute/save');
                break;
            case 'delete':
                return Mage::getSingleton('admin/session')->isAllowed('customer/customer_attribute/delete');
                break;
            default:
                return Mage::getSingleton('admin/session')->isAllowed('customer/customer_attribute');
                break;
        }
    }
}
