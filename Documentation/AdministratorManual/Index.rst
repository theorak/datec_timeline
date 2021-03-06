.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _admin-manual:

Administrator Manual
====================

Target group: **Administrators**

.. only:: html

	.. contents:: Within this page
		:local:
		:depth: 3

Requirements
------------

.. caution::
	You must load the jQuery JavaScript framework ans jQuery-uid plugin yourself as the frontend plugin utilizes functions that depend on these libraries.

Installation
------------

1) Download and install the extension via the extension manager (extKey: datec_timeline).
2) Add one storage folder for appointments, note down the page id (PID) of this folder.
3) Check the Configuration section of this manual for the required configuration and follow the steps there.
4) Insert the main plugin as described below.
5) (optional) Add the scheduler task for appointment reminder e-mails.

Insert Plugin
-------------

1) Insert a content element, choose "Plugins" -> "General Plugin"

.. figure:: ../Images/plugin_01.jpg
	:width: 700px
	:alt: insert plugin

	Inserting content element of type "Plugin"


2) Choose the plugin "Datec Timeline".

.. figure:: ../Images/plugin_02.jpg
	:width: 700px
	:alt: modules

	Choosing a display form

3) (optional) Set access rights as required, appointments should have a closed group of users.

Add Reminder E-mail Task
------------------------

1) Goto the BE-Module "Scheduler" (scheduler extension must be installed and configured).
2) Add a new scheduler task "Datec Timeline - Reminder e-mails task".
3) It is recommended to set a recurring, daily task (86400 seconds) and set the time of execution time to 0:00 am.
4) Note that the e-mail translation is not fully implemented yet.


Add Cleanup Dates Task
----------------------

1) Goto the BE-Module "Scheduler" (scheduler extension must be installed and configured).
2) Add a new scheduler task "Datec Timeline - Cleanup Dates Task".
3) It is recommended to set a recurring, daily task (86400 seconds).
4) Also enter a number of days after which dates should be deleted.
5) Note that this is a cleanup task, thus all removed entries will be loosed irreversible.