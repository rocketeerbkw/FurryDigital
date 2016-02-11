{# for notify_key, notify_info in notifications #}

{% if notify_info['count'] > 0 %}
<ul>
    {% for row in notify_info['records'] %}
    <li><input type="checkbox" name="ids[]" value="{{ row.identifier_id }}"> <strong>"<a href="{{ url.named('journal_view', ['id': row.journal_id]) }}" target="_blank">{{ row.journal.subject|e }}</a>"</strong> posted by <a href="{{ url.named('user_view', ['username': row.journal.user.lower ]) }}" target="_blank"><strong>{{ row.journal.user.username|e }}</strong></a></li>
    {% endfor %}
</ul>
{% endif %}

