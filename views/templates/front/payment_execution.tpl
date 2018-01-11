
{if $nbProducts <= 0}
<p class="alert alert-warning">{l s='Your shopping cart is empty.' mod='xchange'}</p>
{else}
<script type="text/javascript">
window.location = "{$link->getModuleLink('xchange', 'validation', [], true)}";
</script>
{/if}
