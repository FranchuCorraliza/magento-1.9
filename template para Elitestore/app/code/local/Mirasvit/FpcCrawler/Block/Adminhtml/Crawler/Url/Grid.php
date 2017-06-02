<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Full Page Cache
 * @version   1.0.32
 * @build     662
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */



class Mirasvit_FpcCrawler_Block_Adminhtml_Crawler_Url_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected $_sortByPageTypeCount;
    protected $_sortByProductAttributeCount;
    protected $_sortCrawlerUrls;

    public function __construct()
    {
        $this->_sortByPageTypeCount = count(Mage::getSingleton('fpccrawler/config')->getSortByPageType());
        $this->_sortByProductAttributeCount = count(Mage::getSingleton('fpccrawler/config')->getSortByProductAttribute());
        $this->_sortCrawlerUrls = Mage::getSingleton('fpccrawler/config')->getSortCrawlerUrls();

        parent::__construct();
        $this->setId('grid');
        if (Mage::getSingleton('fpccrawler/config')->getSortCrawlerUrls() == 'popularity') {
            $this->setDefaultSort('rate');
            $this->setDefaultDir('DESC');
        } else {
            $this->setDefaultSort('cache_status');
        }

        $this->setSaveParametersInSession(true);
    }

    protected function _setCollectionOrder($column)
    {
        $collection = $this->getCollection();
        if ($collection) {
            $columnIndex = $column->getFilterIndex() ?
                $column->getFilterIndex() : $column->getIndex();

            if ($this->_sortCrawlerUrls == 'popularity'
                || ($this->_sortCrawlerUrls == 'custom_order' && strpos(Mage::helper('core/url')->getCurrentUrl(), '/sort/') !== false)) {
                $collection->setOrder($columnIndex, strtoupper($column->getDir()));
            } elseif ($this->_sortCrawlerUrls == 'custom_order' && $this->_sortByPageTypeCount > 0 && $this->_sortByProductAttributeCount > 0) {
                $collection->getSelect()->order(Mage::helper('fpccrawler')->getOrderSql(true));
            } elseif ($this->_sortCrawlerUrls == 'custom_order' && $this->_sortByPageTypeCount > 0) {
                $collection->getSelect()->order(Mage::helper('fpccrawler')->getOrderSql(false));
            } elseif ($this->_sortCrawlerUrls == 'custom_order' && $this->_sortByProductAttributeCount > 0) {
                $collection->getSelect()->order(array('sort_by_product_attribute asc', 'rate desc'));
            } else {
                $collection->getSelect()->order('rate desc');
            }
        }

        return $this;
    }

    protected function _prepareCollection()
    {
        if ($this->_sortCrawlerUrls == 'custom_order' && strpos(Mage::helper('core/url')->getCurrentUrl(), '/sort/') === false) {
            $_SESSION['adminhtml']['gridsort'] = 'cache_status';
        }

        $collection = Mage::getModel('fpccrawler/crawler_url')
            ->getCollection();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('url_id', array(
            'header' => Mage::helper('fpccrawler')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'url_id',
            )
        );

        $this->addColumn('url', array(
            'header' => Mage::helper('fpccrawler')->__('URL'),
            'index' => 'url',
            'renderer' =>  'Mirasvit_FpcCrawler_Block_Adminhtml_Crawler_Url_Grid_Renderer_Url',
            )
        );

        $this->addColumn('cache_id', array(
            'header' => Mage::helper('fpccrawler')->__('Cache Id'),
            'index' => 'cache_id',
            )
        );

        $this->addColumn('rate', array(
            'header' => Mage::helper('fpccrawler')->__('Popularity (number of visits)'),
            'index' => 'rate',
            'align' => 'right',
            'width' => '100px',
            'type' => 'number',
            )
        );

        $this->addColumn('sort_by_page_type', array(
            'header' => Mage::helper('fpccrawler')->__('Sort by page type'),
            'index' => 'sort_by_page_type',
            'width' => '150px',
            )
        );

        $this->addColumn('sort_by_product_attribute', array(
            'header' => Mage::helper('fpccrawler')->__('Sort by product attribute'),
            'index' => 'sort_by_product_attribute',
            'align' => 'right',
            'width' => '100px',
            )
        );

        $this->addColumn('store_id', array(
            'header' => Mage::helper('fpccrawler')->__('Store id'),
            'index' => 'store_id',
            'align' => 'right',
            'width' => '100px',
            'filter_condition_callback' => array($this, '_storeFilter'),
            )
        );

        $this->addColumn('currency', array(
            'header' => Mage::helper('fpccrawler')->__('Currency'),
            'index' => 'currency',
            'align' => 'right',
            'width' => '100px',
            )
        );

        $this->addColumn('cache_status', array(
            'header' => Mage::helper('fpccrawler')->__('Cache Status'),
            'index' => 'url',
            'renderer' => 'Mirasvit_FpcCrawler_Block_Adminhtml_Crawler_Url_Grid_Renderer_Cache',
            'filter' => false,
            'sortable' => false,
            )
        );

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('url_id');
        $this->getMassactionBlock()->setFormFieldName('url_id');
        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('fpccrawler')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('fpccrawler')->__('Are you sure?'),
        ));
        $this->getMassactionBlock()->addItem('warm', array(
            'label' => Mage::helper('fpccrawler')->__('Warm cache'),
            'url' => $this->getUrl('*/*/massWarm'),
        ));
        $this->getMassactionBlock()->addItem('clear', array(
            'label' => Mage::helper('fpccrawler')->__('Clear cache'),
            'url' => $this->getUrl('*/*/massClear'),
        ));

        return $this;
    }

    protected function _urlFilter($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
        $value = base64_encode($value);
        $value = substr($value, 0, strlen($value) - 3);

        $this->getCollection()
            ->addFieldToFilter($column->getIndex(), array('like' => '%'.$value.'%'));

        return $this;
    }

    /**
     * @param object $collection
     * @param object $column
     * @return void
     */
    protected function _storeFilter($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }

        $collection->addFieldToFilter('store_id', $value);

        return $this;
    }

}
