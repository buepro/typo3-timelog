.. include:: /Includes.rst.txt

==============
Administration
==============


Security
========

One of the objectives from this extension is to view reports without the need to be registered on the website.
This is why handles are used to define the content to be shown. In case the data is more sensitive it is suggested
to require a user identification (login) for the page the plugin is used on.

Environment
===========

When generating tasks in the backend the php time zone is taken into account. Review it in the
"Configure Installation-Wide Options" from the settings module (e.g.: `[SYS][phpTimeZone] = Europe/Helsinki`).
In case the timezone setting can't be changed for the installation it might be defined by
:ref:`userTS <configuration-timezone>`.

Installation
============

The extension has been created using the Bootstrap framework hence the html-markup as well as the css-classes used take
advantage from that framework. The fastest way to get the extension productive is to use bootstrap. In case
bootstrap isn't available the extension templates as well as style definitions need to be reviewed.

Install extension
-----------------

Non-composer mode
~~~~~~~~~~~~~~~~~

#. Install extension `auxlibs`
#. Install extension `timelog`

.. note::
   Further information regarding installing an extension can be found in the
   `installation guide <https://docs.typo3.org/m/typo3/guide-installation/master/en-us/ExtensionInstallation/Index.html>`__

Composer mode
~~~~~~~~~~~~~

`composer require buepro/typo3-timelog`

Add plugin to page
------------------

Follow these steps to add the plugin to a page:

#. Create a page to view the tasks (following called *timelog page*)

#. Create an extension template for the *timelog page* and add "Timelog (timelog)"  to "Include static (from extension)".

#. Add the plugin "Task panel" to the *timelog page*

#. In the :ref:`constant editor <configuration-constant-editor>` set the `storagePid` to the *timelog page*-uid.

Create separate storage page
----------------------------

The page where timelog related records (e.g. project, task) are stored is referred to as *timelog storage page*.
As *timelog storage page* the above created *timelog page* can be used. In case a separate page or folder is desired
the following optional steps should be carried out:

#. Create a folder page (following called *timelog storage page*). You might place it inside the *timelog page*.

#. Configure the record preview as outlined in the :ref:`configuration <configuration-record-preview>`.

#. In the :ref:`constant editor <configuration-constant-editor>` set the `storagePid` to the *timelog storage page*-uid.

Update the extension
====================

After updating the extension launch the upgrade wizard from the upgrade module and do the following:

- Create missing tables and fields if necessary
- Run wizards as needed

Configuration
=============

Following some aspects from the extension should be configured as outlined :ref:`here <configuration>`.

Maintenance
===========

Handles
-------

The projects, tasks, task groups as well as batches get a handle assigned. The handle is based on a "salt" which is
generated upon installing the extension. In case the extension and the associated records should be used on an
other TYPO3 installation the "salt" needs to be moved as well. The "salt" can be edited in the settings module under
the "Extension Configuration".

.. important::
   Store the value from `Hashid salt` found in the timelog extension configuration (settings module) separately.

