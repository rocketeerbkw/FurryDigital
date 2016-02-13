{% set title='Compose New Message' %}

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">{{ title }}</h3>
    </div>
    <div class="panel-body">
        <div class="buttons">
            <a class="btn btn-sm btn-default" href="{{ url.routeFromHere(['action': 'pms']) }}">&laquo; Back to Messages</a>
        </div>
        <br>

        {{ form.render() }}
    </div>
</div>