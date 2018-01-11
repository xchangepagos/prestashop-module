<?php


include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../header.php');
include(dirname(__FILE__).'/../../init.php');

$context = Context::getContext();
$cart = $context->cart;
$xchange = Module::getInstanceByName('xchange');

if ($cart->id_customer == 0 OR $cart->id_address_delivery == 0 OR $cart->id_address_invoice == 0 OR !$xchange->active)
	Tools::redirect('index.php?controller=order&step=1');

$authorized = false;
foreach (Module::getPaymentModules() as $module)
	if ($module['name'] == 'xchange')
	{
		$authorized = true;
		break;
	}
if (!$authorized)
	die($xchange->l('This payment method is not available.', 'validation'));

$customer = new Customer((int)$cart->id_customer);

if (!Validate::isLoadedObject($customer))
	Tools::redirect('index.php?controller=order&step=1');

$currency = $context->currency;
$total = (float)($cart->getOrderTotal(true, Cart::BOTH));

$xchange->validateOrder($cart->id, Configuration::get('PS_OS_PAYMENT'), $total, $xchange->displayName, NULL, array(), (int)$currency->id, false, $customer->secure_key);

$order = new Order($xchange->currentOrder);
Tools::redirect('index.php?controller=order-confirmation&id_cart='.$cart->id.'&id_module='.$xchange->id.'&id_order='.$xchange->currentOrder.'&key='.$customer->secure_key);
 