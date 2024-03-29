{% extends "cpanel.volt" %}

{% block content %}
    {% set title='My Favorites' %}

    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="floatright">
                {{ paginate(pager) }}
            </div>
            <h3 class="panel-title">My Favorites</h3>
        </div>
        <div class="panel-body">
            <center class="flow gallery with-titles thumb-size-200">
            {% for fav_row in pager %}
                {% set row = fav_row.upload %}
                <b>
                    <u>
                        <s>
                            <a href="{{ url.get('view/'+row.id) }}"><img alt="thumbnail" src="{{ row.getThumbnailUrl() }}"></a>
                        </s>
                    </u>
                    <span class="desc"><a href="{{ url.routeFromHere(['action': 'delete', 'id': row.id]) }}" onclick="return confirm('Are you sure you want to delete this favorite?');" class="hlp">[ Remove ]</a></span>
                </b>
            {% endfor %}
            </center>
        </div>
    </div>

{% endblock %}