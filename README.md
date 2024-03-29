# PgMetadata Module

[![Lint JS 🎳](https://github.com/3liz/lizmap-pgmetadata-module/actions/workflows/test-lint-js.yml/badge.svg)](https://github.com/3liz/lizmap-pgmetadata-module/actions/workflows/test-lint-js.yml)
[![Lint PHP 🎳](https://github.com/3liz/lizmap-pgmetadata-module/actions/workflows/test-lint-php.yml/badge.svg)](https://github.com/3liz/lizmap-pgmetadata-module/actions/workflows/test-lint-php.yml)
[![Release 🚀](https://github.com/3liz/lizmap-pgmetadata-module/actions/workflows/release.yml/badge.svg)](https://github.com/3liz/lizmap-pgmetadata-module/actions/workflows/release.yml)
[![Packagist](https://img.shields.io/packagist/v/lizmap/lizmap-pgmetadata-module)](https://packagist.org/packages/lizmap/lizmap-pgmetadata-module)

This module is designed for [Lizmap Web Client](https://github.com/3liz/lizmap-web-client/), and allows 
displaying PostgreSQL layers metadata stored in the layer database.

The metadata must be created in the PostgreSQL database as designed in the
[PgMetadata QGIS plugin](https://github.com/3liz/qgis-pgmetadata-plugin).

![Metadata information panel](metadata_information_panel.jpeg)

## Installation

Once Lizmap Web Client application is installed and working, you can install the pgmetadata module:

Since version 0.2.4, it is recommended to install the module
with [Composer](https://getcomposer.org), the package manager for PHP.
If you can't use it or if you are using Lizmap 3.3 or lower, use the manual way to
install the module (jump to the corresponding section below)

### Automatic installation with Composer and lizmap 3.4 or higher

* into `lizmap/my-packages`, create the file `composer.json` (if it doesn't exist)
  by copying the file `composer.json.dist`, and install the modules with Composer:

```bash
cp -n lizmap/my-packages/composer.json.dist lizmap/my-packages/composer.json
composer require --working-dir=lizmap/my-packages "lizmap/lizmap-pgmetadata-module"
```

* Then go into `lizmap/install/` and execute Lizmap install scripts :


If you are using Lizmap 3.6 or higher, first execute 

```bash
php configurator.php pgmetadata
php configurator.php pgmetadataAdmin
```

Then, for Lizmap 3.4 and higher:

```bash
php installer.php
./clean_vartmp.sh
./set_rights.sh
```

Go to the next section "Publish your metadata as a DCAT catalog".

### Manual installation into lizmap 3.3 or 3.4 without Composer

* Get the last ZIP archive in the [release page](https://github.com/3liz/lizmap-pgmetadata-module/releases) of
  the GitHub repository.
* Extract the archive and copy the `pgmetadata` and `pgmetadataAdmin` directories in Lizmap Web Client folder `lizmap/lizmap-modules/`
* Edit the config file `lizmap/var/config/localconfig.ini.php` and add into 
  the section `[modules]`:

```ini
pgmetadata.access=2
pgmetadataAdmin.access=2
```
* Add into the section `[simple_urlengine_entrypoints]`:
```ini
admin="jacl2db~*@classic,jacl2db_admin~*@classic,jauthdb_admin~*@classic,master_admin~*@classic,admin~*@classic,jcommunity~*@classic,pgmetadataAdmin~*@classic"
```
If the parameter already exists, add `,pgmetadataAdmin~*@classic` into the value.

* If you are using Lizmap 3.6 or higher, execute

```bash
php lizmap/install/configurator.php pgmetadata
php lizmap/install/configurator.php pgmetadataAdmin
```

* Then execute Lizmap install scripts into `lizmap/install/` :

```bash
php lizmap/install/installer.php
./lizmap/install/clean_vartmp.sh
./lizmap/install/set_rights.sh
```

## Publish your metadata as a DCAT catalog

If you need to publish your metadata catalog as an RDF catalog, for example to be harvested by a third-party
metadata platform such as Ckan, you need to declare the needed credentials to your PostgreSQL database hosting the metadata.

To do it, go into the administration interface of Lizmap, click on the pgmetadata menu item,
and you could indicate these credentials.

Then, you would be able to access the following URLs

* **The full RDF catalog** with a URL like: https://[YOUR_LIZMAP_URL]/index.php/pgmetadata/dcat/
* **One dataset record by UUID** identifier with: https://[YOUR_LIZMAP_URL]/index.php/pgmetadata/dcat/?id=76853e57-387e-4b8d-9ee8-f176e11cd9ce
* **Filter datasets** containing a specified **keyword** (exact match): https://[YOUR_LIZMAP_URL]/index.php/pgmetadata/dcat/?q=akeyword

The XML created and returned would be like:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<rdf:RDF
    xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
    xmlns:dct="http://purl.org/dc/terms/"
    xmlns:dcat="http://www.w3.org/ns/dcat#"
    xmlns:owl="http://www.w3.org/2002/07/owl#"
    xmlns:foaf="http://xmlns.com/foaf/0.1/"
    xmlns:adms="http://www.w3.org/ns/adms#"
    xmlns:vcard="http://www.w3.org/2006/vcard/ns#"
    xmlns:schema="http://schema.org/"
    xmlns:rdfs="http://www.w3.org/2000/01/rdf-schema#">
<dcat:Catalog rdf:about="https://lizmap.com">
    <dct:description>PgMetadata DCAT RDF catalog</dct:description>
    <dct:language>fr</dct:language>
    <dct:title>PgMetadata</dct:title>
    <dcat:dataset>
      <dcat:Dataset rdf:about="http://lizmap.localhost/index.php/pgmetadata/dcat/?id=76853e57-387e-4b8d-9ee8-f176e11cd9ce">
        <dct:identifier>76853e57-387e-4b8d-9ee8-f176e11cd9ce</dct:identifier>
        <dct:title>Test title</dct:title>
        <dct:description>Test abstract.</dct:description>
        <dct:language>en</dct:language>
        <dct:license>Licence Ouverte Version 2.1</dct:license>
        <dct:rights>Open</dct:rights>
        <dct:accrualPeriodicity>Yearly</dct:accrualPeriodicity>
        <dct:spatial>{"type":"Polygon","coordinates":[[[3.854,43.5786],[3.854,43.622],[3.897,43.622],[3.897,43.5786],[3.854,43.5786]]]}</dct:spatial>
        <dct:created rdf:datatype="http://www.w3.org/2001/XMLSchema#dateTime">2020-12-31T09:16:16.980258</dct:created>
        <dct:issued rdf:datatype="http://www.w3.org/2001/XMLSchema#dateTime">2020-12-31T09:16:16.980258</dct:issued>
        <dct:modified rdf:datatype="http://www.w3.org/2001/XMLSchema#dateTime">2020-12-31T09:16:16.980258</dct:modified>
        <dcat:contactPoint>
          <vcard:Organization>
            <vcard:fn>Jane Doe - Acme (GIS)</vcard:fn>
            <vcard:hasEmail rdf:resource="jane.doe@acme.gis">jane.doe@acme.gis</vcard:hasEmail>
          </vcard:Organization>
        </dcat:contactPoint>
        <dct:creator>
          <foaf:Organization>
            <foaf:name>Jane Doe - Acme (GIS)</foaf:name>
            <foaf:mbox>jane.doe@acme.gis</foaf:mbox>
          </foaf:Organization>
        </dct:creator>
        <dct:publisher>
          <foaf:Organization>
            <foaf:name>Bob Robert - Corp (Spatial div)</foaf:name>
            <foaf:mbox>bob.bob@corp.spa</foaf:mbox>
          </foaf:Organization>
        </dct:publisher>
        <dcat:distribution>
          <dcat:Distribution>
            <dct:title>test link</dct:title>
            <dct:description>Link description</dct:description>
            <dcat:downloadURL>https://metadata.is.good</dcat:downloadURL>
            <dcat:mediaType>application/pdf</dcat:mediaType>
            <dct:format>a file</dct:format>
            <dct:bytesize>590</dct:bytesize>
          </dcat:Distribution>
        </dcat:distribution>
        <dcat:keyword>tag_one</dcat:keyword>
        <dcat:keyword>tag_two</dcat:keyword>
        <dcat:theme>test theme</dcat:theme>
        <dcat:theme>New test theme</dcat:theme>
      </dcat:Dataset>
    </dcat:dataset>
</dcat:Catalog>
</rdf:RDF>
```

## Contribution guide

For PHP, the project is using PHP-CS-Fixer.
For JavaScript, it's using ESLint. Install it using `npm install`.

There are commands in the Makefile to run them.


## Getting help
* Send an email to the Lizmap mailing list at https://lists.osgeo.org/pipermail/lizmap/
* Join us on IRC , #lizmap on https://libera.chat
* Open a Github tickets on https://github.com/3liz/lizmap-adresse-module/issues
* Commercial support through 3liz , [Contact Us](mailto:info@3liz.com?subject=CommercialSupportRequest)
