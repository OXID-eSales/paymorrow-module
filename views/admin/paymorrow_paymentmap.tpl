[{assign var="sPaymentOXID" value=$oView->getPaymentObjectId()}]
[{assign var="oPayment" value=$oView->getPaymorrowEditValue()}]
[{assign var="edit" value=$oPayment}]

[{if !$oPayment->isPaymorrowActive() || $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

[{if $edit and $edit->isPaymorrowActiveAndMapped()}]
    [{include file="paymorrow_paymnet_validation.tpl"}]
[{/if}]

<form name="transfer" id="transfer" action="[{$oViewConf->getSelfLink()}]" method="post">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="oxid" value="[{$sPaymentOXID}]">
    <input type="hidden" name="cl" value="oxpspaymorrowpaymentmap">
    <input type="hidden" name="editlanguage" value="[{$editlanguage}]">
</form>

<form name="myedit" id="myedit" action="[{$oViewConf->getSelfLink()}]" method="post">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="cl" value="oxpspaymorrowpaymentmap">
    <input type="hidden" name="fnc" value="save">
    <input type="hidden" name="oxid" value="[{$sPaymentOXID}]">
    <input type="hidden" name="editval[oxpayments__oxid]" value="[{$sPaymentOXID}]">

    <table cellspacing="0" cellpadding="0" border="0" width="98%">
        <tr>
            <td valign="top" class="edittext">
                <table cellspacing="0" cellpadding="0" border="0">
                    <tr>
                        <td class="edittext" width="70">
                            [{oxmultilang ident="GENERAL_ACTIVE"}]
                        </td>
                        <td class="edittext">
                            <input class="edittext" type="checkbox" name="editval[oxpayments__oxpspaymorrowactive]" value='1' [{if $oPayment->isPaymorrowActive()}]checked[{/if}]>
                            [{oxinputhelp ident="PM_HELP_ADMIN_PAYMENT_METHODS_ACTIVATE"}]
                        </td>
                    </tr>
                    <tr>
                        <td class="edittext" width="70">
                            &nbsp;
                        </td>
                    </tr>
                    <tr>
                        <td class="edittext" width="70">
                            [{oxmultilang ident="OXPSPAYMORROW_PAYMENT_TYPE_INVOICE"}]
                        </td>
                        <td class="edittext">
                            <input class="edittext" type="radio" name="editval[oxpayments__oxpspaymorrowmap]" value="1" [{if $oPayment->oxpayments__oxpspaymorrowmap->value == 1}]CHECKED[{/if}] [{$readonly}]>
                            [{oxinputhelp ident="PM_HELP_ADMIN_PAYMENT_METHODS_INVOICE"}]
                        </td>
                    </tr>
                    <tr>
                        <td class="edittext" width="100">
                            [{oxmultilang ident="OXPSPAYMORROW_PAYMENT_TYPE_DIRECT_DEBIT"}]
                        </td>
                        <td class="edittext">
                            <input class="edittext" type="radio" name="editval[oxpayments__oxpspaymorrowmap]" value="2" [{if $oPayment->oxpayments__oxpspaymorrowmap->value == 2}]CHECKED[{/if}] [{$readonly}]>
                            [{oxinputhelp ident="PM_HELP_ADMIN_PAYMENT_METHODS_SDD"}]
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">&nbsp;</td>
                    </tr>
                    <tr>
                        <td class="edittext">
                            [{oxmultilang ident="PAYMENT_MAIN_ADDPRICE"}]&nbsp;([{$oActCur->sign}])
                        </td>
                        <td class="edittext">
                            <input type="text" class="editinput" size="15"
                                   maxlength="[{$edit->oxpayments__oxaddsum->fldmax_length}]"
                                   name="editval[oxpayments__oxaddsum]"
                                   value="[{$edit->oxpayments__oxaddsum->value}]" [{$readonly}]/>
                            <select name="editval[oxpayments__oxaddsumtype]"
                                    class="editinput" [{include file="help.tpl" helpid=addsumtype}] [{$readonly}]>
                                <option value="%" [{if '%' == $edit->oxpayments__oxaddsumtype->value}]SELECTED[{/if}]>
                                    %
                                </option>
                                <option value="abs"
                                        [{if 'abs' == $edit->oxpayments__oxaddsumtype->value}]SELECTED[{/if}]>
                                    abs
                                </option>
                            </select>
                            [{oxinputhelp ident="HELP_PAYMENT_MAIN_ADDPRICE"}]
                        </td>
                    </tr>
                    <tr>
                        <td class="edittext">
                            [{oxmultilang ident="PAYMENT_MAIN_AMOUNT"}]&nbsp;([{$oActCur->sign}])
                        </td>
                        <td class="edittext">
                            [{oxmultilang ident="PAYMENT_MAIN_FROM"}]
                            <input type="text" class="editinput" size="5"
                                   maxlength="[{$edit->oxpayments__oxfromamount->fldmax_length}]"
                                   name="editval[oxpayments__oxfromamount]"
                                   value="[{$edit->oxpayments__oxfromamount->value}]" [{$readonly}]/>
                            [{oxmultilang ident="PAYMENT_MAIN_TILL"}]
                            <input type="text" class="editinput" size="5"
                                   maxlength="[{$edit->oxpayments__oxtoamount->fldmax_length}]"
                                   name="editval[oxpayments__oxtoamount]"
                                   value="[{$edit->oxpayments__oxtoamount->value}]" [{$readonly}]/>
                            [{oxinputhelp ident="HELP_PAYMENT_MAIN_AMOUNT"}]
                        </td>
                    </tr>
                    <tr>
                        <td class="edittext">
                            [{oxmultilang ident="PAYMENT_MAIN_SELECTED"}]
                        </td>
                        <td class="edittext">
                            <input type="checkbox" name="editval[oxpayments__oxchecked]" value="1"
                                   [{if $edit->oxpayments__oxchecked->value}]checked[{/if}] [{$readonly}]/>
                            [{oxinputhelp ident="HELP_PAYMENT_MAIN_SELECTED"}]
                        </td>
                    </tr>
                    <tr>
                        <td class="edittext">
                            [{oxmultilang ident="GENERAL_SORT"}]
                        </td>
                        <td class="edittext">
                            <input type="text" class="editinput" size="25"
                                   maxlength="[{$edit->oxpayments__oxsort->fldmax_length}]"
                                   name="editval[oxpayments__oxsort]"
                                   value="[{$edit->oxpayments__oxsort->value}]" [{$readonly}]/>
                            [{oxinputhelp ident="HELP_PAYMENT_MAIN_SORT"}]
                        </td>
                    </tr>
                    <tr>
                        <td class="edittext"><br>
                            <input type="submit" class="edittext" name="save" value="[{oxmultilang ident="GENERAL_SAVE"}]" onClick="Javascript:document.myedit.fnc.value='save'"" style="width: 150px;">
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</form>

[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]
