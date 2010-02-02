<?php
# RenownedCMS Configuration File

define("DB_USER", 'root');			# MySQL Database Username
define("DB_PASS", '');				# MySQL Database Password
define("DB_HOST", 'localhost');		# MySQL Server Location (Usually 'localhost')
define("DB_NAME", 'cms');			# MySQL Server Database Name

define("CMS_BASEHREF", 'http://' . $_SERVER['SERVER_NAME'] . dirname($_SERVER['SCRIPT_NAME']) . '/');

define("CMS_USER", 'tlhunter');		# CMS Administrator Username
define("CMS_PASS", 'secret');		# CMS Administrator Password

define("CMS_DEBUG", FALSE);			# Verbose error reporting, set to false unless stuff is breaking