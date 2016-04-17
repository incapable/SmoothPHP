<!DOCTYPE html>
<html>
<head>
    <title>{block "title"}Random title{/block}</title>
</head>
<body>
{block "main"} {/block}
{if getvar() == 5}
    {doStuff()}
    {pow(2,3)}
{/if}
</body>
</html>