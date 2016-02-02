{% extends "cpanel.volt" %}

{% block content %}
    {% set title='My Journals' %}

    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="floatright">
                {{ paginate(pager) }}
            </div>
            <h3 class="panel-title">My Journals</h3>
        </div>
        <div class="panel-body">
            <div class="buttons">
                <a class="btn btn-success" href="{{ url.routeFromHere(['action': 'edit']) }}">+ Post New Journal</a>
            </div>

            <br>
            <table class="table">
                <colgroup>
                    <col width="15%">
                    <col width="85%">
                </colgroup>
                <tbody>
                {% for row in pager %}
                    <tr class="input">
                        <td>
                            <div class="btn-group">
                                <a class="btn btn-sm btn-default" href="{{ url.routeFromHere(['action': 'edit', 'id': row.id]) }}">Edit</a>
                                <a class="btn btn-sm btn-danger" href="{{ url.routeFromHere(['action': 'delete', 'id': row.id]) }}" onclick="return confirm('Are you sure you want to delete this item?');">Delete</a>
                            </div>
                        </td>
                        <td>
                            <a href="{{ url.get('journal/'~row.id) }}">{{ row.subject }}</a>
                            {% if row.id == featured_journal %}<span class="label label-primary">Featured</span>{% endif %}
                            <br>
                            <span class="fontsize12">Posted {{ fa.formatDate(row.created_at) }}</span>
                        </td>
                    </tr>
                {% endfor %}
                </tbody>
            </table>
        </div>
    </div>

{% endblock %}