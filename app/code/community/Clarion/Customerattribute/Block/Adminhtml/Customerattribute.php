<?php
/**
 * Grid container file
 * 
 * @category    Clarion
 * @package     Clarion_Customerattribute
 * @author      Clarion Magento Team
 * 
 */
class Clarion_Customerattribute_Block_Adminhtml_Customerattribute extends Mage_Adminhtml_Block_Widget_Grid_Container
{       
  public function __construct()
  {
    /*both these variables tell magento the location of our Grid.php(grid block) file.
     * $this->_blockGroup.'/' . $this->_controller . '_grid'
     * i.e clarion_Customerattribute/adminhtml_customerattribute_grid
     * $_blockGroup - is your module's name.
     * $_controller - is the path to your grid block. 
     */
    $this->_controller = 'adminhtml_customerattribute';
    $this->_blockGroup = 'clarion_customerattribute';
    
    $this->_headerText = Mage::helper('clarion_customerattribute')->__('Manage Attributes ');
    $this->_addButtonLabel = Mage::helper('clarion_customerattribute')->__('Add New Attribute');

    parent::__construct();
  }
}