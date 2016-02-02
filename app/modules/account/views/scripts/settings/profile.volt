{% extends "cpanel.volt" %}

{% block content %}
    {% set title='Edit Profile Page' %}

    {{ form.render() }}
{% endblock %}