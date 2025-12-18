<p>Вы были приглашены на бронирование.</p>

<p><strong>Детали бронирования:</strong></p>

<p>
	<strong>Начало:</strong> {formatdate date=$StartDate key=reservation_email}<br/>
	<strong>Окончание:</strong> {formatdate date=$EndDate key=reservation_email}<br/>
	<strong>Название:</strong> {$Title}<br/>
	<strong>Описание:</strong> {$Description|nl2br}
</p>

<p>
{if $ResourceNames|default:array()|count > 1}
    <strong>Ресурсы ({$ResourceNames|default:array()|count}):</strong> <br />
    {foreach from=$ResourceNames item=resourceName}
        {$resourceName}<br/>
    {/foreach}
{else}
    <strong>Ресурс:</strong> {$ResourceName}<br/>
{/if}
</p>

{if $ResourceImage}
    <div class="resource-image"><img alt="{$ResourceName|escape}" src="{$ScriptUrl}/{$ResourceImage}"/></div>
{/if}

<p><strong>Номер ссылки:</strong> {$ReferenceNumber}</p>

{if !$Deleted}
<a href="{$ScriptUrl}/{$AcceptUrl}">Принять</a>
|
<a href="{$ScriptUrl}/{$DeclineUrl}">Отклонить</a>
|
{/if}
<a href="{$ScriptUrl}/{$ReservationUrl}">Просмотреть это бронирование</a>
|
<a href="{$ScriptUrl}">Войти в {$AppTitle}</a>
