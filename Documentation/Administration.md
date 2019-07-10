# Administration

## Security

One of the objectives from this extension is to view reports without the need to be registered at the website.
This is why handles are used to define the content to be shown. In case the data is more sensitive it is suggested
to require a user identification (login) for the page the plugin is used on. 

## Environment

When generating tasks in the backend the php time zone is taken into account. Review it in the 
"Configure Installation-Wide Options" from the settings module (e.g.: `[SYS][phpTimeZone] = Europe/Helsinki`). 
In case the timezone setting can't be changed for the installation it might be defined by 
[userTS](Configuration.md#timezone).

## Installation

The extension has been created using the Bootstrap framework hence the html-markup as well as the css-classes used take
advantage from that framework. The fastest way to get the extension productive is to use bootstrap. In case
bootstrap isn't available the extension templates as well as style definitions need to be reviewed.

### Install Plugin

Follow these steps to install the extension plugin:

[UrlInstallationGuide]: https://docs.typo3.org/m/typo3/guide-installation/master/en-us/ExtensionInstallation/Index.html

1. Install the extension as outlined in the ["Installation and Upgrade Guide"](UrlInstallationGuide). The extension 
key is **timelog**.

1. Create a page to view the tasks (following called *timelog page*)

1. Create an extension template for the *timelog page* and add "Timelog (timelog)"  to "Include static (from extension)".

1. Add the plugin "Task panel" to the *timelog page*

1. In the [constant editor](Configuration.md#constant-editor) set the `storagePid` to the *timelog page*-uid.

### Create separate storage page

The page where timelog related records (e.g. project, task) are stored is referred to as *timelog storage page*.
As *timelog storage page* the above created *timelog page* can be used. In case a separate page or folder is desired
the following optional steps should be carried out:

1. Create a folder page (following called *timelog storage page*). You might place it inside the *timelog page*.

1. Configure the record preview as outlined in the [configuration](Configuration.md#record-preview).

1. In the [constant editor](Configuration.md#constant-editor) set the `storagePid` to the *timelog storage page*-uid.

## Configuration

Following some aspects from the extension should be configured as outlined [here](Configuration.md).

## Maintenance

### Handles

The projects, task batches as well as the tasks get a handle assigned. The handle is based on a "salt" which is
generated upon installing the extension. In case the extension and the associated records should be used on an
other TYPO3 installation the "salt" needs to be moved as well. The "salt" can be edited in the settings module under
the "Extension Configuration".

