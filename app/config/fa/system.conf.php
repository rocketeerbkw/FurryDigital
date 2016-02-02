<?
/**
 * FloofClub Legacy Configuration File
 *
 * TODO: These all need documentation!
 */

return array(
    // Name of the website
    'Site_Name'         => "FloofClub",

    // Primary email which the website shows for things like errors or support...
    'Master_Email'      => "admin@floof.club",

    // Primary format for displaying time
    'Regular_Date_Format' => "m-d-y",

    // A short format for displaying time
    'Short_Date_Format' => "m.d.y",

    // A long format for displaying time
    'Long_Date_Format'  => "F jS, Y h:i A",

    // Format for today's time/date
    'Today_Date_Format' => "F jS, Y",

    // Format to show just time
    'Time_Format'       => "h:i",

    // System modes:
    // 0 = Normal operation
    // 1 = Read Only Mode: Disables most things that alter the database for users Only... browsing is left open.
    // 2 = Administrative mode: Main system is locked out to users, admins still have access to all functions.
    // 3 = Owners Only Mode: Only my account (Alkora) will work, this is useful while i try to implement a new feature.
    // 4 = Total Lock Down: JUUUUUUST incase, this could be useful incase an administrative account gets hijacked...
    // 5 = Code Update mode: This will be activated by coders when the site needs to be updated code-wise. User access denied in header.inc.php Offline page displayed
    // 6 = File readonly mode: Disables uploading and deleting files to and from the site. Database access still unrestricted, so people can post comments and journals, just not upload, edit and delete submissions and avatars.
    'System_Mode'       => "0",

    // 0 = Account registration enabled
    // 1 = Account registration disabled
    'Disable_Registration' => "0",

    // Explanation as to why the registration is disabled. May be empty, in which case the page will simply say that the reg is offline.
    'Disable_Registration_Reason' => "User registration is down until we migrate our email server off-site.",

    // This message will be shown at the top, under the header image, on every page.
    'Admin_Message'     => "",

    // Use content pipelining. If enabled, galeries and avatar loading will be speeded up due to the use of additional data subdomains, e.g
    // http://a.d.facdn.net
    // http://b.d.facdn.net etc..
    'Use_content_pipelining' => "No",

    // This value determines what the MAXIMUM amount (in pixels) the width and height of thumbnails can be.
    'Thumbnail_Image_Sizes' => "120",

    // Every submission makes a smaller sized version, not the thumbnail, this value determines what the MAXIMUM amount (in pixels) the width and height can be.
    'Smaller_Image_Sizes' => "300",

    // This will limit avatar files to this filesize (in KB)
    'Avatar_Filesize'   => "55",

    // A list of words separated by whitespace. Prevents registration of accounts the names of which contain any of the listed words.
    'Account_Name_Blocklist' => "n1gg3r nigger niggger nigggger niggggger Nigger nigga Nigga niglet cunt fuck Fuck fucking Fucking  beaner gook dogcock piche Piche Dragoneer DRAGONEER dragoneer Carenath  arshesnei qmelon Surgat net-cat affinity faggot Faggot f4gg0t floofclub faadmin admin Admin niggar niggah nigga furfa-g f-urfag -furfag fu-rfag fur-fag furfa-g furf-ag cerbrus cerberusnl asshole a5shole as5hole a55hole 4sshole cocks c0cks vagina v4gina v4g1n4 v4g1na v4gin4 yiffinhell yiff-in-hell inkbunny sofurry weasyl moderator Moderator imvu",

    // List of junk or temporary email providers, one item per line.
    // !!!! You damn well better know what you're doing before updating this list !!!!
    'Junk_Email_Providers' => str_replace("|", "\n", 'goat.si|redchan.it|getairmail.com|nigge.rs|vidchart.com|0815.ru|drdrb.net|0clickemail.com|0wnd.net|0wnd.org|10minutemail.com|1chuan.com|1zhuan.com|20minutemail.com|2prong.com|3d-painting.com|4warding.com|4warding.net|4warding.org|5mail.in|6paq.com|9ox.net|a-bc.net|abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyzabcdefghijk.com|akerd.com|amilegit.com|anonbox.net|anonymail.dk|anonymbox.com|anonymousspeech.com|antichef.com|antichef.net|antispam.de|armyspy.com|auoie.com|baxomale.ht.cx|beefmilk.com|binkmail.com|bio-muesli.net|blogmyway.org|bobmail.info|bodhi.lawlita.com|bofthew.com|brefmail.com|bsnow.net|bugmenot.com|bumpymail.com|buyusedlibrarybooks.org|casualdx.com|chammy.info|chogmail.com|consumerriot.com|cool.fr.nf|cool.fr.nf|correo.blogos.net|cosmorph.com|courriel.fr.nf|courrieltemporaire.com|curryworld.de|cust.in|cuvox.de|dacoolest.com|dandikmail.com|dayrep.com|deadaddress.com|deadesu.com|despam.it|devnullmail.com|dfgh.net|digitalsanctuary.com|discardmail.com|discardmail.de|disposableaddress.com|disposeamail.com|disposemail.com|dispostable.com|dodgeit.com|dodgit.com|dodgit.org|dontreg.com|dontsendmespam.de|dotmsg.com|drdrb.com|dudmail.com|dump-email.info|dumpyemail.com|e4ward.com|e4ward.com|einrot.com|email-jetable.biz.st|email60.com|emailias.com|emailinfive.com|emailmiser.com|emailtemporario.com.br|emailwarden.com|ephemail.net|explodemail.com|eyepaste.com|fakebox.org|fakeinbox.com|fakeinformation.com|fakemailz.com|fastacura.com|fastmail.net|fizmail.com|fleckens.hu|footard.com|forgetmail.com|frapmail.com|garliclife.com|get1mail.com|getonemail.com|getonemail.net|girlsundertheinfluence.com|gishpuppy.com|great-host.in|greensloth.com|grr.la|gsrv.co.uk|guerillamail.biz|guerillamail.com|guerillamail.net|guerillamail.org|guerrillamail.biz|guerrillamail.com|guerrillamail.net|guerrillamail.org|guerrillamailblock.com|gustr.com|haltospam.com|hmamail.com|hotpop.com|hush.com|ieatspam.eu|ieatspam.info|ihateyoualot.info|imails.info|imstations.com|inboxclean.com|inboxclean.org|incognitomail.com|incognitomail.net|incognitomail.org|ipoo.org|irish2me.com|jetable.com|jetable.fr.nf|jetable.net|jetable.org|jnxjn.com|jourrapide.com|junk1e.com|kasmail.com|kaspop.com|keepmymail.com|klzlk.com|kulturbetrieb.info|kurzepost.de|lifebyfood.com|link2mail.net|litedrop.com|loginz.org|lookugly.com|lopl.co.cc|lovemeleaveme.com|lr78.com|maboard.com|mail.by|mail.mezimages.net|mail4trash.com|mailbidon.com|mailcatch.com|maileater.com|mailexpire.com|mailin8r.com|mailinater.com|mailinator.com|mailinator.net|mailinator.org|mailinator2.com|mailincubator.com|mailismagic.com|mailme.lv|mailmetrash.com|mailnator.com|mailnull.com|mailquack.com|mailseal.de|mailslapping.com|mailtothis.com|mailzilla.org|mbx.cc|mega.zik.dj|meltmail.com|mierdamail.com|mintemail.com|moncourrier.fr.nf|monemail.fr.nf|monmail.fr.nf|mt2009.com|mx0.wwwnew.eu|mycleaninbox.net|mytrashmail.com|neverbox.com|no-spam.ws|nobulk.com|noclickemail.com|nogmailspam.info|nomail.xl.cx|nomail2me.com|nospam.ze.tc|nospam4.us|nospamfor.us|nowmymail.com|objectmail.com|obobbo.com|oneoffmail.com|onewaymail.com|opayq.com|ordinaryamerican.net|otherinbox.com|owlpic.com|pjjkp.com|pookmail.com|proxymail.eu|punkass.com|putthisinyourspamdatabase.com|quickinbox.com|rcpt.at|receiveee.chickenkiller.com|receiveee.com|recode.me|recursor.net|recyclemail.dk|regbypass.comsafe-mail.net|rhyta.com|rmqkr.net|safetymail.info|sandelf.de|sayawaka-dea.info|saynotospams.com|selfdestructingmail.com|sendspamhere.com|sharklasers.com|shiftmail.com|shitmail.me|shortmail.net|skeefmail.com|slopsbox.com|smellfear.com|snakemail.com|sneakemail.com|sofort-mail.de|sogetthis.com|soodonims.com|spam.la|spam.su|spamavert.com|spambob.net|spambob.org|spambog.com|spambog.de|spambog.ru|spambox.info|spambox.us|spamcannon.com|spamcannon.net|spamcero.com|spamcorptastic.com|spamcowboy.com|spamcowboy.net|spamcowboy.org|spamday.com|spamex.com|spamfree24.com|spamfree24.de|spamfree24.eu|spamfree24.info|spamfree24.net|spamfree24.org|spamgourmet.com|spamgourmet.net|spamgourmet.org|spamherelots.com|spamhereplease.com|spamhole.com|spamify.com|spaminator.de|spamkill.info|spaml.com|spaml.de|spammotel.com|spamobox.com|spamspot.com|spamthis.co.uk|spamthisplease.com|speed.1s.fr|speed.1s.fr|stealth-mode.net|stop-my-spam.com|supergreatmail.com|superrito.com|suremail.info|suremail.info|tagyourself.com|teleworm.us|tempalias.com|tempe-mail.com|tempemail.biz|tempemail.com|tempemail.net|tempinbox.co.uk|tempinbox.com|tempomail.fr|temporaryemail.net|temporaryinbox.com|thankyou2010.com|thisisnotmyrealemail.com|thisisnotmyrealemail.com|throwawayemailaddress.com|tilien.com|tmailinator.com|tormail.org|tradermail.info|trash-amil.com|trash-mail.at|trash-mail.com|trash-mail.de|trash2009.com|trashdevil.com|trashdevil.de|trashinbox.com|trashmail.at|trashmail.com|trashmail.de|trashmail.me|trashmail.net|trashmailer.com|trashymail.com|trashymail.net|tyldd.com|uggsrock.com|veryrealemail.com|walala.org|wegwerfmail.de|wegwerfmail.net|wegwerfmail.org|wh4f.org|whopy.com|whyspam.me|wilemail.com|willselfdestruct.com|winemaven.info|wronghead.com|wuzupmail.net|xoxy.net|yaied.com|yogamaven.com|yopmail.com|yopmail.fr|yopmail.net|yuurok.com|yxzx.net|zippymail.info|polieduc.com.br|promocionsrustiques.com|oss-concept.ch|wahaha.com.my|wlanacion.com.ar|turbotax2013site.com|vanaheim.co.za|latinexo.com|sexychiamamisubito.net|condize.tk|exend.ro|drupal-pt.org|tahanan201.com|doge.to|keeplisted.org|exend.ro|xengarden.net|hobisedekah.com|menggoda.fi|snyderonline.info|wendyandnick.net|spam4.me'),
);