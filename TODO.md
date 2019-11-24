wip:
    - fixed admin configuration and action configuration events

v0.n+1:
- add filters
- file exporters : array type, association
- move export logic into a separate service
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
- making batch actions configurable and disabled
- make interfaces for factories to allow overriding by third party
- adding simple mass edit
- unify load entities method with a FilterObject as parameter for load method
- add configuration to disable flash message and logs in message handler
- use an Interface for MessageHandler and a getter in Admin class
- use ContainerAwareInterface for FieldFactory (and maybe for ContainerTrait)
- add configuration for entity getLabel method
- add an adapter to pagination to allow to change pager
- adding translation pattern for admin (and not only in configuration)
- add possibility to have tab in generated forms
- improve customActions template
- make delete form dynamic (@see CRUDController l.214)
- configure application date format (filename export, displayed date...)
- add configuration for custom url for actions (waiting for ActionBundle ?)
- documentation
- handle custom actions for edit form (enable user for example) (WIP)
- configure application date format (filename export, displayed date...) (WIP)
- localization (rest field label in edit mode)
- remove dependency with container in action configuration (=> move camelize method in a trait)
- use translation in twig instead for flash messages
-----------------------------------------------

v0.4: 
 - Refactor configuration management (one unified interface, configureOptions in Configuration classes)
 - Move Action* in its own folder
 - Handle menu with KNPMenu bundle
 - Improve documentation
 - Fix assets management
 
 
-----------------------------------------------

v0.3:
Features:
- [DONE] basic unit testing
- [DONE] handling new Symfony bootstrap layout
- [DONE] adding mass edit
- [DONE] improve admin and action name from request (use default parameters in routing instead)
- [DONE] update to Symfony 3.x 
- [DONE] add config for order entities in list view
- [DONE] add format for date in list
- [DONE] add link type in list
- [DONE] batch actions

Bug fixes :
- fix bug when deleting entity with integrity constraint
- fix bug with FOSUser column sorting
- fixing columns order in export


Bug fixes :
- fix bug when deleting entity with integrity constraint
- fix bug with FOSUser column sorting
- fix bug when sorting columns with relations
- fixing columns order in export
- fix bug in list on column title when property has a uppercase character (eg: publicationDate should be Publication Date)
- add default actions configuration in list (edit+delete) in ExtraConfiguration subscriber

-----------------------------------------------

v0.2:
Features:
- [DONE] add column sort
- [DONE] handle basic permissions
- [DONE] add dynamic admin or config by event
- [DONE] add exporters
- [DONE] basic documentation
- [DONE] changing admin generated routes default names with dots

Bug fixes :
- [DONE] fixing bug in list with array fields (doctrine "array" type")
- [DONE] fixing bug when User is null if 404 permissions method
- [DONE] fixing bug if a prefix was added in routing admin routing import
- [DONE] fixing empty text on deletion when an entity have no label property
- [DONE] fixing bug in filename when exporting (always .csv)
- [DONE] fixing bug in date time fields when exporting
- [DONE] fixing bug in array fields when exporting
- [DONE] fixing bug in action configuration merge on export property (is override)

-----------------------------------------------

v0.1:
The void into the black darkness
