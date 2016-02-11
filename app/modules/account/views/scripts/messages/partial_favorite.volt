{# for notify_key, notify_info in notifications #}

{% if notify_info['count'] > 0 %}
    <ul>
    {% for row in notify_info['records'] %}
        <li><input type="checkbox" name="ids[]" value="{{ row.identifier_id }}"> <a href="{{ url.named('user_view', ['username': row.user.lower]) }}" target="_blank"><strong>{{ row.user.username|e }}</strong></a> favorited <strong>"<a href="{{ url.named('upload_view', ['id': row.upload_id]) }}" target="_blank">{{ row.upload.title|e }}</a>"</strong></li>
    {% endfor %}
    </ul>
{% endif %}