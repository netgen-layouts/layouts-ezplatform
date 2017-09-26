Netgen Block Manager & eZ Publish integration installation instructions
=======================================================================

Use Composer to install the integration
---------------------------------------

Run the following command to install Netgen Block Manager & eZ Publish
integration:

```
composer require netgen/block-manager-ezpublish:^1.0
```

Activating integration bundle
-----------------------------

After completing standard Block Manager install instructions, you also need to
activate `NetgenEzPublishBlockManagerBundle`. Make sure it is activated after
all other Block Manager bundles.

```
...

$bundles[] = new Netgen\Bundle\BlockManagerAdminBundle\NetgenBlockManagerAdminBundle();
$bundles[] = new Netgen\Bundle\EzPublishBlockManagerBundle\NetgenEzPublishBlockManagerBundle();

return $bundles;
```

Activating legacy eZ Publish extension
--------------------------------------

If you use eZ Publish Legacy admin interface in your eZ Platform installation,
you might want to activate `nglayouts` legacy extension to be able to add
`nglayouts/admin` and `nglayouts/editor` policies to your roles.

Add the following to your legacy `site.ini.append.php` to activate the
extension:

```
[ExtensionSettings]
ActiveExtensions[]=nglayouts
```

Configuring your main pagelayout template
-----------------------------------------

To configure which template is your main pagelayout, use the following semantic
configuration somewhere in your application:

```
netgen_block_manager:
    pagelayout: "@NetgenSite/pagelayout.html.twig"
```

If using eZ Platform 1.3 or later, there's no need setting the main pagelayout,
since it will be picked up automatically from your pagelayout siteaccess config.
