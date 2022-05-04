
```yaml
# config/packages/lag_admin.yaml
lag_admin:
    admins:
        # The admin name can be anything, but it should be unique. All the following configuration values are only 
        # applied to the current Admin. It overrides the values of Application configuration parameters. Those values 
        # are the default values
        article:
            # The fully qualified path to the entity. If you use the Doctrine ORM data provider, this class should 
            # be a Doctrine entity 
            entity: App\Entity\Article
            # Those are the default actions. You can remove or add your own action.
            actions:
                create: ~
                edit: ~
                list: ~
                delete: ~
            # You can use your own Admin class here. It should implements LAG\AdminBundle\Admin\AdminInterface
            class: LAG\AdminBundle\Admin\Admin 
            
            # You can customize the route names and urls generated. It should contains "{admin}" and "{action}"
            routes_pattern: lag.admin.{admin}.{action}
            
            # You can override the default controller. Be careful, the bundle should not work as expected if your
            # custom controller does trigger required events 
            controller: LAG\AdminBundle\Controller\AdminAction
            
            # The maximum number of entities displayed in the list Action 
            max_per_page: 20
            
            # Use the translation system. The translation pattern are used to generate the translation keys. It 
            # should contains the {key}
            translation:                
                pattern: lag.admin.{key}
                
            
            # Documentation in progress
            form: ~
            form_options: ~
            pager: ~
            permissions: ~
            string_length: ~
            string_truncate: ~
            date_format: ~
            data_provider: ~
                page_parameter: ~
                list_template: ~
                update_template: ~
                create_template: ~
                delete_template:  ~
```
