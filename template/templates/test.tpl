{include "base.tpl"}

{block "main"}
    <div>{$language->hello_world}</div>
    <div>
        {if !$form->isValid()}
            {foreach $form->getErrorMessages() as $error}
                <span style="color: red;">{$error}</span>
            {/foreach}
        {/if}
    </div>
    {$form}
{/block}