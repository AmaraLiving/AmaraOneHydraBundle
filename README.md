# AmaraOneHydraBundle
Bundle to integrate [Amara's OneHydra library](https://github.com/AmaraLiving/php-onehydra) with Symfony2.

Installation
------------

Use composer:
```
composer require amara/onehydra-bundle
```

Example usage
-------------
After enabling the bundle in your application, the minimal configuration for config.yml looks like:

```yaml
amara_one_hydra:
    is_uat: true
    programs:
        your_program_id:
            auth_token: authtoken1
```

After creating the table for the entity, you'll want to run the fetch command to populate them from the OneHydra API:

```
app/console onehydra:fetch --all --programId=your_program_id
```

You will now be able to look up the OneHydra pages from your controllers etc.

```php
// Get the entity from our system for the current request
$oneHydraPage = $this->get('amara_one_hydra.page_manager')->getPageByRequest($request);

if ($oneHydraPage) {
    // Get the PageInterface object which reflects what's on the OneHydra system 
    $page = $oneHydraPage->getPage();
    
    // Override what you want based on the content of page...
    $title = $page->getTitle();
}
```





