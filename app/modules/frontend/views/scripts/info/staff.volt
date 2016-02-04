{% set title='Staff' %}

<h1>{{ title }}</h1>

<p class="staffdesc">Do you need help or assistance?</p>
<p class="staffdesc">Before contacting someone from our support team for assistance please <a href="{{ url.route(['module': 'account', 'controller': 'tickets', 'action': 'edit']) }}"><b>create a Trouble Ticket</b></a>! Staff are instructed to answer and respond to tickets first, and do not work from notes.</p>

<h2>Full Roster Coming Soon</h2>
<p>A full list of our staff and their roles is coming soon. Check back for more!</p>

<h2>Join the Team!</h2>
<p>Are you interested in becoming a part of the {{ config.application.name }} team? Send us an e-mail at {{ mailto('apply@furry.digital') }}</p>