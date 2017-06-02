document.observe("dom:loaded", function() {

    /** Check for Magento 1.8 which uses different logic here.. */
    if (typeof Checkout.prototype.changeSection === 'function') {

        /** Extend reloadProgressBlock to make sure shipping address and method are updated... */
        Checkout.prototype.reloadProgressBlock = Checkout.prototype.reloadProgressBlock.wrap(function(parentMethod, prevStep) {
            parentMethod(prevStep);
            if ($('billing:collect_in_store') && $('billing:collect_in_store').checked) {
                this.reloadStep('shipping');
                this.reloadStep('shipping_method');
            }
        });

        /** Override back button to allow for collect in store... */
        Checkout.prototype.back = Checkout.prototype.back.wrap(function(parentMethod) {
            if (this.loadWaiting) return;
            //Navigate back to the previous available step
            var stepIndex = this.steps.indexOf(this.currentStep);
            if ($('billing:collect_in_store') && $('billing:collect_in_store').checked) {
                stepIndex -= 2;
            }
            var section = this.steps[--stepIndex];
            var sectionElement = $('opc-' + section);

            //Traverse back to find the available section. Ex Virtual product does not have shipping section
            while (sectionElement === null && stepIndex > 0) {
                --stepIndex;
                section = this.steps[stepIndex];
                sectionElement = $('opc-' + section);
            }
            this.changeSection('opc-' + section);
        });

        /** extend changeSection method to make sure user is directed to correct checkout section. */
        Checkout.prototype.changeSection = Checkout.prototype.changeSection.wrap(function(parentMethod, section) {
            if (!$('billing:collect_in_store') || !$('billing:collect_in_store').checked) {
                parentMethod(section);
            }
            else {
                if(section == 'opc-shipping' || section == 'opc-shipping_method') {
                    section = 'opc-billing';
                }
                var changeStep = section.replace('opc-', '');
                this.gotoSection(changeStep, false);
            }
        });
    }
    else {
        /** Override back button to allow for collect in store... */
        Checkout.prototype.back = Checkout.prototype.back.wrap(function(parentMethod) {
            if (this.loadWaiting) return;

            this.accordion.sections.reverse().each(function(section) {
                if ( !Element.hasClassName( section, 'active' ) && Element.hasClassName( section, 'allow' ) ) {
                    this.accordion.sections.reverse();
                    this.accordion.openSection( section );
                    return false;
                }
            });
        });

        /** extend gotoSection method to make sure user is directed to correct checkout section. */
        Checkout.prototype.gotoSection = Checkout.prototype.gotoSection.wrap(function(parentMethod, section) {
            if (!$('billing:collect_in_store') || !$('billing:collect_in_store').checked) {
                parentMethod(section);
            }
            else {
                if (section == 'shipping' || section == 'shipping_method') {
                    section = 'billing';
                }
                parentMethod(section);
            }
        });
        
    }
});