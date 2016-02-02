{% set title="Verification E-mail Sent" %}

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">{{ title }}</h3>
    </div>
    <div class="panel-body">
        <p>An email has been sent to <b>{{ email|e }}</b> with more information.</p>
        <p>Please give it a few moments and check your inbox, then follow the instructions within.</p>
        <p><strong>Note:</strong> This account registration request will claim the username for 24 hours. If you do not confirm the request in this time it will become available again.</p>
    </div>
</div>