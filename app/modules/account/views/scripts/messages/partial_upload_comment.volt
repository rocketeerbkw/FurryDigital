{# for notify_key, notify_info in notifications #}

{% if notify_info['count'] > 0 %}
<ul>
    {% for row in notify_info['records'] %}
        <li><input type="checkbox" name="ids[]" value="{{ row.identifier_id }}"> <a href="{{ url.named('user_view', ['username': row.comment.sender.lower ]) }}" target="_blank"><strong>{{ row.comment.sender.username|e }}</strong></a> replied on <strong>"<a href="{{ url.named('upload_view', ['id': row.comment.upload_id ]) }}#cid:{{ row.comment_id }}" target="_blank">{{ row.comment.upload.title|e }}</a>"</strong></li>
    {% endfor %}
</ul>
{% endif %}