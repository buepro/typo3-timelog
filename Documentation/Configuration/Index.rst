.. include:: ../Includes.txt

.. _configuration:

=============
Configuration
=============

.. _configuration-constant-editor:

Constant editor
===============

Select the `PLUGIN.TX_TIMELOG_TASKPANEL` category in the template module constant editor to define the following
constants:

========================= ==========
Constant                  Description
========================= ==========
general.stylesheet        Select the stylesheet to be used:

                          - **css**: Use a css-file
                          - **scss**: Use a scss-file.

                          **Remark**: With the scss-option a scss-processing needs to be available (e.g. provided by
                          extension `bootstrap_package`)

persistence.storagePid    The uid from the *timelog storage page*. The timelog records are stored on that page.

markdown.transform        Defines how the description text for projects and tasks should be treated:

                          - 0: Output raw text
                          - 1: Apply markdown to html transformation
                          - 2: Individual treatment controlled by token

                          Please refer to constant editor for further adjustment parameters.
========================= ==========

Constant path: `plugin.tx_timelog_taskpanel`


PageTS
======

.. _configuration-record-preview:

Record preview
--------------

In case a separate page (folder page) holds timelog related records the record preview needs to be configured as
following:

.. code-block:: typoscript

   TCEMAIN.preview {
       disableButtonForDokType = 255, 199
       tx_timelog_domain_model_project {
           previewPageId = pluginPid
       }
       tx_timelog_domain_model_taskgroup {
           previewPageId = pluginPid
       }
       tx_timelog_domain_model_task {
           previewPageId = pluginPid
       }
   }

Replace pluginPid with the uid from the *timelog page*.


UserTS
======

.. _configuration-timezone:

Timezone
--------

Upon adding intervals the start time is automatically set to the server time. In case the backend user works in a
different timezone the start times might need to be adapted accordingly. The following `userTS` defines the timezone
`gmt+2` (e.g. Europe/Helsinki):

`tx_timelog.timezone.offset = 2`

