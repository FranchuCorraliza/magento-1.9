<?php
class Elite_Sizechart_Block_Adminhtml_Sizechart_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('sizechartGrid');
        // This is the primary key of the database
        $this->setDefaultSort('sizechart_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('sizechart/sizechart')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    protected function _prepareColumns()
    {
        $this->addColumn('sizechart_id', array(
        'header' => Mage::helper('sizechart')->__('ID'),
        'align' =>'right',
        'width' => '50px',
        'index' => 'sizechart_id',
        ));
        $this->addColumn('tallaje', array(
        'header' => Mage::helper('sizechart')->__('Tallaje'),
        'align' =>'left',
        'index' => 'tallaje',
        ));

        $this->addColumn('idequivalente', array(
        'header' => Mage::helper('sizechart')->__('Id Equivalente'),
        'width' => '150px',
        'index' => 'idequivalente',
        ));

        $this->addColumn('talla', array(
        'header' => Mage::helper('sizechart')->__('Talla'),
        'width' => '150px',
        'index' => 'talla',
        ));

        $this->addColumn('categoria', array(
        'header' => Mage::helper('sizechart')->__('Categoria'),
        'width' => '150px',
        'index' => 'categoria',
        ));
		$this->addColumn('status', array(
        'header' => Mage::helper('sizechart')->__('Status'),
        'align' => 'left',
        'width' => '80px',
        'index' => 'status',
        'type' => 'options',
        'options' => array(
        1 => 'Active',
        0 => 'Inactive',
        ),
        ));
        return parent::_prepareColumns();
    }
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}