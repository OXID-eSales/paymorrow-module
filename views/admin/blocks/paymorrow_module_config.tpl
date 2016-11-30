[{* Extends admin backend module settings form to add custom JavaScript and CSS *}]
[{assign var="sBaseLink" value=$oViewConf->getSelfLink()|cat:'&amp;cl=oxpspaymorrowresource&amp;fnc='}]
[{assign var="sStylesUrl" value=$sBaseLink|cat:'getPaymorrowAdminCss'}]
[{assign var="sScriptUrl" value=$sBaseLink|cat:'getPaymorrowAdminJavaScript'}]
[{assign var="sLanguageCode" value=$oViewConf->getActiveInterfaceLanguageAbbr()|lower}]
[{assign var="sMerchantId" value=$oViewConf->getPaymorrowMerchantId()}]
[{if $sMerchantId}]
    [{oxstyle include=$sStylesUrl}]
    [{oxstyle}]
    [{oxscript include="js/libs/jquery.min.js"}]
    [{oxscript include="js/libs/jquery-ui.min.js"}]
    [{oxscript include=$oViewConf->getModuleUrl('oxpspaymorrow', 'out/src/js/jquery.cookie.js')}]
    [{oxscript include=$sScriptUrl}]
    [{oxscript add="jQuery(document).ready(function() {
            var request_data = {
                langcode : '`$sLanguageCode`',
                merchantId: '`$sMerchantId`'
            };
            try {
                pmInitCertificates(
                    'pm_modules_settings',
                    {
                        paymorrowKeysJson  : 'confstrs[paymorrowKeysJson]',
                        merchantPrivateKey : 'confstrs[paymorrowPrivateKey]',
                        merchantPublicKey  : 'confstrs[paymorrowPublicKey]',
                        paymorrowPublicKey : 'confstrs[paymorrowPaymorrowKey]'
                    },
                    true,
                    request_data
                );
                pmInitCertificates(
                    'pm_modules_settings',
                    {
                        paymorrowKeysJson  : 'confstrs[paymorrowKeysJson]',
                        merchantPrivateKey : 'confstrs[paymorrowPrivateKeyTest]',
                        merchantPublicKey  : 'confstrs[paymorrowPublicKeyTest]',
                        paymorrowPublicKey : 'confstrs[paymorrowPaymorrowKeyTest]'
                    },
                    false,
                    request_data
                );
            } catch (exception) {}
    });"}]
    [{oxscript}]
[{/if}]
<div id="pm_modules_settings">
    [{$smarty.block.parent}]
</div>