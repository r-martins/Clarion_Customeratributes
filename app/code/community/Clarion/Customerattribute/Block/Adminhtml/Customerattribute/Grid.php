<?php
/**
 * Manage Customer Attribute grid block
 * 
 * @category    Clarion
 * @package     Clarion_Customerattribute
 * @author      Clarion Magento Team
 * 
 */
class Clarion_Customerattribute_Block_Adminhtml_Customerattribute_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        /** This set’s the ID of our grid i.e the html id attribute of the <div>.
         * If you’re using multiple grids in a page then id needs to be unique.
         */
        $this->setId('CustomerattributeGrid');
        
        /**
         * This tells which sorting column to use in our grid. Which column 
         * should be used for default sorting
         */
        $this->setDefaultSort('attribute_code');
        
        /**
         * The default sorting order, ascending or descending
         */
        $this->setDefaultDir('ASC');
        
        /**
         * this basically sets your grid operations in session. Example, if we 
         * were on page2 of grid or we had searched something on grid when 
         * refreshing or coming back to the page, the grid operations will 
         * still be there. It won’t revert back to its default form. 
         */
       // $this->setSaveParametersInSession(true);
    }
    
    /**
     * Prepare customer attributes grid collection object
     *
     * @return Clarion_Customerattribute_Block_Adminhtml_Customerattribute_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('customer/attribute_collection');
            //->addVisibleFilter();
        //echo "<pre>" . ($collection->getSelect());
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    /**
     * Prepare default grid column
     *
     * @return Clarion_Customerattribute_Block_Adminhtml_Customerattribute_Grid
     */
    protected function _prepareColumns()
    {
       /**
        * ‘id’ an unique id for column
        * ‘header’ is the name of the column
        * ‘index’ is the field from our collection. This ‘id’ column needs to be 
        * present in our collection’s models.
        */
        
        $yesno = Mage::getModel('adminhtml/system_config_source_yesno')->toArray();
        
        $this->addColumn('attribute_code', array(
            'header'=>Mage::helper('clarion_customerattribute')->__('Attribute Code'),
            'sortable'=>true,
            'index'=>'attribute_code'
        ));
		
        $this->addColumn('frontend_label', array(
            'header'=>Mage::helper('clarion_customerattribute')->__('Attribute Label'),
            'sortable'=>true,
            'index'=>'frontend_label'
        ));
		
        $this->addColumn('is_required', array(
            'header'=>Mage::helper('clarion_customerattribute')->__('Required'),
            'sortable'=>true,
            'index'=>'is_required',
            'type' => 'options',
            'options' => $yesno,
            'align' => 'center',
        ));
        
        $this->addColumn('is_user_defined', array(
            'header'=>Mage::helper('eav')->__('System'),
            'sortable'=>true,
            'index'=>'is_user_defined',
            'type' => 'options',
            'align' => 'center',
            'options' => array(
                '0' => Mage::helper('eav')->__('Yes'),   // intended reverted use
                '1' => Mage::helper('eav')->__('No'),    // intended reverted use
            ),
        ));
        
       $this->addColumn('is_visible', array(
            'header'    => Mage::helper('clarion_customerattribute')->__('Visible on Frontend'),
            'index'     => 'is_visible',
           'width'=>'100px',
            'type'      => 'options',
            'options'    => $yesno,
        ));
         
         $this->addColumn('sort_order', array(
            'header'=>Mage::helper('clarion_customerattribute')->__('Sort Order'),
            'sortable'=>true,
            'width'=>'100px',
            'index'=>'sort_order'
        ));
         
        /**
         * Adding Different Options To Grid Rows
         */
       $this->addColumn('action',
        array(
            'header'    => Mage::helper('clarion_customerattribute')->__('Action'),
            'type'      => 'action',
            'getter'    => 'getId',
            'actions'   => array(
                array(
                    'caption' => Mage::helper('clarion_customerattribute')->__('Edit'),
                    'url'     => array('base'=> '*/*/edit'),
                    'field'   => 'attribute_id'
                )
            ),
            'filter'    => false,
            'sortable'  => false,
            'index'     => 'stores',
            'is_system' => true,
       ));
        
        return parent::_prepareColumns();
    }
    
    /**
     * Row click url. 
     * when user click on any rows of the grid it goes to a specific URL.
     * URL is of the editAction of your controller and it passed the row’s id as a parameter. 
     * @param object $row Data row object
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('attribute_id' => $row->getId()));
    }
}