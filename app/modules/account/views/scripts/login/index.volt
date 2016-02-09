<div class="row">
    <div class="col-md-8">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Create an Account</h3>
            </div>
            <div class="panel-body">
                <p>Don't have an account within the {{ config.application.name }} system? Create one below.</p>

                <div class="buttons">
                    <a class="btn btn-primary"><i class="icon-asterisk"></i> Create New Account</a>
                </div>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Log In with Account</h3>
            </div>
            <div class="panel-body">
                <p>If you already have an account, log in with your credentials below.</p>

                {{ form.render() }}
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Sign In with Social Account</h3>
            </div>
            <div class="panel-body">
                <p>Don't want to remember another password? Sign in with your linked social networking account!</p>

                <div class="buttons">
                {% for provider_key, provider_info in external %}
                    <a href="{{ url.routeFromHere(['action': 'oauth', 'provider': provider_key]) }}" class="zocial {{ provider_info['class'] }}">Sign in with {{ provider_info['name'] }}</a></div>
                {% endfor %}
            </div>
        </div>
    </div>
</div>