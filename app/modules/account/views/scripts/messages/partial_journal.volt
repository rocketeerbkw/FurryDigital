{# for notify_key, notify_info in notifications #}

{% if notify_info['count'] > 0 %}
<ul>
    {% for row in notify_info['records'] %}
    <li><input type="checkbox" name="{{ notify_key }}[]" value="{{ row.journal_id }}"><strong>"<a href="{{ url.named('journal_view', ['id': row.journal_id]) }}">{{ row.journal.subject|e }}</a>"</strong> posted by <a href="{{ url.named('user_view', ['username': row.journal.user.short ]) }}"><strong>{{ row.journal.user.username|e }}</strong></a></li>
    {% endfor %}
</ul>
{% endif %}

