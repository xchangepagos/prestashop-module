<?php

if (!defined('_PS_VERSION_'))
	exit;

class Xchange extends PaymentModule
{
	protected $_html = '';
	protected $_postErrors = array();

	public $email;
	public $secretkey;
	public $extra_mail_vars;
	public function __construct()
	{
		$this->name = 'xchange';
		$this->tab = 'payments_gateways';
		$this->version = '1.0';
		$this->author = 'Teknologies INC';
		$this->controllers = array('payment', 'validation');
		$this->is_eu_compatible = 1;
		$this->module_key = '5487d6f0ba5cf2b133c7091e19b9223b';

		$this->currencies = true;
		$this->currencies_mode = 'checkbox';

		$config = Configuration::getMultiple(array('XCHANGE_EMAIL', 'XCHANGE_SECRETKEY'));
		if (!empty($config['XCHANGE_EMAIL']))
			$this->email = $config['XCHANGE_EMAIL'];
		if (!empty($config['XCHANGE_SECRETKEY']))
			$this->secretkey = $config['XCHANGE_SECRETKEY'];

		$this->bootstrap = true;
		parent::__construct();

		$this->displayName = $this->l('XChange');
		$this->description = $this->l('Accept online payments in your store agile and secure.');
		$this->confirmUninstall = $this->l('Are you sure to uninstall all details');
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => '1.6.99.99');

		if (!isset($this->email) || !isset($this->secretkey))
			$this->warning = $this->l('It is necessary to configure the account data before using this module.');
		if (!count(Currency::checkPaymentCurrencies($this->id)))
			$this->warning = $this->l('No currency are set.');

		$this->extra_mail_vars = array(
		'{xchange_email}' => Configuration::get('XCHANGE_EMAIL'),
		'{xchange_secretkey}' => nl2br(Configuration::get('XCHANGE_SECRETKEY'))
										);
	}

	public function install()
	{
		if (!parent::install() || !$this->registerHook('payment') || ! $this->registerHook('displayPaymentEU') || !$this->registerHook('paymentReturn'))
			return false;
		return true;
	}

	public function uninstall()
	{
		if (!Configuration::deleteByName('XCHANGE_EMAIL')
				|| !Configuration::deleteByName('XCHANGE_SECRETKEY')
				|| !parent::uninstall())
			return false;
		return true;
	}


	protected function _postValidation()
	{
		if (Tools::isSubmit('btnSubmit'))
		{
			if (!Tools::getValue('XCHANGE_SECRETKEY'))
				$this->_postErrors[] = $this->l('The Secret API are necesary.');
			elseif (!Tools::getValue('XCHANGE_EMAIL'))
				$this->_postErrors[] = $this->l('The user account is necesary.');
		}
	}

	protected function _postProcess()
	{
		if (Tools::isSubmit('btnSubmit'))
		{
			Configuration::updateValue('XCHANGE_EMAIL', Tools::getValue('XCHANGE_EMAIL'));
			Configuration::updateValue('XCHANGE_SECRETKEY', Tools::getValue('XCHANGE_SECRETKEY'));
		}
		$this->_html .= $this->displayConfirmation($this->l('Settings updated'));
	}

	protected function _displayXchange()
	{
		return $this->display(__FILE__, 'infos.tpl');
	}

	public function getContent()
	{
		if (Tools::isSubmit('btnSubmit'))
		{
			$this->_postValidation();
			if (!count($this->_postErrors))
				$this->_postProcess();
			else
				foreach ($this->_postErrors as $err)
					$this->_html .= $this->displayError($err);
		}
		else
			$this->_html .= '<br />';

		$this->_html .= $this->_displayXchange();
		$this->_html .= $this->renderForm();

		return $this->_html;
	}


	public function hookPayment($params)
	{
		if (!$this->active)
			return;
		if (!$this->checkCurrency($params['cart']))
			return;
		$this->smarty->assign(array(
			'total_to_pay' => Tools::displayPrice($params['total_to_pay'], $params['currencyObj'], false),
			'xchangeSecretkey' => Tools::nl2br($this->secretkey),
			'xchangeEmail' => $this->email,
			'this_path' => $this->_path,
			'this_path_xc' => $this->_path,
			'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->name.'/'
		));

		return $this->display(__FILE__, 'payment.tpl');
	}

	public function hookDisplayPaymentEU($params)
	{
		if (!$this->active)
			return;

		if (!$this->checkCurrency($params['cart']))
			return;

		$payment_options = array(
			'cta_text' => $this->l('Pay by XCHANGE'),
			'logo' => Media::getMediaPath(_PS_MODULE_DIR_.$this->name.'/xchange.jpg'),
			'action' => $this->context->link->getModuleLink($this->name, 'validation', array(), true)
		);

		return $payment_options;
	}

	public function hookPaymentReturn($params)
	{
		if (!$this->active)
			return;

		$state = $params['objOrder']->getCurrentState();
		if (in_array($state, array(Configuration::get('PS_OS_PAYMENT'), Configuration::get('PS_OS_OUTOFSTOCK'), Configuration::get('PS_OS_OUTOFSTOCK_UNPAID'))))
		{
			$this->smarty->assign(array(
				'total_to_pay' => Tools::displayPrice($params['total_to_pay'], $params['currencyObj'], false),
				'xchangeSecretkey' => Tools::nl2br($this->secretkey),
				'xchangeEmail' => $this->email,
				'status' => 'ok',
				'id_order' => $params['objOrder']->id
			));
			if (isset($params['objOrder']->reference) && !empty($params['objOrder']->reference))
				$this->smarty->assign('reference', $params['objOrder']->reference);
		}
		else
			$this->smarty->assign('status', 'failed');
		return $this->display(__FILE__, 'payment_return.tpl');
	}

	public function checkCurrency($cart)
	{
		$currency_order = new Currency($cart->id_currency);
		$currencies_module = $this->getCurrency($cart->id_currency);

		if (is_array($currencies_module))
			foreach ($currencies_module as $currency_module)
				if ($currency_order->id == $currency_module['id_currency'])
					return true;
		return false;
	}

	public function renderForm()
	{
		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Details of the XChange account'),
					'icon' => 'icon-user'
				),
				'input' => array(
					array(
						'type' => 'text',
						'label' => $this->l('Email / Account'),
						'name' => 'XCHANGE_EMAIL',
						'required' => true
					),
					array(
						'type' => 'text',
						'label' => $this->l('Secret Api'),
						'name' => 'XCHANGE_SECRETKEY',
						'desc' => $this->l('PROFILE / USER DATA / SECRET API.'),
						'required' => true
					),
				),
				'submit' => array(
					'title' => $this->l('Save'),
				)
			),
		);

		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$this->fields_form = array();
		$helper->id = (int)Tools::getValue('id_carrier');
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'btnSubmit';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->tpl_vars = array(
			'fields_value' => $this->getConfigFieldsValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id
		);

		return $helper->generateForm(array($fields_form));
	}

	public function getConfigFieldsValues()
	{
		return array(
			'XCHANGE_EMAIL' => Tools::getValue('XCHANGE_EMAIL', Configuration::get('XCHANGE_EMAIL')),
			'XCHANGE_SECRETKEY' => Tools::getValue('XCHANGE_SECRETKEY', Configuration::get('XCHANGE_SECRETKEY')),
		);
	}
}
