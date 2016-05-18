<?php
class Elite_Sizechart_Model_Sizechart extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('sizechart/sizechart');
    }
    public function getIdequivalente($tallaje, $talla, $categoria)
    {
        $collection = Mage::getModel('sizechart/sizechart')->getCollection()
            ->addFieldToFilter('tallaje', $tallaje)
            ->addFieldToFilter('talla', $talla)
            ->addFieldToFilter('categoria', $categoria);

        $idRetorno = $collection->getItems();
        $item = $collection->getData('idequivalente');
        return $item[0]['idequivalente'];
    }
    public function getTallajes($equivalente, $categoria)
    {
        $collection = Mage::getModel('sizechart/sizechart')->getCollection()
            ->addFieldToFilter('categoria', $categoria)
            ->addFieldToFilter('idequivalente', $equivalente);
        $tallajes="";
        foreach ($collection as $item) {
                $tallajes[]=$item->getTallaje();
            }

        return $tallajes;
    }
}