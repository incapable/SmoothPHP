{$blName = 'main'}

{block "main"}
    Hi there!
    {$i = 0}
    {while $i != 25}
        {$i}
        {$i = $i + 1}
    {/while}
{/block}