[{assign var="sBaseLink" value=$oViewConf->getSelfLink()|cat:'&amp;cl=oxpspaymorrowresource&amp;fnc='}]
[{assign var="sStylesUrl" value=$sBaseLink|cat:'getPaymorrowAdminCss'}]
[{assign var="sScriptUrl" value=$sBaseLink|cat:'getPaymorrowAdminJavaScript'}]
[{assign var="sLanguageCode" value=$oViewConf->getActLanguageAbbr()|lower}]
[{assign var="sMerchantId" value=$oViewConf->getPaymorrowMerchantId()}]
[{if $sMerchantId}]
    [{oxstyle include=$sStylesUrl}]
    [{oxstyle}]
    [{oxscript include=$sScriptUrl}]
    [{oxscript add="pmj(document).ready(function() {
            try {
                pmRegisterValidation(
                    'myedit',
                    {
                        priceSurchargeValue : 'editval[oxpayments__oxaddsum]',
                        priceSurchargeType  : 'editval[oxpayments__oxaddsumtype]',
                        orderAmountFrom     : 'editval[oxpayments__oxfromamount]',
                        orderAmountTo       : 'editval[oxpayments__oxtoamount]',
                        selectedMethod      : 'editval[oxpayments__oxchecked]',
                        sortOrder           : 'editval[oxpayments__oxsort]'
                    },
                    { langcode: '`$sLanguageCode`' }
                );
            } catch (exception) {}
        });"}]
    [{oxscript}]
[{/if}]