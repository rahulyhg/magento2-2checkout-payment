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
        'Magento_Payment/js/view/payment/cc-form',
        'jquery',
        'Magento_Payment/js/model/credit-card-validation/validator',
		'Credevlabz_TwoCheckout/js/view/2co.min'
    ],
    function (Component, $) {
        'use strict';
		TCO.loadPubKey('sandbox');
			// Called when token created successfully.
		var successCallback = function(data) {
			document.getElementById('credevlabz_twocheckout_token').value = data.response.token.token;
		};

		// Called when token creation fails.
		var errorCallback = function(data) {
			if (data.errorCode === 200) {
				tokenRequest();
			} else {
				return false;
			}
		};

		var tokenRequest = function() {
			// Setup token request arguments
			var args = {
				sellerId: "901333173",
				publishableKey: "902CD3D1-119F-4052-BF50-AA7DF6CE0FDD",
				ccNo: document.getElementById('credevlabz_twocheckout_cc_number').value,
				cvv: document.getElementById('credevlabz_twocheckout_cc_cid').value,
				expMonth: document.getElementById('credevlabz_twocheckout_expiration').value,
				expYear: document.getElementById('credevlabz_twocheckout_expiration_yr').value
			};

			// Make the token request
			TCO.requestToken(successCallback, errorCallback, args);
		};
		
        return Component.extend({
            defaults: {
                template: 'Credevlabz_TwoCheckout/payment/checkout-form'
            },

            getCode: function() {
                return 'credevlabz_twocheckout';
            },

            isActive: function() {
                return true;
            },

            validate: function() {
                var $form = $('#' + this.getCode() + '-form');
				tokenRequest();
                return $form.validation() && $form.validation('isValid');
            }
        });
	


    }
);