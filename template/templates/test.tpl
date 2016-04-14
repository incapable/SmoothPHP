Hello world!

{assign $var (1 + 2) + 3}
{$var = $secondvar = 8}
{if $var != 6}
    Var does not equal 6, but instead contains {$var}
{/if}