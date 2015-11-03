v0.4:
- adding simple mass edit
- add possibility to have tab in generated forms
- add configuration for custom url for actions
- add configuration for custom actions
- handling new Symfony bootstrap layout
- bootstrap theme
- file exporters : array type, association
- adding Admin own translation pattern
- move default configuration in a separate and configurable event listener
- remove dependence to container for routing loader
- inject admin list form type
- creating an action view to generate action link
- remove hardcoded id property and use metadata tu get primary key
- removing container dependence in routing loader
- making batch actions configurable and disablable


-----------------------------------------------

v0.3:
Features:
- unit testing
- add config for order entities in list view (DONE)
- add format for date in list (DONE)
- add filters (WIP)
- handle custom actions for edit form (enable user for example) (WIP)
- improve admin and action name from request (use default parameters in routing instead) (DONE)
- configure application date format (filename export, displayed date...) (WIP)
- localization
- add link type in list (DONE)
- adding translation pattern for admin (and not only in configuration)

Bug fixes :
- fix bug when deleting entity with integrity constraint
- fix bug with FOSUser column sorting
- fixing columns order in export
- fix bug in list on column title when property has a uppercase character (eg: publicationDate should be Publication Date)

-----------------------------------------------

v0.2:
Features:
x add column sort
x handle basic permissions
x add dynamic admin or config by event
x add exporters
x basic documentation
x changing admin generated routes default names with dots

Bug fixes :
x fixing bug in list with array fields (doctrine "array" type")
x fixing bug when User is null if 404 permissions method
x fixing bug if a prefix was added in routing admin routing import
x fixing empty text on deletion when an entity have no label property
x fixing bug in filename when exporting (always .csv)
x fixing bug in date time fields when exporting
x fixing bug in array fields when exporting
x fixing bug in action configuration merge on export property (is override)

-----------------------------------------------

v0.1: 
the void
