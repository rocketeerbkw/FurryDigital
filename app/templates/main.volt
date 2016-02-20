<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

    <meta name="viewport" content="width=device-width">
    <meta name="viewport" content="width=768, user-scalable=yes">

    <meta name="description" content="FurryDigital, furry art, media and more!">
    <meta name="keywords" content="furry, furries, furry art, fursuits, fursuiting, anthro, anthropomorphic">

    <meta http-equiv="X-UA-Compatible" content="IE=9; IE=EDGE">

    <meta name="ero_verify" content="1d42658d34f4c71f70af90b130f37c66">

    {% for property, content in meta_tags %}
        <meta property="{{ property }}" content="{{ content }}">
    {% endfor %}

    {% if title is empty %}{% set title = "Welcome" %}{% endif %}
    <title>{{ title }} -- {{ config.application.name }}</title>

    <link rel="icon" href="{{ static_url('icon_'~constant('APP_APPLICATION_ENV')~'.png') }}" type="image/png">
    <link rel="shortcut icon" href="{{ static_url('icon_'~constant('APP_APPLICATION_ENV')~'.png') }}" type="image/png">

    {% block header_css %}
        {{ assets.outputCss('header_css') }}
    {% endblock %}
    {% block header_js %}
        <script type="text/javascript">
            var app_static_url = '{{ static_url('') }}';

            var sfw_cookie_name = '{{ app.sfw_cookie_name|escape_js }}';
            var cookie_domain = '{{ config.application.cookie_domain|escape_js }}';

            var page_has_mature_content = {% if app.page_has_mature_content %}true{% else %}false{% endif %};
        </script>

        {{ assets.outputJs('header_js') }}

        <!-- Start Google Analytics -->
        <script type="text/javascript">
            (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
            })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

            ga('create', '{{ config.application.analytics_code }}', 'auto');
            ga('send', 'pageview');
        </script>
        <!-- End Google Analytics -->
    {% endblock %}
