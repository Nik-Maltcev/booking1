<p>{$ParticipantDetails}</p>

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

<p><strong>Номер ссылки:</strong> {$ReferenceNumber}</p>

<a href="{$ScriptUrl}/{$ReservationUrl}">Просмотреть это бронирование</a>
|
<a href="{$ScriptUrl}">Войти в {$AppTitle}</a>
