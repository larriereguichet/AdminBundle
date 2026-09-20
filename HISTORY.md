v2.0:
- an image row left without a file is rejected by the form instead of failing the save. An entry the
  administrator added and never filled submitted an image with neither an uploaded file nor a stored
  path, which the uploader skipped and the database refused on a non nullable path column, so the whole
  save died on an error naming a column rather than a field. The message is translated through
  `lag_admin.image.file_required` in the `admin` domain
- the English translation catalogue is no longer empty. An application running in English displayed the
  raw translation keys for every string the bundle ships
- BREAKING: the collection entry markup moved from the `collection_item` Twig macro to a
  `lag_admin_collection_item` block, so it can be overridden — a macro cannot be. A theme overriding it
  has to extend `@LAGAdmin/forms/theme.html.twig`, and derive what it needs from `form`:
  `form.vars.name` is the entry index, the collection options sit on `form.parent.vars`
- the delete link of a collection entry no longer carries `glyphicon glyphicon-remove` and `text-right`,
  which are Bootstrap 3 class names and rendered nothing in the Bootstrap 5 layout the theme extends: the
  button had no icon and no alignment. It is now a lighter `btn btn-sm btn-outline-danger`, and the
  unconditional `<br/>` that padded the row is gone
- BREAKING: `ImagesAwareInterface` no longer declares `addImage()` and `removeImage()`. An entity
  using `ImagesAwareTrait` is unaffected, since the trait still provides them. The contract was
  keeping out any entity holding its own image class, because `addImage(ItsOwnImage $image)` cannot
  implement an inherited `addImage(ImageInterface $image)` — narrowing a parameter is illegal — and
  the upload listener only ever reads the collection. Code type hinting `ImagesAwareInterface` to
  call the mutators has to type hint the entity or the trait instead
- the collection widget numbers the next entry after the last rendered one. It announced one index
  too far, so adding an entry to a collection that already had some left a hole in the submitted
  keys and the entry at the missing index came back empty
- BREAKING: a grid property condition is evaluated before the property value is mapped. The
  condition expression receives the row entity where it used to receive the mapped cell value.
  This covers `data`, `this` and `object` alike: ConditionMatcher binds the three to the same
  value, so swapping one for another is not a migration. Read the value from the entity, or
  keep using `resource`, which has always been the row entity
- a property condition now short circuits the value mapping, so it can guard a property path
  that is not readable on every row
- compound property children are built through the whole cell builder chain, so their own
  condition and roles are still checked
- a grid with no row renders its table and its empty message again, instead of rendering
  nothing at all
- a collection entry property no longer has to be named

v1.0:
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
- [DONE] handling new Symfony bootstrap layout
- [DONE] adding mass edit
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
