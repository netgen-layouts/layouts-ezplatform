Netgen Block Manager & eZ Publish integration installation instructions
=======================================================================

Use Composer to install the integration
---------------------------------------

Run the following command to install Netgen Block Manager & eZ Publish integration:

```
composer require netgen/block-manager-ezpublish:^1.0
```

Activating integration bundle
-----------------------------

After completing standard Block Manager install instructions, you also need to activate `NetgenEzPublishBlockManagerBundle`. Make sure it is activated after all other Block Manager bundles.

```
...

$bundles[] = new Netgen\Bundle\BlockManagerAdminBundle\NetgenBlockManagerAdminBundle();
$bundles[] = new Netgen\Bundle\EzPublishBlockManagerBundle\NetgenEzPublishBlockManagerBundle();

return $bundles;
```

Configuring your main pagelayout template
-----------------------------------------

To configure which template is your main pagelayout, use the following semantic configuration
somewhere in your application:

```
netgen_block_manager:
    pagelayout: "NetgenSiteBundle::pagelayout.html.twig"
```

If using eZ Platform 1.3 or later, there's no need setting the main pagelayout, since it will be
picked up automatically from your pagelayout siteaccess config.
