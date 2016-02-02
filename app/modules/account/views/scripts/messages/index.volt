{% extends "cpanel.volt" %}

{% block content %}
    {% set title='My Messages' %}

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">{{ title }}</h3>
        </div>
        <div class="panel-body">
            <p>You currently have:</p>
            <ul>
                {% for notify in user.getNotifications() %}
                    <li><a href="{{ notify['url'] }}">{{ notify['count'] }} {{ notify['title'] }}</a></li>
                {% endfor %}
            </ul>
        </div>
    </div>
{% endblock %}