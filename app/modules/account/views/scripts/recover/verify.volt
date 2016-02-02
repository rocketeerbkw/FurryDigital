{% set title='Recover Your Account' %}

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">{{ title }}</h3>
    </div>
    <div class="panel-body">
        <p>Hello! You have been temporarily logged in to the {{ config.application.name }} web site. Your recovery code can only be used once before needing to request a new one.</p>

        <p>If you do not remember your previous password, you should visit your Account Settings page to change it now!</p>

        <a class="btn btn-primary" href="{{ url.route(['module': 'account', 'controller': 'settings']) }}">Update Account Settings</a>
    </div>
</div>