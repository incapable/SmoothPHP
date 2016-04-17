<!DOCTYPE html>
<html>
<head>
    <title>{block "title"}Random title{/block}</title>
</head>
<body>
{block "main"} {/block}
{if getvar($test) == 5}
    {doStuff($bla, getAnotherVar($bla))}
{/if}
</body>
</html>