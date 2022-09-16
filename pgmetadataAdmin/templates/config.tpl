{meta_htmlmodule js 'pgmetadataAdmin', 'pgmd_admin.js'}
{meta_htmlmodule css 'pgmetadataAdmin',  'pgmd_admin.css'}

<h1>{@pgmetadataAdmin~admin.config.title@}</h1>

<div id="pgmetadata"
     data-check-url="{jurl 'pgmetadataAdmin~pgmdadmin:check'}"></div>


<div id="pgmd-message-panel">

    <div class="pgmd-ok_profile_ok_view">
        <p>{@pgmetadataAdmin~admin.message.pgmetadata.ok@}</p>
        <p>{@pgmetadataAdmin~admin.message.pgmetadata.ok.into.profile@}</p>
    </div>

    <div class="pgmd-bad_profile">
        {@pgmetadataAdmin~admin.error.bad_profile.html@}</p>
    </div>

    <div class="pgmd-ok_profile_no_view">
        {@pgmetadataAdmin~admin.error.ok_profile_no_view.html@}

    </div>
    <div class="pgmd-no_profile_ok_view">
        <p>{@pgmetadataAdmin~admin.message.pgmetadata.ok@}</p>
        <p>{@pgmetadataAdmin~admin.message.pgmetadata.ok.into.lizmap@}</p>
    </div>
    <div class="pgmd-no_profile_no_view">
        {@pgmetadataAdmin~admin.error.no_profile_no_view.html@}
    </div>

    <div class="pgmd-no_database">
        {@pgmetadataAdmin~admin.error.no_database.html@}
    </div>

    <div class="pgmd-ok_profile_ok_view pgmd-ok_profile_no_view pgmd-bad_profile">
        <button id="pgmd-modify-credentials">{@pgmetadataAdmin~admin.button.modify.credentials@}</button>
    </div>
    <div class="pgmd-no_profile_ok_view pgmd-no_profile_no_view pgmd-no_database">
        <button id="pgmd-create-credentials">{@pgmetadataAdmin~admin.button.new.credentials@}</button>
    </div>

    <div class="pgmd-saving">
        <p><img src="{$j_basepath}themes/default/css/images/download_layer.gif" alt="" />
            {@pgmetadataAdmin~admin.form.message.wait@}
        </p>
    </div>
</div>

<form id="pgmd-profile" action="{jurl 'pgmetadataAdmin~pgmdadmin:save'}"
      autocomplete="off"
      method="POST"
      class="pgmd-modify_credentials pgmd-create_credentials"
    >
    <fieldset><legend>{@pgmetadataAdmin~admin.form.title@}</legend>

    <p>{@pgmetadataAdmin~admin.form.message.description@}</p>

    <div>
        <label>{@pgmetadataAdmin~admin.form.host.label@}</label>
        <input type="text" id="pgHost" name="pgHost"
               value="{$profile['host']|eschtml}" required="required"/>
    </div>

    <div>
        <label>{@pgmetadataAdmin~admin.form.port.label@}</label>
        <input type="text" id="pgPort" name="pgPort"
               value="{$profile['port']|eschtml}"  required="required"/>
    </div>

    <div>
        <label>{@pgmetadataAdmin~admin.form.database.label@}</label>
        <input type="text" id="pgDatabase" name="pgDatabase"
               value="{$profile['database']|eschtml}" required="required"/>
    </div>

    <div>
        <label>{@pgmetadataAdmin~admin.form.user.label@}</label>
        <input type="text" id="pgUser" name="pgUser" autocomplete="off"
               value="{$profile['user']|eschtml}" required="required" />
    </div>

    <div>
        <label>{@pgmetadataAdmin~admin.form.password.label@}</label>
        <input type="password" id="pgPassword"  name="pgPassword" autocomplete="off"
               value="{$profile['password']|eschtml}" required="required" />
    </div>
        <p>{@pgmetadataAdmin~admin.form.message.all.required@}</p>
    <div>
        <button id="pgmd-button">{@jelix~ui.buttons.save@}</button>
    </div>
    </fieldset>
</form>