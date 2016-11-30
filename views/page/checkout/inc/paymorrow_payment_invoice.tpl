<dl id="dl_payment_invoice" style="display: none;">
    <dt>
        <input id="rb_payment_invoice" type="radio" name="paymentid" value="[{$pmPaymentId}]" [{if $oView->getCheckedPaymentId() == $pmPaymentId}]checked[{/if}]>
        <label for="rb_payment_invoice"><b>[{oxmultilang ident="PAYMORROW_PAYMENT_METHOD_NAME_INVOICE"}]</b></label>
    </dt>
    <noscript>[{oxmultilang ident="PAYMORROW_PAYMENT_NO_JAVASCRIPT"}]</noscript>
    <dd style="display: none;">
        <div id="pminvoice"></div>
    </dd>
</dl>