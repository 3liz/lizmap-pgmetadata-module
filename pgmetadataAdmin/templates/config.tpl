{meta_htmlmodule js 'pgmetadataAdmin', 'pgmd_admin.js'}
{meta_htmlmodule css 'pgmetadataAdmin',  'pgmd_admin.css'}

<h1>{@pgmetadataAdmin~admin.config.title@}</h1>

<div id="pgmetadata"
     data-check-url="{jurl 'pgmetadataAdmin~pgmdadmin:check'}"></div>


<div id="pgmd-message-panel">

    <div class="pgmd-ok_profile_ok_view">
        <p>PgMetadata can be used.</p>
        <p>The view <code>pgmetadata.v_dataset_as_dcat</code> have been found into
            the database indicated into the given credentials. </p>
    </div>

    <div class="pgmd-bad_profile">
        <p class="error">Error: postgresql credentials set for PgMetadata are not good.
        PgMetadata cannot use the view <code>pgmetadata.v_dataset_as_dcat</code>.
            Please fix postgresql credentials
        </p>
    </div>

    <div class="pgmd-ok_profile_no_view">
        <p class="error">Error: PgMetadata don't find the view <code>pgmetadata.v_dataset_as_dcat</code>
        into the database given into PgMetadata credentials.</p>
        <p>Please create the view into the database <span id="pgmd-database"></span>
        (see the PgMetadata plugin for Qgis) or modify credentials.</p>
    </div>
    <div class="pgmd-no_profile_ok_view">
        <p>PgMetadata can be used.</p>
        <p>The view <code>pgmetadata.v_dataset_as_dcat</code> have been found into
            the database indicated into the database of Lizmap.</p>
    </div>
    <div class="pgmd-no_profile_no_view">
        <p class="error">Error: PgMetadata don't find the view <code>pgmetadata.v_dataset_as_dcat</code>
            into the database of Lizmap.</p>
        <p>Please create the view into the database <span id="pgmd-database"></span>
            (see the PgMetadata plugin for Qgis) or indicate new credentials.</p>
    </div>

    <div class="pgmd-no_database">
        <p class="error">Error: PgMetadata don't find a Postgresql database, so it
            has no access to any view <code>pgmetadata.v_dataset_as_dcat</code>.</p>
        <p>Please indicate credentials to access to a Postgresql database having
            a view <code>pgmetadata.v_dataset_as_dcat</code>.</p>
    </div>

    <div class="pgmd-ok_profile_ok_view pgmd-ok_profile_no_view pgmd-bad_profile">
        <button id="pgmd-modify-credentials">Modify credentials</button>
    </div>
    <div class="pgmd-no_profile_ok_view pgmd-no_profile_no_view pgmd-no_database">
        <button id="pgmd-create-credentials">Set new credentials</button>
    </div>

    <div class="pgmd-saving">
        <p><img src="{$j_basepath}themes/default/css/images/download_layer.gif" alt="" /> Checking the postgresql access and the expected view...
            Please wait.
        </p>
    </div>
</div>

<form id="pgmd-profile" action="{jurl 'pgmetadataAdmin~pgmdadmin:save'}"
      autocomplete="off"
      method="POST"
      class="pgmd-modify_credentials pgmd-create_credentials"
    >
    <fieldset><legend>Credentials to access to the Postgresql database</legend>

    <p>The database must have the <code>pgmetadata.v_dataset_as_dcat</code> view.</p>

    <div>
        <label>Host</label>
        <input type="text" id="pgHost" name="pgHost"
               value="{$profile['host']|eschtml}" required="required"/>
    </div>

    <div>
        <label>Port</label>
        <input type="text" id="pgPort" name="pgPort"
               value="{$profile['port']|eschtml}"  required="required"/>
    </div>

    <div>
        <label>Database</label>
        <input type="text" id="pgDatabase" name="pgDatabase"
               value="{$profile['database']|eschtml}" required="required"/>
    </div>

    <div>
        <label>login</label>
        <input type="text" id="pgUser" name="pgUser" autocomplete="off"
               value="{$profile['user']|eschtml}" required="required" />
    </div>

    <div>
        <label>password</label>
        <input type="password" id="pgPassword"  name="pgPassword" autocomplete="off"
               value="{$profile['password']|eschtml}" required="required" />
    </div>
        <p>All fields are required.</p>
    <div>
        <button id="pgmd-button">Sauvegarder</button>
    </div>
    </fieldset>
</form>