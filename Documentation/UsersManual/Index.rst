.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _users-manual:

Users manual
============

Target group: **Users and Editors**

.. only:: html

	.. contents:: Within this page
		:local:
		:depth: 3


Editors - Creator of Appointments
---------------------------------

The color that appointments appear in is based around the creator of that appointment. All creators are frontend users, thus to change his color, look for the Date Color option in the users data.

.. figure:: ../Images/datec_timeline_04_creator.jpg
	:width: 900px
	:alt: Backend view

	Editing Frontend users as creators


Users - Views
-------------

The default view is fullcalendar's "Agenda Week" view. This view is optimal for appointments with time information.
The controls on the right also let the user switch between monthly and per day view, the latter is forced as default for mobilde devices or small screens.
Also above the timeline you'll find the list of creators and participants of appointments, click them to filter the viewed appointments.
This view will refresh every 30 seconds.

.. figure:: ../Images/datec_timeline_02_views.jpg
	:width: 900px
	:alt: Backend view

	Viewing of appointmets


Users - Creating and Editing
----------------------------

Users can create and edit appointments right in the frontend. Just click on any spot in the timeline and a popup window for all necessary properties appears:
- Title: the title of the appointment.
- Description: A short description of the appointment.
- Participants: A list of other frontend-users to invite to this appointment.
- From/To: The date-time range of the appointment, prefilled with the selected date.
- Reminder From: Automatic reminder e-mails will be send after this date, prefilled with the start of the appointment.
Note: The currently logged in frontend user is saved as the creator of the appointment.

The appointment can then be edited again, by simply clicking on it, or moving it to a desired date.

.. figure:: ../Images/datec_timeline_03_createdate.jpg
	:width: 900px
	:alt: Backend view

	Viewing of appointmets

