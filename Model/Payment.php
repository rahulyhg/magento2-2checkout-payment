<?php
/**
 * 2Checkout payment method model
 *
 * @category    Credevlabz
 * @package     Credevlabz_TwoCheckout
 * @author      Aman Srivastava
 * @copyright   Credevlabz (http://credevlabz.org)
 */
namespace Credevlabz\TwoCheckout\Model;

class Payment extends \Magento\Payment\Model\Method\Cc
{
    const CODE = 'credevlabz_twocheckout';
    protected $_code = self::CODE;
    protected $_isGateway                   = true;
    protected $_canCapture                  = true;
    protected $_canCapturePartial           = true;
    protected $_canRefund                   = false;
    protected $_canRefundInvoicePartial     = false;
    protected $_twoCheckoutApi = false;
    protected $_countryFactory;
    //protected $_supportedCurrencyCodes = array('USD');
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
        \Magento\Framework\Module\ModuleListInterface $moduleList,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        array $data = array()
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger,
            $moduleList,
            $localeDate,
            null,
            null,
            $data
        );
        $this->_countryFactory = $countryFactory;
	}
    /**
     * Payment capturing
     *
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @param float $amount
     * @return $this
     * @throws \Magento\Framework\Validator\Exception
     */
    public function capture(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $payment->getOrder();
        /** @var \Magento\Sales\Model\Order\Address $billing */
		$billing = $order->getBillingAddress();		
		$this->_logger->addDebug("In Capture function");
		try {
			$requestData =	[
				"merchantOrderId" => $order->getIncrementId(),
				"token" => $payment->getToken(),
				"currency" => $order->getBaseCurrencyCode(),
				"total" => $amount,
				"billingAddr" => [
					"email"		  => $order->getCustomerEmail(),
					'name'        => $billing->getName(),
                    'addrLine1'   => $billing->getStreetLine(1),
                    'addrLine2'   => $billing->getStreetLine(2),
                    'city' 		  => $billing->getCity(),
                    'zipCode'     => $billing->getPostcode(),
                    'state'       => $billing->getRegion(),
                    'country'     => $billing->getCountryId(),
				]
			];
			$this->_logger->addDebug("Token:".$requestData['token']);
			\Twocheckout::privateKey('01ECDA8B-8070-450C-B369-5E9E9D70108B'); //Private Key
			\Twocheckout::sellerId('901333173'); // 2Checkout Account Number
			\Twocheckout::sandbox(true); // Set to false for production accounts.
			\Twocheckout::verifySSL(false);
			$charge = \Twocheckout_Charge::auth($requestData);
			$payment->setTransactionId($charge['response']['transactionId'])
                ->setIsTransactionClosed(0);
		} catch (\Twocheckout_Error $e) {
			echo $e->getMessage();
			$this->_logger->addError(__('Payment capturing error.'));
		}
		catch(\Exception $e){
			echo $e->getMessage();
		}
        return $this;
    }

    /**
     * Determine method availability based on config data
     *
     * @param \Magento\Quote\Api\Data\CartInterface|null $quote
     * @return bool
     */
    public function isAvailable(\Magento\Quote\Api\Data\CartInterface $quote = null)
    {
		return true;
	   if (!$this->getConfigData('active')) {
            return false;
        }
        return parent::isAvailable($quote);
    }

    /**
     * Availability for currency
     *
     * @param string $currencyCode
     * @return bool
     */
    public function canUseForCurrency($currencyCode)
    {
		/* if (!in_array($currencyCode, $this->_supportedCurrencyCodes)) {
            return false;
        } */
        return true;
    }
}