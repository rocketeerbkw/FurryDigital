<div class="p20">
    <h1>{{ title }}</h1>

    {% if render_mode == 'edit' %}
        {{ form.render() }}
    {% else %}
        {{ form.renderView() }}
    {% endif %}
</div>