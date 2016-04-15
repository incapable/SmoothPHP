{$blName = 'main'}

{block "main"}
    Hi there!
    {$var = (5+3) * pow(2,3)}
    {if 4 == 4}{/if}
    {if 4 != 5}{/if}
{/block}