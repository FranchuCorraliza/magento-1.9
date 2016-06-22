<?php
class WP_ThemeFashionstore_Block_Links extends Mage_Checkout_Block_Links
{
    public function addCartLink()
    {
        if ($parentBlock = $this->getParentBlock()) {
            $count = $this->helper('checkout/cart')->getSummaryCount();

            if( $count == 1 ) {
                $text = $this->__('My Cart (%s item)', $count);
            } elseif( $count > 0 ) {
                $text = $this->__('My Cart (%s items)', $count);
            } else {
                $text = $this->__('My Cart');
            }

            $parentBlock->addLink($text, 'checkout/cart', $text, true, array(), 50, null, 'class="top-link-cart"');
        }
        return $this;
    }

    public function addCheckoutLink()
    {	
		echo ('Hola mundo2');
        if (!$this->helper('checkout')->canOnepageCheckout()) {
            return $this;
        }
        if ($parentBlock = $this->getParentBlock()) {
            $text = $this->__('Checkout');
            $parentBlock->addLink($text, 'checkout', $text, true, array(), 60, null, 'class="top-link-checkout"');
        }
        return $this;
    }

}
