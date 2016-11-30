[{include file="headitem.tpl" title="PAYMORROW_ADMIN_TITLE"|oxmultilangassign}]

<form name="transfer" id="transfer" action="[{$oViewConf->getSelfLink()}]" method="post">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="oxid" value="[{$oView->getEditObjectId()}]">
    <input type="hidden" name="cl" value="oxpspaymorrowadminerrorlog">
    <input type="hidden" name="editlanguage" value="[{$editlanguage}]">
</form>

<pre>[{$oView->getPaymorrowErrorLog()}]</pre>
[{include file="bottomitem.tpl"}]