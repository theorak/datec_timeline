.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt


Zielgruppe: **Administratoren**


.. only:: html

	.. contents:: Auf dieser Seite
		:local:
		:depth: 3

.. _configurationDE:

Konfigurationshandbuch
======================

Dieses Kapitel beschreibt alle Einstellungen von Datec Timeline die über TypoScript konfigurierbar sind.
Zum Ändern der Einstellungen fügen Sie bitte eines Erweiterungstemplate dem ROOT Template hinzu.

.. _configuration-typoscriptDE:

Minimale Einstellungen
-----------------------

Fügen Sie für die minimalen Einstellung ein statisch Template zu Ihrem Root Template (Web > Template > ROOT-Template bearbeiten> enthält > statisches Template der Erweiterung Auswählen) hinzu und fügen folgende Sie dort folgende Einstellungen hinzu:

.. code-block:: ts

	# PID of your storage folder for appointments
	plugin.tx_datectimeline.persistence.storagePid = 123
	plugin.tx_datectimeline.settings.storagePid = 123

	# Valid e-mail address to dispatch automatic mails from
	plugin.tx_datectimeline.settings.mail.internMailFrom = timeline@no-reply.com


Generelle Einstellungen
-----------------------

**plugin.tx_datectimeline**.

.. container:: ts-properties

	================================================   =============   ==============================================================================  ============
	Einstellungen                                      Datentyp        Beschreibung                                                                    Standardwert
	================================================   =============   ==============================================================================  ============
	view.templateRootPaths                             array           Konstante, Pfad zu den Template Dateien                                         EXT:datec_timeline/Resources/Private/Templates/
	view.partialRootPaths                              array           Konstante, Pfad zu den Partial Template Dateien                                 EXT:datec_timeline/Resources/Private/Partials/
	view.layoutRootPaths                               array           Konstante, Pfad zu den Layout Dateien                                           EXT:datec_timeline/Resources/Private/Layouts/
	persistence.storagePid                             int             Systemordner für die Termine
	settings.storagePid                                int             Systemordner für die Termine
	settings.mail.internMailFrom                       string          E-mail Adresse für die automatische E-Mail Benachrichtigung [FROM]              timeline@no-reply.com
	settings.mail.internMailFromName                   string          Anzeigename für die automatische E-Mail Benachrichtigung [FROM-NAME]            Datec Timeline
	settings.mail.translang                            string          Sprache für E-Mails, besonders Erinnerungs E-Mails aus dem BE                   de
	settings.mail.onCreate                             string          E-Mail versenden nach der Erstellung eines Termins                              true
	settings.mail.onUpdate                             string          E-Mail versenden nach der Enpassung eines Termins                               true
	settings.mail.onDelete                             string          E-Mail versenden nach der Löschung eines Termins                                true
	settings.display.comments.dateFormat               string          Datums Format nur für die Bemerkungen                                           d.m.Y - H:i
	settings.langOptions                               string          Sprachen ausblenden / anzeigen                                                  true
	================================================   =============   ==============================================================================  ============


.. tip::
	Die Optionen settings.reminderMailAfterCreation und settings.reminderMailAfterCreation werden in zukünftigen Versionen entfernt.