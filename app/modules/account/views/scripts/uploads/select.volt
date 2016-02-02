{% extends "cpanel.volt" %}

{% block navigation %}
    <h2>Content Upload</h2>
    <div class="p10">Here, your new work of art, brilliant audio piece, inspiring written work or picture begins its journey to become a full-fledged uploaded content!</div>
{% endblock %}

{% block content %}
    {% set title='Upload' %}

    <h2>Select Content Type</h2>

    {% for type_key, type_info in types %}
        <div class="p5t p5b">
            <h3><a href="{{ url.routeFromHere(['type': type_key]) }}">{{ type_info['name'] }}</a> &raquo;</h3>
            <p class="p5">
                {{ type_info['description'] }}
            </p>
        </div>
    {% endfor %}
{% endblock %}