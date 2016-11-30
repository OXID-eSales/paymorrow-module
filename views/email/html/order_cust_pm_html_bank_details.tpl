[{if $oxPayment and $oxPayment->isPaymorrowActiveAndMapped()}]
    <h3 style="font-weight: bold; margin: 20px 0 7px; padding: 0; line-height: 35px; font-size: 12px;font-family: Arial, Helvetica, sans-serif; text-transform: uppercase; border-bottom: 4px solid #ddd;">
        [{oxmultilang ident="BANK_DETAILS"}]
    </h3>
[{/if}]
[{if $oxUserPayment->getPaymorrowIBAN() && $oxUserPayment->getPaymorrowBIC()}]
    <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 5px 0 10px;">
        [{oxmultilang ident="PAYMORROW_EMAIL_ORDER_CUST_HTML_BANK"}] [{$oxUserPayment->getPaymorrowBankName()}]<br />
        [{oxmultilang ident="PAYMORROW_EMAIL_ORDER_CUST_HTML_IBAN"}] [{$oxUserPayment->getPaymorrowIBAN()}]<br />
        [{oxmultilang ident="PAYMORROW_EMAIL_ORDER_CUST_HTML_BIC"}] [{$oxUserPayment->getPaymorrowBIC()}]
    </p>
[{else}]
    <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 5px 0 10px;">
        <b>[{oxcontent ident="oxpspmuserorderemailinvoice"}]</b>
    </p>
[{/if}]
[{if $oxPayment->getPaymorrowPaymentType() == 'pm_invoice'}]
    <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 5px 0 10px;">
        [{oxmultilang ident="PAYMORROW_EMAIL_ORDER_CUST_HTML_REFERENCE_LINE"}] [{oxmultilang ident="PAYMORROW_EMAIL_ORDER_CUST_HTML_ORDER_ID"}] [{$order->oxorder__oxordernr->value}], [{oxmultilang ident="PAYMORROW_EMAIL_ORDER_CUST_HTML_CUSTOMER_NR"}] [{$user->getCustomerPaymorrowCustomerNumber()}]
    </p>
[{elseif $oxPayment->getPaymorrowPaymentType() == 'pm_sdd'}]
    <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 5px 0 10px;">
        <b>[{oxcontent ident="oxpspmuserorderemailsdd"}]</b>
    </p>
[{/if}]
[{$smarty.block.parent}]
