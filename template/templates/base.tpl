<!DOCTYPE html>
<html>
    <head>
        <title>{block "title"}Test site{/block}</title>
        {$assets->addCSS('style.css')}
        {css}
    </head>
    <body>
        {block "main"} {/block}
        {javascript}
    </body>
</html>