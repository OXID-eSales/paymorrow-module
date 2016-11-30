[{*
Reloading oxUserPayment to get access to newly stored field
when object is loaded in this way it becomes available accross templates
and in other custom blocks from Paymorrow
*}]
[{assign var="oxUserPayment" value=$order->getPaymorrowOxUserPaymentReloaded()}]
[{assign var="oxPayment" value=$oxUserPayment->getPaymorrowOxPayment()}]

[{if $oxPayment and $oxPayment->isPaymorrowActiveAndMapped()}]
[{oxmultilang ident="EMAIL_ORDER_CUST_HTML_PAYMENTMETHOD"}]
[{if $oxPayment->getPaymorrowPaymentType() == 'pm_invoice'}]
[{oxmultilang ident="PAYMORROW_PAYMENT_METHOD_NAME_INVOICE"}] [{if $basket->getPaymentCosts()}]([{$basket->getFPaymentCosts()}] [{$currency->sign}])[{/if}]
[{elseif $oxPayment->getPaymorrowPaymentType() == 'pm_sdd'}]
[{oxmultilang ident="PAYMORROW_PAYMENT_METHOD_NAME_DIRECT_DEBIT"}] [{if $basket->getPaymentCosts()}]([{$basket->getFPaymentCosts()}] [{$currency->sign}])[{/if}]
[{/if}]
[{else}]
[{$smarty.block.parent}]
[{/if}]
