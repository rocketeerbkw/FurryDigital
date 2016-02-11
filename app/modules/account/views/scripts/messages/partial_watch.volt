{# for notify_key, notify_info in notifications #}

{% if notify_info['count'] > 0 %}
<ul>
    {% for row in notify_info['records'] %}
        <li><input type="checkbox" name="ids[]" value="{{ row.identifier_id }}"> <a href="{{ url.named('user_view', ['username': row.watch.user.lower]) }}" target="_blank"><img src="{{ row.watch.user.getAvatar() }}"> {{ row.watch.user.username|e }}</a></li>
    {% endfor %}
</ul>
{% endif %}