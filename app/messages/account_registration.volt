<h1>{{ subject }}</h1>

<p>Dear {{ form_data['username']|e }},</p>
<p>Thank you for registering on {{ config.application.name }}. Before we can activate your account, you must confirm that the request was sent by you, a human (and not an automated script or a program).</p>
<p>To proceed with the registration, please visit the URL below. You will only need to complete this step once.</p>

<p>{{ url.route(['module': 'account', 'controller': 'register', 'action': 'verify', 'code': confirmation_code])|link }}</p>