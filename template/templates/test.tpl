Hello world!

{assign $var 1 + 2 * 3 * 4 + 5}
{$var = $secondvar = 8}
{if $var != 6}
    Var does not equal 6, but instead contains {$var}
{/if}