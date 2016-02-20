{% extends "cpanel.volt" %}

{% block navigation %}
    <h2>Content Upload</h2>
    <div class="p10">Here, your new work of art, brilliant audio piece, inspiring written work or picture begins its journey to become a full-fledged uploaded content!</div>
{% endblock %}

{% block content %}
    {% set title='Upload' %}

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Select Content Type</h3>
        </div>
        <div class="panel-body">
            <dl>
            {% for type_key, type_info in types %}
                <dt><a href="{{ url.routeFromHere(['type': type_key]) }}">{{ type_info['name'] }}</a> &raquo;</dt>
                <dd>{{ type_info['description'] }}</dd>
            {% endfor %}
            </dl>
        </div>
    </div>
{% endblock %}