<h1>{{ subject }}</h1>

<p>A Lost Password recovery request has been started on {{ config.application.name }}. This one-time recovery code will allow you to log in to the {{ config.application.name }} homepage, where you can change your password if needed.</p>

<p>If you did not request that this code be sent to you, please ignore this e-mail. Don't worry; this is the only e-mail address that received this code.</p>

<p>To recover your account, click the link below:</p>

<p>{{ url.route(['module': 'account', 'controller': 'recover', 'action': 'verify', 'id': id, 'code': code])|link }}</p>