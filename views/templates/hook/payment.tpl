
<script src="{$this_path_xc}/js/checkout.js"></script>
<link rel="stylesheet" type="text/css" href="{$this_path_xc}/css/style.css">
<form name="PayBox" id="PayBox" class="PayBox">
    <input type="hidden" name="api" value="{$xchangeSecretkey}">
    <input type="hidden" name="account" value="{$xchangeEmail}">
    <input type="hidden" name="user" value="{if $cookie->email}{$cookie->email}{else}invitado@prestashop.com{/if}">
    <input type="hidden" name="name" value="{if $customerName}{$customerName}{else}Invitado{/if}">
    <input type="hidden" name="store" value="{$base_dir}">
    <input type="hidden" name="amount"  value="{$cart->getOrderTotal(!$show_taxes)|string_format:"%.2f"}">
    <input type="hidden" name="razon"  value="{$shop_name}">
<button onclick="submitPayBox()" id="pay"></button>
</strong>
</form>
