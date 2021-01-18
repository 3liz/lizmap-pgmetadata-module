## PgMetadata Module

This module is designed for Lizmap Web Client, and allows displaying PostgreSQL layers metadata stored in the 
layer database.

The metadata must be created in the PostgreSQL database as designed in the 
[PgMetadata QGIS plugin](https://github.com/3liz/qgis-pgmetadata-plugin).

![Metadata information panel](metadata_information_panel.jpeg)

### Installation

Once Lizmap Web Client application is installed and working, you can install the pgmetadata module:

* Get the last ZIP archive in the [release page](https://github.com/3liz/lizmap-pgmetadata-module/releases) of
  the github repository.
* Extract the archive and copy the `pgmetadata` directory in Lizmap Web Client folder `lizmap/lizmap-modules/`
* Edit the config file `lizmap/var/config/localconfig.ini.php` and add (or adapt) the section `[modules]` by 
  adding the `pgmetadata.access=2`

```ini
[modules]
pgmetadata.access=2
```

* Run Lizmap Web Client installer

```bash
php lizmap/install/installer.php
lizmap/install/clean_vartmp.sh
lizmap/install/set_rights.sh
```

### Publish your metadata as a DCAT catalog

If you need to publish your metadata catalog as an RDF catalog, for example to be harvested by a third-party 
metadata platform such as Ckan, you need to declare in the file `lizmap/var/config/profiles.ini.php` a new 
section `[jdb:pgmetadata]` with the needed credentials to your PostgreSQL database hosting the metadata. For 
example:

```ini
[jdb:pgmetadata]
driver=pgsql
database=pgmetadata
host=localhost
port=5432
user=your_user
password=your_password
persistent=off
;search_path=""
```

Then, you would be able to access the

* full RDF catalog with a URL like: http://[YOUR_HOST]/index.php/pgmetadata/dcat/
* one dataset record with: http://[YOUR_HOST]/index.php/pgmetadata/dcat/?id=76853e57-387e-4b8d-9ee8-f176e11cd9ce

The XML created would be like

```xml
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
```

## Contribution guide

For PHP, the project is using PHP-CS-Fixer.
For JavaScript, it's using ESLint. Install it using `npm install`.

There are commands in the Makefile to run them.
