.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


Target group: **Administrators**


.. only:: html

	.. contents:: Within this page
		:local:
		:depth: 3

.. _configuration:

Configuration Reference
=======================

This section describes all options aviable for Datec Timeline via TypoScript setup. To change these options please add a new extension template to your ROOT template.

.. _configuration-typoscript:

Minimal configuration
---------------------

Upon installation, please add the static extension template 'Datec Timeline' to your ROOT template (Web > Template > edit root page template > Includes > select static template from extensions) and set at least the following options:

.. code-block:: ts

	# PID of your storage folder for appointments
	plugin.tx_datectimeline.persistence.storagePid = 123
	plugin.tx_datectimeline.settings.storagePid = 123

	# Valid e-mail address to dispatch automatic mails from
	plugin.tx_datectimeline.settings.mail.internMailFrom = timeline@no-reply.com


General configuration
---------------------

plugin.tx_datectimeline.

.. container:: ts-properties

	================================================    =============   ==============================================================================  ===========
	Property                                            Data type       Description                                                                     Default
	================================================    =============   ==============================================================================  ===========
	view.templateRootPaths                              array           Constant, path to template files if you wish to use your own.                   EXT:datec_timeline/Resources/Private/Templates/
	view.partialRootPaths                               array           Constant, path to partial template files if you wish to use your own.           EXT:datec_timeline/Resources/Private/Partials/
	view.layoutRootPaths                                array           Constant, path to layout files if you wish to use your own.                     EXT:datec_timeline/Resources/Private/Layouts/
	persistence.storagePid                              int             System folder for appointments.
	settings.storagePid                                 int             System folder for appointments.
	settings.mail.internMailFrom                        string          E-mail address for automatic notification Mails [FROM].                         timeline@no-reply.com
	settings.mail.internMailFromName                    string          Name to display for automatic notification Mails [FROM-NAME].                   Datec Timeline
	settings.display.comments.dateFormat                string          Like 'settings.display.dateFormat' for comments only.                           d.m.Y - H:
	settings.reminderMailAfterCreation                  string          Send out first reminder E-Mail after creation of appointment.                   true
	settings.langOptions                                string          Hides/shows language options.                                                   true
	================================================    =============   ==============================================================================  ===========
