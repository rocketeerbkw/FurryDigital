{# for notify_key, notify_info in notifications #}

{% if notify_info['count'] > 0 %}
    <ul>
    {% for row in notify_info['records'] %}
        <li><input type="checkbox" name="{{ notify_key }}[]" value="{{ row.favorite_id }}"><a href="{{ url.named('user_view', ['username': row.user.short]) }}"><strong>{{ row.user.username|e }}</strong></a> favorited <strong>"<a href="{{ url.named('upload_view', ['id': row.upload_id]) }}">{{ row.upload.title|e }}</a>"</strong></li>
    {% endfor %}
    </ul>
{% endif %}