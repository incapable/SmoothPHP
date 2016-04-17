{include "base.tpl"}

{block "main"}
    {$ctrl->testVar}
    <br />
    {$ctrl->test()}
    <br />
    {pow(2,3) * 5}
    <br />
    {pow(sqrt(2), $ctrl->getSelf()->testInt())}
{/block}