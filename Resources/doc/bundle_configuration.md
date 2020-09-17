# Bundle configuration

Bundle configuration options. Configuration must be specified at path `artur_doruch_list`.

### paginators

**type**: `array`

The list of paginator class namespaces. 
Allows to register own paginators for database query or cursor (like Doctrine\ORM\Query).
The paginator class must implement the `ArturDoruch\ListBundle\Paginator\PaginatorInterface` interface.

### pagination

 * #### item_limits
   
   **type**: `array`   
   Specifies the limits of list items displayed per page. 
   These values can be overridden for a specific item list (in controller getting list items)
   by `ArturDoruch\ListBundle\Pagination::setItemLimits()` method.
   
 * #### page_items
 
   * prev_page_label
   
     **type**: `string` **default**: `&#8592; Previous`       
     The link label of the pagination item to the previous list page.

   * next_page_label

     **type**: `string` **default**: `Next &#8594;`       
     The link label of the pagination item to the next list page.

### query_parameter_names

Specifies URL query parameter names of the HTTP request getting the list items.

  * #### page
    **type**: `string` **default**: `page`  
    The name of the query "page" parameter.
     
  * #### limit
    **type**: `string` **default**: `limit`  
    The name of the query "limit" parameter.
          
  * #### sort
    **type**: `string` **default**: `sort`       
    The name of the query "sort" parameter.    

### query_sort_direction

Configures format of the URL query "sort" parameter of the HTTP request getting the list items.

 * #### asc 
    **type**: `string` **default**: `asc`     
    The word or sign for ascending sorting direction.
    
 * #### desc 
    **type**: `string` **default**: `desc`     
    The word or sign for descending sorting direction.

 * #### position
    **type**: `string` **default**: `after`     
    Position of the sorting direction relative to the sorting field. Valid values are `before`, `after`.
    
 * #### separator
    **type**: `string` **default**: `:`     
    Separator between values of sorting direction and sorting field.    

### filter_form

Options of the form, filtering the list items.

 * #### display_reset_button
     **type**: `boolean` **default**: `true`  
     Whether to display button resetting the filter form elements.
  
 * #### reset_sorting
      **type**: `boolean` **default**: `false`   
      Whether to reset list sorting after filtering the list. If true query `sort` parameter is removed from the request query.
      

## Default configuration
      
```yml
artur_doruch_list:
    # Paginator class namespaces.
    paginators: []
    pagination:
        item_limits: []
        page_items:
            prev_page_label: '&#8592; Previous'
            next_page_label: 'Next &#8594;'
    query_parameter_names:
        page: page
        limit: limit
        sort: sort
    query_sort_direction:
        asc: asc
        desc: desc
        position: after # One of "before"; "after"
        separator: ':'
    filter_form:
        # Whether to display button resetting the filter form elements.
        display_reset_button: true
        # Whether to reset list sorting after filtering the list.
        reset_sorting: false
```