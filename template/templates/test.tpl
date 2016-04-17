<!DOCTYPE html>
<html>
<head>
    <title>{block "title"}Random title{/block}</title>
</head>
<body>
{block "main"} {/block}
{if $var == 5}
    {2 + 2 * 5}
{/if}
</body>
</html>