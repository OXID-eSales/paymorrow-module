<dl id="dl_payment_sdd" style="display: none;">
    <dt>
        <input id="rb_payment_sdd" type="radio" name="paymentid" value="[{$pmPaymentId}]" [{if $oView->getCheckedPaymentId() == $pmPaymentId}]checked[{/if}]>
        <label for="rb_payment_sdd"><b>[{oxmultilang ident="PAYMORROW_PAYMENT_METHOD_NAME_DIRECT_DEBIT"}]</b></label>
    </dt>
    <noscript>[{oxmultilang ident="PAYMORROW_PAYMENT_NO_JAVASCRIPT"}]</noscript>
    <dd style="display: none;">
        <div id="pmsdd"></div>
    </dd>
</dl>