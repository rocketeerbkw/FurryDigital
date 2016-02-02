{% extends "cpanel.volt" %}

{% block content %}
    {% set title='Account Settings' %}

    {{ form.render() }}
{% endblock %}