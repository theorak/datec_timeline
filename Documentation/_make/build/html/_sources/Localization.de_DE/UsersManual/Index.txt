.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt


.. _users-manualDE:

Benutzerhandbuch
================

Zielgruppe: **Benutzer und Redakteure**

.. only:: html

	.. contents:: Auf dieser Seite
		:local:
		:depth: 3


Editors - Creator of Appointments
---------------------------------

Die Farbe der Termine basiert auf dem Ersteller des Termins. Alle Ersteller sind Frontend-Benutzer, daher kann die Farbe über die Option "Terminfarbe" in den Benutzerdaten angepasst werden.

.. figure:: ../../Images/datec_timeline_04_creator.jpg
	:width: 900px
	:alt: Backend view

	Frontend Benutzer editieren als Ersteller

Benutzer - Ansicht
------------------

Es wird standardmäßig die fullcalendar "Wochenangenda"-Ansicht angezeigt. Diese Ansicht ist optimal für das planen von Terminen mit zeitlichen Informationen.
Über die Schaltflächen auf der rechten Seite kann der Benutzer zwischen der Monats, Wochen und Tag Ansicht wechseln. Die Tag Ansicht ist die Standard Ansicht für Mobile geäte oder kleinere Bildschirme.
Ebenfalls oberhalb der Timeline finden Sie die Ersteller von Terminen, klicken Sie diese an um Termine dieser Ersteller auszuschließen.
Diese Anzeige wird alle 30 Sekunden aktualisiert.

.. figure:: ../../Images/datec_timeline_02_views.png
	:width: 900px
	:alt: Backend view

	Anzeige von Terminen


Benutzer - Erstellen und Bearbeiten
-----------------------------------

Der Benutzer kann einen Termin im Frontend erstellen oder bearbeiten. Mit einem klick auf den gewünschten Tag in der Timeline öffnet sich ein Popup Fenster mit folgenden Eigenschaften:

- Titel: der Titel des Termins.
- Beschreibung: Eine kurze Beschreibung des Termins.
- Teilnehmer: Eine Liste von anderen Frontend Benutzern die zu dem Termin eingeladen werden können.
- Von/Bis: Datum und Zeitraum des Termin mit dem Datum ausgefüllt das ausgewählt wurde.
- Errinnerung am: Es wird eine automatische Erinnerungsmail zu dem gewünschten Datum versendet, vorausgefüllt mit dem Start des Termins.
Hinweis: Der Aktuell eingeloggte Frontend Benutzer wird als Terminersteller gespeichert.

Der Termin kann wieder bearbeitet werden, indem man einfach auf den Termin klickt oder ihn per Drag&Drop zum gewünschten Datum bewegt.

.. figure:: ../../Images/datec_timeline_03_createdate.png
	:width: 900px
	:alt: Backend view

	Ansicht von Terminen
