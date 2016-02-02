{% extends "cpanel.volt" %}

{% block content %}
    {% set title='Edit Journal' %}

    {{ form.render() }}
{% endblock %}