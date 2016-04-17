{include "base.tpl"}

{block "main"}
    {$ctrl->testVar}
    <br />
    {$ctrl->test()}
    <br />
    {pow(2,3) * 5}
    <br />
    {$ctrl->testInt() == 10}
{/block}