{% extends "cpanel.volt" %}

{% block content %}
    {% set title='Edit Journal Template' %}

    {{ form.render() }}
{% endblock %}