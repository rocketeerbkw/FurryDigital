{% extends "cpanel.volt" %}

{% block content %}
    {% set title='My Watches' %}

    <div class="container-item-top">
        <div class="clearfix">
            <div class="floatright">
                {{ paginate(pager) }}
            </div>
            <h3>My Watches</h3>
        </div>
    </div>

    <div class="container-item-bot-last">
        <div class="clearfix">
        {% for row in pager %}
            <div style="height:150px; width: 20%; float: left;">
                <a href="{{ url.get('user/'~row.user.lower) }}" target="_BLANK"><img src="{{ row.user.getAvatar() }}" alt="{{ row.user.lower }}"><br>
                    <span style="font-size:10px"><strong>{{ row.user.username }}</strong></span>
                </a>
                {# TODO: Update this link when available. #}
                <a style="font-size:10px;text-decoration:none" href="{{ url.get('unwatch/'~row.user.lower) }}"><strong>[ Remove ]</strong></a>
            </div>
        {% endfor %}
        </div>
    </div>

{% endblock %}