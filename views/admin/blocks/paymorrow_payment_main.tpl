[{if $edit and $edit->isPaymorrowActiveAndMapped()}]
    [{include file="paymorrow_paymnet_validation.tpl"}]
[{/if}]
[{$smarty.block.parent}]