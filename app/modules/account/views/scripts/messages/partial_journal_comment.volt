{# for notify_key, notify_info in notifications #}

{% if notify_info['count'] > 0 %}
<ul>
    {% for row in notify_info['records'] %}
    <li><input type="checkbox" name="{{ notify_key }}[]" value="{{ row.comment_id }}"><a href="{{ url.named('user_view', ['username': row.comment.user.lower ]) }}"><b>{{ row.comment.user.username|e }}</b></a> replied to {% if row.comment.is_comment_reply %}your comment on{% endif %} "<a href="{{ url.named('journal_view', ['id': row.comment.journal_id]) }}#cid:{{ row.comment_id }}">{{ row.comment.journal.title|e }}</a></li>
    {% endfor %}
</ul>
{% endif %}
