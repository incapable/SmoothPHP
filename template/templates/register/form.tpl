{include "base.tpl"}

{block "main"}
    <div>Register form</div>
    <ul>
        {if !$form->isValid()}
            {foreach $form->getErrorMessages() as $error}
                <li class="error">{$error}</li>
            {/foreach}
        {/if}
    </ul>
    {$form}
{/block}