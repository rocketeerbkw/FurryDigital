{# for notify_key, notify_info in notifications #}

{% if notify_info['count'] > 0 %}
<ul>
    {% for row in notify_info['records'] %}
        <li><input type="checkbox" name="{{ notify_key }}[]" value="{{ row.comment_id }}"><a href="{{ url.named('user_view', ['username': row.comment.sender.lower ]) }}"><strong>{{ row.comment.sender.username|e }}</strong></a> replied on <strong>"<a href="{{ url.named('upload_view', ['id': row.comment.upload_id ]) }}#cid:{{ row.comment_id }}">{{ row.comment.upload.title|e }}</a>"</strong></li>
    {% endfor %}
</ul>
{% endif %}