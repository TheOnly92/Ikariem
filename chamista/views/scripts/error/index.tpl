<div id="information" class="dynamic"></div>
{if count($dynamics) > 0}
	{assign var="fileName" value=`$dynamics.type`}
	{include file="dynamics/$fileName.tpl"}
{/if}
<div id="mainview">
    <div class="buildingDescription">
        <h1>Error!</h1>
    </div>
    <div class="contentBox01h">
        <h3 class="header"><span class="textLabel">Error message(s)</span></h3>
        <div class="content">
            <ul class="error">
            	{foreach from=$messages item=message}
					<li>{$message}</li>
				{/foreach}
            </ul>
        </div>
        <div class="footer"></div>
    </div>
</div>