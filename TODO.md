v0.4:
- adding simple mass edit
- add possibility to have tab in generated forms


v0.3:
Features:
- unit testing
x add config for order entities in list view
x add format for date in list
- file exporters : array type, association
- add filters
- handle custom actions for edit form (enable user for example)
- add configuration for custom url for actions
- add configuration for custom actions
x improve admin and action name from request (use default parameters in routing instead)
- handling new Symfony bootstrap layout
- configure application date format (filename export, displayed date...)
- localization
- bootstrap theme
x add link type in list

Bug fixes :
- fix bug when deleting entity with integrity constraint
- fix bug with FOSUser column sorting
- fixing columns order in export
- fix bug in list on column title when property has a uppercase character (eg: publicationDate should be Publication Date)

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
