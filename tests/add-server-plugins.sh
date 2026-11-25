#!/usr/bin/env bash

qgis-plugin-manager init
qgis-plugin-manager update
qgis-plugin-manager install "Lizmap server"
# Upgrade the plugins already installed: an old Lizmap server plugin prevents Lizmap from displaying the maps
qgis-plugin-manager upgrade
