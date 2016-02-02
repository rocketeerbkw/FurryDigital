{% set title='An Error Occurred' %}

<h2>{{ exception.getMessage() }}</h2>
<p>An error has occurred on this page. Please retry your request again later, or contact the administrators if the issue continues.</p>
<p><small>Exception found on line {{ exception.getLine() }} of {{ exception.getFile() }}</small></p>