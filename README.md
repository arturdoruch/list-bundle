# ListBundle

Symfony bundle for pagination, sorting and filtering of list items.

Build-in paginators can paginate:
 
 * array
 * Doctrine\ORM\Query
 * Doctrine\ORM\QueryBuilder
 * Doctrine\ODM\MongoDB\Query\Query
 * Doctrine\ODM\MongoDB\Query\Builder
 * Doctrine\MongoDB\CursorInterface
 * MongoCursor

For other database queries you can create own paginators. See section [paginator registration](#paginator-registration).

For JavaScript support install (with `yarn` or `npm`) package [@arturdoruch/list](https://github.com/arturdoruch/js-list). 
Package contains also file with CSS styles, styling the filter form and item list.

## Installation

Add the following code to the `composer.json` file, to the `repositories` block

```json
{
    "repositories": [
        // ...
        {
            "type": "vcs",
            "url": "https://github.com/arturdoruch/ListBundle"
        }
    ]
}
```

and run command `composer require arturdoruch/list-bundle`.

## Bundle configuration

```yml
artur_doruch_list:
    query_parameter_names:
        page: page
        limit: limit
        sort: sort
    query_sort_direction:
        asc: asc
        desc: desc
        position: after # One of "before"; "after"
        separator: ':'
    pagination:
        item_limits: []
        page_items:
            prev_page_label: '&#8592; Prev'
            next_page_label: 'Next &#8594;'
    # A collection of paginator providers, with "query class: paginator class" pairs.
    paginator_providers: []
```

### Paginator registration

1. Create paginator for database query (like Doctrine\ORM\Query).
The paginator must implement the `ArturDoruch\ListBundle\Paginator\PaginatorInterface` interface.
2. Register paginator in configuration at path `artur_doruch_list.paginator_providers`.

## Usage

Description of use on the example of a list of books.

### Filter form (optional)

For filtering list items, must be created form type class. This can be done by:

  * Using the `ArturDoruch\ListBundle\Form\FilterType` class and add the form filter fields in controller,
  * or creating own for type class and (optionally) extend the `ArturDoruch\ListBundle\Form\FilterType` class.
  
**The filter form must have method type of "GET" and `csrf_protection` option should be set to false.**
 
Example of the filter form type class. 
```php
<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\BookCategory;
use ArturDoruch\ListBundle\Form\FilterType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;

class BookFilterType extends FilterType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('category', EntityType::class, [
                'placeholder' => '-- all --',
                'class' => BookCategory::class,
                'choice_label' => 'category',
                'choice_value' => 'id'
            ])
            ->add('author')
            ->add('title');
    }
}
```

### Creating controller getting the item list

**The controller route method must be type of "GET".** 

Example of controller action getting the item list:

```php
<?php

namespace AppBundle\Controller;

use AppBundle\Form\Type\BookFilterType;
use ArturDoruch\ListBundle\ItemList;
use ArturDoruch\ListBundle\Paginator;
use ArturDoruch\ListBundle\Request\QueryParameterBag;
use ArturDoruch\ListBundle\Sorting\SortChoiceCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BookController
{
    /**
     * @Route(
     *     "/",
     *     methods={"GET"}
     * )
     */
    public function list(Request $request)
    {
        // (optional) Create filter form.
        // Info: 
        // The form name is used as the query parameter name in the request URL.
        // Use named form for creating form with own name (e.g. "filter").
        $form = $this->get('form.factory')->createNamed('filter', BookFilterType::class);
        $form->handleRequest($request);

        // Filtering criteria.
        $criteria = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $criteria = $form->getData();
        }
               
        // Get request query parameters (page, limit, sort).
        $parameterBag = new QueryParameterBag($request);        
        // Array with sorting field and order, pair ["field" => "order"]
        $sort = $parameterBag->getSort();
        
        // Get book items - array, query or cursor depend on database type.
        $bookRepository = '';
        $books = $bookRepository->get($criteria, $sort);        
        
        $pagination = Paginator::paginate($books, $parameterBag->getPage(), $parameterBag->getLimit(100));
        // (optional) Set item limits (overrides values form default config "pagination.item_limits").
        $pagination->setItemLimits([50, 100, 200]);

        // (optional) Define SortChoiceCollection to display "select" field with sorting options.
        // Alternatively you can render sorting link in twig template with "arturdoruch_list_sort_link" function.
        $sortChoiceCollection = new SortChoiceCollection();
        $sortChoiceCollection
            ->add('Lowest price', 'price', 'asc') // Sort books by price ascending.
            ->add('Highest price', 'price', 'desc'); // Sort books by price descending.

        return [
            'bookList' => new ItemList($pagination, $form, $sortChoiceCollection),
        ];
    }
}
```

## Frontend

### Twig functions

#### Filtering

 * `arturdoruch_list_filter_form` - Renders the filter form.
    
    Arguments:
    * `formView` *Symfony\Component\Form\FormView* - object getting from the ItemList object returned by controller.
    * `config` array
       * `resetSorting` *bool* - Whether to reset list sorting after filtering the list. 
       If true query "sort" parameter is removed from the request query.
       * `displayResetButton` *bool* - Whether to display button resetting the filter form elements.

#### Pagination

 * `arturdoruch_list_pagination` - Renders pagination.
 
    Arguments:
    * `pagination` *ArturDoruch\ListBundle\Pagination* - object getting from the ItemList object returned by controller.
     
 * `arturdoruch_list_displayed_items` - Renders range of displayed list items.
 
    Arguments:
    * `pagination` *ArturDoruch\ListBundle\Pagination* - object getting from the ItemList object returned by controller.
    
 * `arturdoruch_list_items_limit_form` - Renders form with "select" field to change items limit (displayed number items per page).
    
    Arguments:
    * `pagination` *ArturDoruch\ListBundle\Pagination* - object getting from the ItemList object returned by controller.
      
 * `arturdoruch_list_items_and_pagination` - Renders all elements mentioned above: pagination, range of displayed list items
 select field changing items limit.
 
    Arguments:
    * `pagination` *ArturDoruch\ListBundle\Pagination* - object getting from the ItemList object returned by controller.
 
#### Sorting 
 
 * `arturdoruch_list_sort_link` - Renders a link (an anchor) sorting the list items.
    
    Arguments:
    * `label` - The link label
    * `field` - The item field name used in repository for sorting items. For example Doctrine entity order field name.
    * `initialDirection` (default: `asc`) - Initial sort direction. One of the values: "asc", "desc".
    
 * `arturdoruch_list_sort_form` - Renders select element with options to sort list items.
    
    Arguments:
    * `sortChoiceCollection` *ArturDoruch\ListBundle\Sorting\SortChoiceCollection* - object getting from the ItemList object returned by controller.

### Template

```twig
{# base.html.twig #}
<!DOCTYPE html>
<html>
    <head>
    </head>
    <body>
        {% block content %}
        {% endblock %}
    </body>
</html>
```

Template for use with AJAX request.

```twig
{# ajax_list.html.twig #}
{% block list %}{% endblock %}
```

Example of template displaying all of the list parts.

```twig
{# Update only list table (block list) when is AJAX request. #}
{% extends app.request.xmlHttpRequest ?
    '@App/ajax_list.html.twig':
    '@App/base.html.twig'
%}

{% block content %}
    {{ arturdoruch_list_filter_form(bookList.filterForm) }}

    <div id="book-list-container">
    {% block list %}
        {% if bookList.count > 0 %}
        {{ arturdoruch_list_items_and_pagination(bookList.pagination) }}
        {{ arturdoruch_list_sort_form(bookList.sortChoiceCollection) }}

        <table class="table" id="book-list">
            <thead>
                <tr>
                    <th>Category</th>
                    <th>{{ arturdoruch_list_sort_link('Author', 'author') }}</th>
                    <th>{{ arturdoruch_list_sort_link('Title', 'title') }}</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
            {% for book in bookList %}
                <tr>
                    <td>{{ book.category }}</td>
                    <td>{{ book.author }}</td>>
                    <td>{{ book.title }}</td>
                    <td>{{ book.price }}</td>                                      
                </tr>
            {% endfor %}
            </tbody>
        </table>
        {% else %}
            <h4>No books with the specified criteria.</h4>
        {% endif %}
    {% endblock %}
    </div>
{% endblock %}
```