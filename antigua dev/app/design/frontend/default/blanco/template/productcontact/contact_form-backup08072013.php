<?php 
	
	$productId = $this->getRequest()->getParam('id');
	$customer = $this->getCustomer();
	
	$address 	= $customer->getDefaultBillingAddress();
?>
<?php if(Mage::helper('productcontact')->isActive()): ?>
<div class="box-collateral productcontact">
	<a name="product-contact-form"></a>
	<h2><?php echo $this->__('Product contact'); ?></h2>
	<div id="messages_product_view"><?php echo $this->getMessagesBlock()->getGroupedHtml() ?></div>
	<!--<ul class="messages" style="display:none;" id="productcontact_message">
		<li class="success-msg">
			<ul><li><?php echo $this->__('Your contact for product has been sent. We will notify you as soon as. Thank you!'); ?></li></ul>
		</li>
	</ul>-->
	
	<form action="<?php echo $this->getUrl('productcontact/index/submit'); ?>" id="productcontactForm" method="post">
		<input type="hidden" name="product_id" value="<?php echo $productId; ?>"/>
		<div class="fieldset">
			<h2 class="legend"><?php echo Mage::helper('productcontact')->__('Contact Information') ?></h2>
			<ul class="form-list">
				<li class="fields">
					<?php if(Mage::helper('productcontact')->isShowCompanyName()): ?>
						<div class="field">
							<label for="company_name" class="required"><?php if(Mage::helper('productcontact')->isRequireCompanyName()): ?><em>*</em><?php endif; ?><?php echo Mage::helper('productcontact')->__('Company Name') ?></label>
							<div class="input-box">
								<?php if(Mage::helper('productcontact')->isRequireCompanyName()): ?>
									<input name="company_name" id="company_name" title="<?php echo Mage::helper('productcontact')->__('Company Name') ?>" value="<?php if($address) echo $this->htmlEscape($address->getCompany()) ?>" class="input-text required-entry" type="text"/>
								<?php else: ?>
									<input name="company_name" id="company_name" title="<?php echo Mage::helper('productcontact')->__('Company Name') ?>" value="<?php if($address) echo $this->htmlEscape($address->getCompany()) ?>" class="input-text" type="text"/>
								<?php endif; ?>
							</div>
						</div>
					<?php endif; ?>
					
					<?php if(Mage::helper('productcontact')->isShowPersonalName()): ?>
						<div class="field">
							<label for="personal_name" class="required"><?php if(Mage::helper('productcontact')->isRequirePersonalName()): ?><em>*</em><?php endif; ?><?php echo Mage::helper('productcontact')->__('Personal Name') ?></label>
							<div class="input-box">
								<?php if(Mage::helper('productcontact')->isRequirePersonalName()): ?>
									<input name="personal_name" id="personal_name" title="<?php echo Mage::helper('productcontact')->__('Personal Name') ?>" value="<?php if($customer->getId()) echo $this->htmlEscape($customer->getName()); ?>" class="input-text required-entry" type="text" />
								<?php else: ?>
									<input name="personal_name" id="personal_name" title="<?php echo Mage::helper('productcontact')->__('Personal Name') ?>" value="<?php if($customer->getId()) echo $this->htmlEscape($customer->getName()); ?>" class="input-text" type="text" />
								<?php endif; ?>
							</div>
						</div>
					<?php endif; ?>
				</li>
				<li class="fields">
					<?php if(Mage::helper('productcontact')->isShowAddress()): ?>
						<div class="field">
							<label for="address" class="required"><?php if(Mage::helper('productcontact')->isRequireAddress()): ?><em>*</em><?php endif; ?><?php echo Mage::helper('productcontact')->__('Address') ?></label>
							<div class="input-box">
								<?php if(Mage::helper('productcontact')->isRequireAddress()): ?>
									<input name="address" id="address" title="<?php echo Mage::helper('productcontact')->__('Address') ?>" value="<?php if($address) echo $this->htmlEscape($address->getStreet(1))." - ".$this->htmlEscape($address->getStreet(2)) ?>" class="input-text required-entry" type="text" />
								<?php else: ?>
									<input name="address" id="address" title="<?php echo Mage::helper('productcontact')->__('Address') ?>" value="<?php if($address) echo $this->htmlEscape($address->getStreet(1))." - ".$this->htmlEscape($address->getStreet(2)) ?>" class="input-text" type="text" />
								<?php endif; ?>
							</div>
						</div>
					<?php endif; ?>
					<?php if(Mage::helper('productcontact')->isShowZipcode()): ?>
						<div class="field">
							<label for="zipcode" class="required"><?php if(Mage::helper('productcontact')->isRequireZipcode()): ?><em>*</em><?php endif; ?><?php echo Mage::helper('productcontact')->__('Zipcode') ?></label>
							<div class="input-box">
								<?php if(Mage::helper('productcontact')->isRequireZipcode()): ?>
									<input name="zipcode" id="zipcode" title="<?php echo Mage::helper('productcontact')->__('Zipcode') ?>" value="<?php if($address) echo $this->htmlEscape($address->getPostcode()) ?>" class="input-text" type="text" />
								<?php else: ?>	
									<input name="zipcode" id="zipcode" title="<?php echo Mage::helper('productcontact')->__('Zipcode') ?>" value="<?php if($address) echo $this->htmlEscape($address->getPostcode()) ?>" class="input-text" type="text" />
								<?php endif; ?>	
							</div>
						</div>
					<?php endif; ?>
				</li>
				<li class="fields">
					<?php if(Mage::helper('productcontact')->isShowCity()): ?>
						<div class="field">
							<label for="city" class="required"><?php if(Mage::helper('productcontact')->isRequireCity()): ?><em>*</em><?php endif; ?><?php echo Mage::helper('productcontact')->__('City') ?></label>
							<div class="input-box">
								<?php if(Mage::helper('productcontact')->isRequireCity()): ?>
									<input name="city" id="city" title="<?php echo Mage::helper('productcontact')->__('City') ?>" value="<?php if($address) echo $this->htmlEscape($address->getCity()) ?>" class="input-text required-entry" type="text" />
								<?php else: ?>		
									<input name="city" id="city" title="<?php echo Mage::helper('productcontact')->__('City') ?>" value="<?php if($address) echo $this->htmlEscape($address->getCity()) ?>" class="input-text" type="text" />
								<?php endif; ?>
							</div>
						</div>
					<?php endif; ?>
					<?php if(Mage::helper('productcontact')->isShowCountry()): ?>
						<div class="field">
							<label for="country_id" class="required"><?php if(Mage::helper('productcontact')->isRequireCountry()): ?><em>*</em><?php endif; ?><?php echo Mage::helper('productcontact')->__('Country') ?></label>
							<div class="input-box">
								<?php echo $this->getCountryHtmlSelect() ?>
							</div>
						</div>
					<?php endif; ?>
				</li>
				<li class="fields">
					<?php if(Mage::helper('productcontact')->isShowPhone()): ?>
						<div class="field">
							<label for="phone" class="required"><?php if(Mage::helper('productcontact')->isRequirePhone()): ?><em>*</em><?php endif; ?><?php echo Mage::helper('productcontact')->__('Phone') ?></label>
							<div class="input-box">
								<?php if(Mage::helper('productcontact')->isRequirePhone()): ?>
									<input name="phone" id="phone" title="<?php echo Mage::helper('productcontact')->__('Phone') ?>" value="<?php if($address) echo $this->htmlEscape($address->getTelephone()) ?>" class="input-text required-entry" type="text" />
								<?php else: ?>	
									<input name="phone" id="phone" title="<?php echo Mage::helper('productcontact')->__('Phone') ?>" value="<?php if($address) echo $this->htmlEscape($address->getTelephone()) ?>" class="input-text" type="text" />
								<?php endif; ?>
							</div>
						</div>
					<?php endif; ?>
					<?php if(Mage::helper('productcontact')->isShowFax()): ?>
						<div class="field">
							<label for="fax" class="required"><?php if(Mage::helper('productcontact')->isRequireFax()): ?><em>*</em><?php endif; ?><?php echo Mage::helper('productcontact')->__('Fax') ?></label>
							<div class="input-box">
								<?php if(Mage::helper('productcontact')->isRequireFax()): ?>
									<input name="fax" id="fax" title="<?php echo Mage::helper('productcontact')->__('Fax') ?>" value="<?php if($address) echo $this->htmlEscape($address->getFax()) ?>" class="input-text required-entry" type="text" />
								<?php else: ?>
									<input name="fax" id="fax" title="<?php echo Mage::helper('productcontact')->__('Fax') ?>" value="<?php if($address) echo $this->htmlEscape($address->getFax()) ?>" class="input-text" type="text" />
								<?php endif; ?>
							</div>
						</div>
					<?php endif; ?>	
				</li>
				<li class="fields">
					<?php if(Mage::helper('productcontact')->isShowEmail()): ?>
						<div class="field">
							<label for="customer_email" class="required"><?php if(Mage::helper('productcontact')->isRequireEmail()): ?><em>*</em><?php endif; ?><?php echo Mage::helper('productcontact')->__('Email') ?></label>
							<div class="input-box">
								<?php if(Mage::helper('productcontact')->isRequireEmail()): ?>
									<input name="customer_email" id="customer_email" title="<?php echo Mage::helper('productcontact')->__('Email') ?>" value="<?php echo $this->htmlEscape($customer->getEmail()) ?>" class="input-text required-entry validate-email" type="text" />
								<?php else: ?>
									<input name="customer_email" id="customer_email" title="<?php echo Mage::helper('productcontact')->__('Email') ?>" value="<?php echo $this->htmlEscape($customer->getEmail()) ?>" class="input-text" type="text" />
								<?php endif; ?>
							</div>
						</div>
					<?php endif; ?>
					<?php if(Mage::helper('productcontact')->isShowWebsite()): ?>
						<div class="field">
							<label for="website" class="required"><?php if(Mage::helper('productcontact')->isRequireWebsite()): ?><em>*</em><?php endif; ?><?php echo Mage::helper('productcontact')->__('Website') ?></label>
							<div class="input-box">
								<?php if(Mage::helper('productcontact')->isRequireWebsite()): ?>
									<input name="website" id="website" title="<?php echo Mage::helper('productcontact')->__('Website') ?>" value="<?php echo ""; ?>" class="input-text required-entry" type="text" />
								<?php else: ?>
									<input name="website" id="website" title="<?php echo Mage::helper('productcontact')->__('Website') ?>" value="<?php echo ""; ?>" class="input-text" type="text" />	
								<?php endif; ?>
							</div>	
						</div>
					<?php endif; ?>	
				</li>
				<li class="wide">
						<div class="input-box">
							<img src="<?php echo $this->getUrl('productcontact/index/imagecaptcha');?>" id="captcha_image" />
						</div>
					
					<div class="clear"></div>
					<span><a href="" onclick="refreshImage();return false;"><?php echo $this->__("Refresh"); ?></a></span>
					<br/>
					
					<div class="left">
						<div class="left"><label class="required"><?php echo $this->__('Enter the text above');?><em>*</em></label></div>
						<div class="left">
							<input type="text" name="captcha_text" class="required-entry input-text captcha-input" id="captcha_text" value="" />
						</div>
					</div>
					<br/>
				</li>
				<li class="wide">
					<?php if(Mage::helper('productcontact')->isShowDetail()): ?>
						<label for="detail" class="required"><?php if(Mage::helper('productcontact')->isRequireDetail()): ?><em>*</em><?php endif; ?><?php echo Mage::helper('contacts')->__('Detail') ?></label>
						<div class="input-box">
							<?php if(Mage::helper('productcontact')->isRequireDetail()): ?>
								<textarea name="detail" id="detail" title="<?php echo Mage::helper('contacts')->__('Detail') ?>" class="required-entry input-text" cols="5" rows="3"></textarea>
							<?php else: ?>
								<textarea name="detail" id="detail" title="<?php echo Mage::helper('contacts')->__('Detail') ?>" class="input-text" cols="5" rows="3"></textarea>
							<?php endif; ?>
						</div>
					<?php endif; ?>	
				</li>
			</ul>
		</div>
		<div class="buttons-set">
			<div class="productcont-by-magestore">
                <a href="http://www.magestore.com"><?php echo $this->__('By Magestore'); ?></a>
				-
                <a href="http://www.magestore.com/magento-extensions.html/"><?php echo $this->__('Magento extensions');?></a>            
            </div>
			<p class="required"><?php echo Mage::helper('contacts')->__('* Required Fields') ?></p>
			<input type="text" name="hideit" id="hideit" value="" style="display:none !important;" />	
			<button type="submit"  title="<?php echo Mage::helper('contacts')->__('Submit') ?>" class="button"><span><span><?php echo Mage::helper('contacts')->__('Submit') ?></span></span></button>
		</div>
	</form>
</div>
<?php endif; ?>
<script type="text/javascript">
//<![CDATA[
    var contactForm = new VarienForm('productcontactForm', true);
	
	function refreshImage() {	
		url = '<?php echo $this->getUrl('productcontact/index/refreshcaptcha');?>';			
		$('captcha_image').src = '';

		capchaRefesh = new Ajax.Request(url, {

			method: 'get',

			onSuccess: function(transport) {

				imageCapcha = new Image();

				imageCapcha.src = transport.responseText;

				$('captcha_image').src = imageCapcha.src;

			}

		});
	}
	
	// var productcontact = new Productcontact('productcontactForm', '<?php echo $this->getUrl('productcontact/index/submit');?>' );
	
</script>
