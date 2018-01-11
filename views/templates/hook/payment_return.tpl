
<p class="price"><strong>{l s='Thanks for buy in %s.' sprintf=$shop_name mod='xchange'}</strong></p>
<br/>
<h4>{l s='Your purchase has been successfully completed and your payment has been processed with Order No.' mod='xchange'}<strong> {if !isset($reference)}{l s='%d' sprintf=$id_order mod='xchange'}{else}{l s='%s' sprintf=$reference mod='xchange'}{/if}</strong>
<br/><br/>{l s='Below is a brief summary.' mod='xchange'}</h4>
<p>
<br/><br/> 
{l s='Store: ' mod='xchange'}<strong>{l s='%s' sprintf=$shop_name mod='xchange'}</strong>
<br/><br/> 
{l s='Email:' mod='xchange'} <strong>{if $xchangeEmail}{$xchangeEmail}{else}___________{/if}</strong>
<br/><br/>
{l s='Total:' mod='xchange'} <span><strong>{$total_to_pay}</strong></span>
<br/><br/>
{l s='Order N°:' mod='xchange'}<strong>{if !isset($reference)}{l s='%d' sprintf=$id_order mod='xchange'}{else}{l s='%s' sprintf=$reference mod='xchange'}{/if}</strong>
<br/><br/>
<br/><br/>
{l s='Your payment has been sent via XChange, if you want to know more, go to ' mod=' xchange'}
<a href="https://xchange.ec">{l s='www.xchange.ec' mod='xchange'}</a>{l s='and check the details.' mod='xchange'}
<br/><br/>
{l s='An email has been send with more information.' mod='xchange'}
<strong>{l s='Soon your order will be sent.' mod='xchange'}</strong>
<br /><br/>
{l s='If you have a question, contact us.' mod='xchange'}<a href="{$link->getPageLink('contact', true)|escape:'html'}">{l s='Client Support' mod='xchange'}</a>.
</p>
<br/><br/>

