[{*
    Reloading oxUserPayment to get access to newly stored field
    when object is loaded in this way it becomes available accross templates
    and in other custom blocks from Paymorrow
*}]
[{assign var="oxUserPayment" value=$order->getPaymorrowOxUserPaymentReloaded()}]
[{assign var="oxPayment" value=$oxUserPayment->getPaymorrowOxPayment()}]

[{if $oxPayment and  $oxPayment->isPaymorrowActiveAndMapped()}]
    <h3 style="font-weight: bold; margin: 20px 0 7px; padding: 0; line-height: 35px; font-size: 12px;font-family: Arial, Helvetica, sans-serif; text-transform: uppercase; border-bottom: 4px solid #ddd;">
        [{oxmultilang ident="EMAIL_ORDER_CUST_HTML_PAYMENTMETHOD"}]
    </h3>
    [{if $oxPayment->getPaymorrowPaymentType() == 'pm_invoice'}]
        <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 5px 0 10px;">
            <b>[{oxmultilang ident="PAYMORROW_PAYMENT_METHOD_NAME_INVOICE"}] [{if $basket->getPaymentCosts()}]([{$basket->getFPaymentCosts()}] [{$currency->sign}])[{/if}]</b>
        </p>
    [{elseif $oxPayment->getPaymorrowPaymentType() == 'pm_sdd'}]
        <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 5px 0 10px;">
            <b>[{oxmultilang ident="PAYMORROW_PAYMENT_METHOD_NAME_DIRECT_DEBIT"}] [{if $basket->getPaymentCosts()}]([{$basket->getFPaymentCosts()}] [{$currency->sign}])[{/if}]</b>
        </p>
    [{/if}]
[{else}]
    [{$smarty.block.parent}]
[{/if}]
