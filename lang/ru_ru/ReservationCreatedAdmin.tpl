<p>Создано новое бронирование.</p>

<p><strong>Детали бронирования:</strong></p>

<p>
	<strong>Пользователь:</strong> {$UserName}<br/>
	<strong>Начало:</strong> {formatdate date=$StartDate key=reservation_email}<br/>
	<strong>Окончание:</strong> {formatdate date=$EndDate key=reservation_email}<br/>
	<strong>Название:</strong> {$Title}<br/>
	<strong>Описание:</strong> {$Description|nl2br}
	{if $Attributes|default:array()|count > 0}
		<br/>
	    {foreach from=$Attributes item=attribute}
			<div>{control type="AttributeControl" attribute=$attribute readonly=true}</div>
	    {/foreach}
	{/if}
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

{if $RequiresApproval}
	<p>* Как минимум один из забронированных ресурсов требует одобрения перед использованием. Это бронирование будет ожидать одобрения. *</p>
{/if}

{if count($RepeatRanges) gt 0}
    <br/>
    <strong>Бронирование повторяется в следующие даты ({$RepeatRanges|default:array()|count}):</strong>
    <br/>
	{foreach from=$RepeatRanges item=date name=dates}
	    {formatdate date=$date->GetBegin()}
	    {if !$date->IsSameDate()} - {formatdate date=$date->GetEnd()}{/if}
	    <br/>
	{/foreach}
{/if}

{if $Accessories|default:array()|count > 0}
    <br />
       <strong>Оборудование ({$Accessories|default:array()|count}):</strong>
       <br />
    {foreach from=$Accessories item=accessory}
        ({$accessory->QuantityReserved}) {$accessory->Name}
        <br/>
    {/foreach}
{/if}

<p><strong>Номер ссылки:</strong> {$ReferenceNumber}</p>

<a href="{$ScriptUrl}/{$ReservationUrl}">Просмотреть это бронирование</a>
|
<a href="{$ScriptUrl}">Войти в {$AppTitle}</a>
