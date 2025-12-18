<p>Ваше бронирование для {$ResourceName} начинается {formatdate date=$StartDate key=reservation_email}</p>

<p><strong>Детали бронирования:</strong></p>

<p>
	<strong>Начало:</strong> {formatdate date=$StartDate key=reservation_email}<br/>
	<strong>Окончание:</strong> {formatdate date=$EndDate key=reservation_email}<br/>
	<strong>Ресурс:</strong> {$ResourceName}<br/>
	<strong>Название:</strong> {$Title}<br/>
	<strong>Описание:</strong> {$Description|nl2br}
</p>

<p><strong>Номер ссылки:</strong> {$ReferenceNumber}</p>

<a href="{$ScriptUrl}/{$ReservationUrl}">Просмотреть это бронирование</a>
|
<a href="{$ScriptUrl}">Войти в {$AppTitle}</a>