</head>
<body>
<div id="site">
    <div id="header">
        <a name="top"></a>
        <a href="{{ url.get('') }}"><div class="sitebanner hideonmobile"></div></a>

        {# Menu Top #}

        <nav id="ddmenu" class="block-menu-top">
            <div class="hideondesktop floatleft" style="font-size:24px;padding-top:7px"><a href="{{ url.get('') }}"><strong>FurryDigital</strong></a></div>
            <div class="menu-icon"></div>

            <ul>
                <li class="lileft"><a class="top-heading" href="{{ url.get('browse') }}"><i class="fa fa-th"></i> Browse</a></li>
                <li class="lileft"><a class="top-heading hideondesktop" href="{{ url.get('search') }}"><i class="fa fa-search"></i> Search</a></li>
                <li class="lileft"><a class="top-heading" href="{{ url.get('upload') }}"><i class="fa fa-upload"></i> Upload</a></li>
                <li class="lileft dropdown" role="presentation">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-info-circle"></i>
                        About
                        <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="dropdown-header">Community</li>
                        <li><a href="http://www.facebook.com/furrydigital">Facebook</a></li>
                        <li><a href="http://twitter.com/furrydigital">Twitter</a></li>
                        <li><a href="https://www.youtube.com/channel/UCcvvrapgk2QcfhWidi0ThPQ">YouTube</a></li>

                        <li class="dropdown-header">Support</li>
                        <li><a href="{{ url.get('staff') }}">FurryDigital Staff</a></li>
                        <li><a href="{{ url.get('terms') }}">Terms / Code of Conduct</a></li>
                    </ul>
                </li>

                <li class="lileft hideonmobile">
                    <form id="searchbox" method="post" action="{{ url.get('search') }}">
                        <input type="search" name="q" placeholder="Search" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Search'">
                    </form>
                </li>

                {% if auth.isLoggedIn() %}
                    <li role="presentation" class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="{{ url.named('user_view', ['username': user.lower]) }}" role="button" aria-haspopup="true" aria-expanded="false">
                            {{ user.symbol }} {{ user.username }}
                            <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="dropdown-header">Profile Information</li>
                            <li><a href="{{ url.named('user_view', ['username': user.lower]) }}">My Profile Page</a></li>
                            <li><a href="{{ url.route(['module': 'account', 'controller': 'messages', 'action': 'pms']) }}">My Notes</a></li>
                            <li><a href="{{ url.route(['module': 'account', 'controller': 'journals', 'action': 'edit']) }}">Post a New Journal</a></li>
                            <li><a href="/commissions/{{ user.lower }}/">My Commission Info</a></li>

                            <li class="dropdown-header">Customize</li>
                            <li><a href="{{ url.route(['module': 'account', 'controller': 'settings', 'action': 'index']) }}">Site Settings</a></li>
                            <li><a href="{{ url.route(['module': 'account', 'controller': 'settings', 'action': 'profile']) }}">Edit Profile Page</a></li>
                            <li><a href="{{ url.route(['module': 'account', 'controller': 'avatar']) }}">My Avatar</a></li>
                        </ul>
                    </li>

                    {% if user.hasNotifications() %}
                        <li style="padding-left:8px">
                            <span class="hideondesktop">Messages:<br/></span>
                            {% for notification_key, notification in user.getNotifications() %}
                                {% if notification['show'] %}
                                    <span><a title="{{ notification['title'] }}" href="{{ notification['url'] }}">{{ notification['text'] }}</a></span>
                                {% endif %}
                            {% endfor %}
                        </li>
                    {% endif %}

                    <li class="dropdown" role="presentation">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="{{ url.route(['module': 'account']) }}" role="button" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-cog"></i>
                            My Content
                            <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="{{ url.route(['module': 'account', 'controller': 'uploads']) }}">Uploads</a></li>
                            <li><a href="{{ url.route(['module': 'account', 'controller': 'journals']) }}">Journals</a></li>
                            <li><a href="{{ url.route(['module': 'account', 'controller': 'folders']) }}">Folders</a></li>
                            <li><a href="{{ url.route(['module': 'account', 'controller': 'favorites']) }}">Favorites</a></li>
                            <li><a href="{{ url.route(['module': 'account', 'controller': 'watches']) }}">Watches</a></li>
                            <li><a href="{{ url.route(['module': 'account', 'controller': 'shouts']) }}">Shouts</a></li>
                            <li><a href="{{ url.route(['module': 'account', 'controller': 'tickets']) }}">Trouble Tickets</a></li>
                        </ul>
                    </li>

                    {% if acl.isAllowed('administer all') %}
                        <li>
                            <a class="top-heading" href="{{ url.route(['module': 'admin']) }}"><i class="fa fa-cogs"></i> Admin</a>
                        </li>
                    {% endif %}

                    {% if app.canSeeArt('adult', false) %}
                        <li class="no-sub sfw-toggle {% if app.getSfwCookie() %}active{% endif %}">
                            <a class="top-heading" href="?" title="Toggle to hide Mature and Adult submissions. Effective starting next page load.">
                                <span class="hideonmobile"><i class="fa fa-flag"></i> SFW</span>
                                <span class="hideondesktop">Toggle SFW</span>
                            </a>
                        </li>
                    {% endif %}
                {% endif %}

                <li class="no-sub">
                {% if auth.isLoggedIn() %}
                    <a id="logout-link" class="logout-link" href="{{ url.route(['module': 'account', 'controller': 'logout', 'csrf': csrf.generate('login')]) }}"><i class="fa fa-sign-out"></i> Log Out</a>
                {% else %}
                    <a href="{{ url.route(['module': 'account', 'controller': 'register']) }}"><i class="fa fa-asterisk"></i> Register</a> | <a href="{{ url.route(['module': 'account', 'controller': 'login']) }}"><i class="fa fa-sign-in"></i> Log in</a>
                {% endif %}
                </li>

            </ul>
        </nav>

        {# End Menu Top #}

        {% if flash.hasMessages() %}
            {% for message in flash.getMessages() %}
                <div class="message {{ message['color'] }}">{{ message['text'] }}</div>
            {% endfor %}
        {% endif %}
    </div>

    {# Ad Block Header

    <div class="ads">
        <div class="in">
        {% if app.canSeeArt('adult') %}
            <span id="ad-2" class="ad hidden first"></span >
            <span id="ad-4" class="ad hidden adhidemobile"></span >
        {% else %}
            <span id="ad-1" class="ad hidden first"></span >
            <span id="ad-3" class="ad hidden adhidemobile"></span >
        {% endif %}
        </div>
    </div>

     End Ad Block Header #}

    {% if !app.page_has_mature_content %}
    <div id="ad-extra-flat" class="bg1 leaderboard1 aligncenter">
        <ins class="adsbygoogle"
             style="display:inline-block;width:728px;height:90px"
             data-ad-client="ca-pub-1896533421580439"
             data-ad-slot="6496973379"></ins>
        <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
        </script>
    </div>
    {% endif %}

    <div class="bg1 container-fluid" id="page">
        {{ content() }}
    </div>

    <div id="footer" class="bg5">
        <strong>&copy; 2015-{{ date('Y') }} {{ config.application.name }}</strong> |
        <span class="hideonmobile">
            <a href="https://github.com/FurryDigital/FurryDigital">An Open-Source Project</a> |
            <a href="{{ url.get('terms') }}">Terms of Service / Code of Conduct</a>
        </span>
    </div>
</div>

{% block footer_js %}
    {{ assets.outputJs('footer_js') }}
{% endblock %}
</body>
</html>