# Fields

Actions are responsible to display a list of fields. Each field can have a type, and according to this type, options are
also available. The field options override the options defined in the parent action. All options are shown with the 
default options.

Here is the list of types :

## String
This field displays a simple string.

```yaml
# config/admin.resources/article.yaml
article:
    actions:
        list:
            name:
                type: string
                options:  
                    length: 200
                    replace: '...'
                    translation: true
```

| Options | Defaults  | Notes |
| ------- | --------- | ------------- |
| length  | 200       | If the string is longer than this value, the string will be truncated |
| replace | ...       | If the string is truncated, the rest of the string (after the length) will be replace by this string |
| translation | true  | Use the translation system |

## Boolean
This field displays a boolean value.

```yaml
# config/admin.resources/article.yaml
article:
    actions:
        list:
            name:
                type: boolean
                options:
                    template: '@LAGAdmin/Field/boolean.html.twig'
```

| Options | Defaults  | Notes |
| ------- | --------- | ------------- |
| template | @LAGAdmin/Field/boolean.html.twig | The template used to render the field |

## Array
This field displays an array, a collection or an iterator value.

```yaml
# config/admin.resources/article.yaml
article:
    actions:
        list:
            name:
                type: array
                options:
                    glue: ', '
```

| Options | Defaults  | Notes |
| ------- | --------- | ------------- |
| glue | ', ' | The glue used between each value of the array |

## Link
This field displays a link to another page.

```yaml
# config/admin.resources/article.yaml
article:
    actions:
        list:
            name:
                type: link
                options:
                    template: '@LAGAdmin/Field/link.html.twig'
                    title: ''
                    icon: ''
                    target: '_self'
                    route: ''
                    parameters: [],
                    url: ''
                    text: ''
                    admin: null,
                    action: null
                    class: ''
                    default: '~',
```

| Options | Defaults  | Notes |
| ------- | --------- | ------------- |
| template | '@LAGAdmin/Field/link.html.twig' | The template used to render the field |
| title | '' | The title markup of the link |
| icon | '' | Add an icon to the link text |
| target | '_self' | The target of the link |
| route | '' | If the value is filled, the link url will be generated using this route |
| parameters | [] | If a route is used, some parameters can be passed. You can pass dynamic parameters matching the entity value (see example) |

