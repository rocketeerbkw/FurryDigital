{# for notify_key, notify_info in notifications #}

{% if notify_info['count'] > 0 %}
    <ul>
        {% for row in notify_info['records'] %}
            <li><input type="checkbox" name="ids[]" value="{{ row.identifier_id }}"> <a href="{{ url.route(['module': 'account', 'controller': 'tickets', 'action': 'view', 'id': row.ticket_id]) }}" target="_blank"><strong>{{ row.ticket.getIssueTypeName() }}</strong></a><br>
                An administrator has posted an update to this ticket!
        {% endfor %}
    </ul>
{% endif %}