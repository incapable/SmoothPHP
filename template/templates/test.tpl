{include "base.tpl"}

{block "main"}
    New body with {$var|dechex}! :D
    {if $var == 255}
        It is!
    {/if}
{/block}