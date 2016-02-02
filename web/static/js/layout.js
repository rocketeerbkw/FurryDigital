/**
 * Common Layout Functions
 */

var today       = new Date();
var expiryyear  = new Date(today.getTime() + 365 * 24 * 60 * 60 * 1000);
var expirymonth = new Date(today.getTime() + 30 * 24 * 60 * 60 * 1000);
var expiryday   = new Date(today.getTime() + 24 * 60 * 60 * 1000);

function getCookie(name)
{
    var reg= new RegExp("; "+name+";|; "+name+"=([^;]*)");
    var matches=reg.exec('; '+document.cookie+';');
    if (matches) return ((matches[1])?unescape(matches[1]):'');
    return null;
}

function setCookie(name,value,expires,path,domain,secure)
{
    document.cookie = name + "=" + escape (value) +
        ((expires) ? "; expires=" + expires.toGMTString() : "") +
        ((path) ? "; path=" + path : "") +
        ((domain) ? "; domain=" + domain : "") +
        ((secure) ? "; secure" : "");

    return (getCookie(name) != null);
}

function deleteCookie(name,path,domain)
{
    if (getCookie(name)!=null)
    {
        document.cookie = name + "=" +
            ((path) ? "; path=" + path : "") +
            ((domain) ? "; domain=" + domain : "") +
            "; expires=Thu, 01-Jan-1970 00:00:01 GMT";
    }
}

// onReady handler
jQuery(function($) {

    // Initialize header window.
    $('div.menu-icon').on('click', function(e) {
        e.preventDefault();
        $(this).siblings('ul').toggle();
        return false;
    });

    init_sfw_button();

    // Loop through all fieldsets, uniquely calculating row alignment for columns.
    $('fieldset').each(function() {

        // 3-column form layouts.
        $(this).find('div.control-group.col33').filter(function(index, element){
            return index % 3 == 0;
        }).addClass('start-of-row');

        // 2-column form layouts.
        $(this).find('div.control-group.col50').filter(function(index, element){
            return index % 2 == 0;
        }).addClass('start-of-row');

    });

    /* Client-side form validation */
    var form_engine_forms = $('form.fa-form-engine');
    if ($.validate && form_engine_forms.length > 0)
    {
        form_engine_forms.each(function() {
            var form_id = '#'+$(this).attr('id');
            $.validate({
                'modules': 'security, file',
                'form': form_id,
                'inputParentClassOnError': ''
            });
        });
    }

    /* Autoselect */
    $('.autoselect').each(function() {
        var active = $(this).attr('rel');
        $(this).find('[rel="'+active+'"]').addClass('active');
    });

});

// Safe for Work (SFW) mode header item
function init_sfw_button() {
    var sfw_toggle_elms = jQuery('.sfw-toggle');
    if(sfw_toggle_elms.length)
    {
        sfw_toggle_elms.on('click', function(e) {
            e.preventDefault();

            var sfw_toggle_elm = jQuery(this).closest('li');
            if (sfw_toggle_elm.hasClass('active'))
            {
                // disable the sfw mode
                deleteCookie(sfw_cookie_name, '/', cookie_domain);
                sfw_toggle_elm.removeClass('active');
            }
            else
            {
                // enable the sfw mode
                setCookie(sfw_cookie_name, '1', expiryyear, '/', cookie_domain);
                sfw_toggle_elm.addClass('active');
            }

            return false;
        });
    }
}