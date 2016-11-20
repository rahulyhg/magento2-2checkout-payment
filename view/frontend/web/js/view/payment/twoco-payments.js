/**
 * Credevlabz_2Checkout Magento JS component
 *
 * @category    Credevlabz
 * @package     Credevlabz_2Checkout
 * @author      Aman Srivastava
 * @copyright   Credevlabz (http://credevlabz.org)
 */
/*browser:true*/
/*global define*/
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'credevlabz_twocheckout',
                component: 'Credevlabz_TwoCheckout/js/view/payment/method-renderer/twocheckout-method'
            }
        );
        /** Add view logic here if needed */
        return Component.extend({});
    }
);