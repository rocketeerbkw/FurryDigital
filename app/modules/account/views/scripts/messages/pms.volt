{% set title='Notes' %}

<div class="row">
    <div class="col-md-4">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="btn-toolbar">

                </div>

                {% for message in messages %}
                <a class="media" href="{{ url.routeFromHere(['id': message.id]) }}">
                    <div class="media-left">
                    {% if message.is_read %}
                        <i class="fa fa-envelope-o"></i>
                    {% else %}
                        <i class="fa fa-envelope"></i>
                    {% endif %}
                    </div>
                    <div class="media-body">
                        <h4 class="media-heading">
                            {% if folder == "outbox" %}{{ message.sender.username|e }}{% else %}{{ message.recipient.username|e }}{% endif %}
                            <br><small>{{ fa.formatDate(message.created_at) }}</small>
                        </h4>
                        {{ parser.truncate(message.message) }}
                    </div>
                </a>
                {% endfor %}
            </div>
        </div>
    </div>
    <div class="col-md-8">

    </div>
</div>