<p>Статус ресурса {$ResourceName} был изменен.</p>

<p>
	<strong>Ресурс:</strong> {$ResourceName}<br/>
	<strong>Статус:</strong> {$ResourceStatus}<br/>
	{if $ResourceStatusReason}
	<strong>Причина:</strong> {$ResourceStatusReason}<br/>
	{/if}
</p>

<a href="{$ScriptUrl}">Войти в {$AppTitle}</a>
