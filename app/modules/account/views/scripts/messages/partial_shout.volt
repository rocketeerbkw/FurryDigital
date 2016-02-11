{# for notify_key, notify_info in notifications #}

{% if notify_info['count'] > 0 %}
<ul>
    {% for row in notify_info['records'] %}
        <li><input type="checkbox" name="{{ notify_key }}[]" value="{{ row.shout.id }}"><a href="{{ url.named('user_view', ['username': row.shout.sender.lower]) }}"><strong>{{ row.shout.sender.username|e }}</strong></a> left a shout</li>
    {% endfor %}
</ul>
{% endif %}