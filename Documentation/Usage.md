# Usage

## Backend

While working in the backend the list module is mainly used with the *timelog storage page* selected.

### Preparation

#### Create users

Create frontend users representing the service provider companies, service providers (the worker) and clients.
The front end users might be stored on an other page than the *timelog storage page*.

#### Create projects

Add new project records on the *timelog storage page*.

### Process

#### Create tasks

Upon creating a task an interval is automatically started. While working on the task you might write down what has been
done. In case the work gets interrupted (going for a pause, getting other urgent task to the desk) set the end time
from the ongoing interval and save the task.

**Remark:** In case of clicking on the view button prior saving the task the task won't be shown since its handle has not
been created yet. After the task has been saved for the first time its details can be shown in the frontend.

#### Continue a task

In case a previous task has been worked on that task should first be closed by defining an end time for the ongoing 
interval. On the current task add a new interval and continue documenting the work.

#### Communicate the progress

1. Open a project or a task and click on the view button.

1. In the rendered frontend view click the action "Send email to client".

#### Create a batch

1. Open a project and click on the view button.

1. In the rendered frontend view click the action "Create batch".


