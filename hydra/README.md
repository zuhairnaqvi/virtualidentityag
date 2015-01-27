README
======

What is Hydra?
--------------

Hydra is a social media channel aggregator. Using other bundles it modulary
built to combine multiple API-requests to different social media websites
like facebook, twitter, instagram or youtube.

A nice bootstraped user interface is available for you to configure your
different social media channels and api requests. Its also easy to decide
which entries that have been synced from your feeds should appear in your
aggregated feed.

Hydra can be used to develop all kind of social media feed renderers.

Requirements
------------

* Hydra is only supported on PHP 5.3.3 and up.
* We love Symfony and built this bundle for usage with it (Version 2.3+ required).
* Hydra is built upon Social Harvester Bundles. At the moment 4 of them exist,
they are installed automatically.

Installation & Documentation
----------------------------

Unfortunately docs are missing at the moment. Normally the steps
required to install this bundle are:

1. Download Hydra using composer (virtualidentityag/hydra 0.1.0)
2. Enable the Bundle in AppKernel.php
3. Configure your application's security.yml
4. Import the configuration in your config.yml (import hydra.yml)
5. Import the DefaultController into your routing.yml using annotations
6. Update your database schema using doctrine:schema:update --force
7. Create a cronjob for syncing your database using the hydra:sync CLI command

Contributing
------------

Hydra is an open source project supported by Virtual Identity AG. If you'd like
to contribute, please contact the virtual identity development team saga -
[dev.saga@virtual-identity.com][1]

[1]: mailto:dev.saga@virtual-identity.com
