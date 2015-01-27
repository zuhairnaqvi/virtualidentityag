Getting Started with the TwitterBundle
======================================

This bundle is part of a collection of bundles that ease the access to social
media channels. The TwitterBundle is self contained, but can be integrated
with the AggregatorBundle with which multiple social media channels can be
joined.

## Prerequisites

This version of the bundle requires Symfony 2.1+ and therefore also PHP 5.3+.

## Installation

1. Download the TwitterBundle using composer
2. Enable the Bundle
3. Configure your application's security.yml
4. Import the configuration in your config.yml
5. Import the TwitterBundle routing
6. Update your database schema
7. Configure the TwitterBundle via browser interface
8. Create a cronjob for syncing your database

### Step 1: Download TwitterBundle using composer

Add TwitterBundle in your composer.json:

```js
{
    "require": {
        "virtualidentityag/hydra-twitter": "0.1.0"
    }
}
```

Now tell composer to download the bundle by running the command:

``` bash
$ php composer.phar update virtualidentityag/hydra-twitter
```

Composer will install the bundle to your project's `vendor/virtualidentityag`
directory. You will also notice that the tmhOauth and the knpPaginator
packages are also installed. TwitterBundle uses them for communicating with
the Twitter API and for paging the tweets on the moderation page.


### Step 2: Enable the bundle

Enable the bundle and its dependencies in the kernel:

``` php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new VirtualIdentity\TwitterBundle\TwitterBundle(),
        new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
    );
}
```

### Step 3: Configure your application's security.yml

You dont want everybody to approve or disapprove the tweets that show up
on your twitter feed. Therefore we will secure the administration area.

Below is a minimal example of the configuration necessary to secure the
TwitterBundle in your application:

``` yaml
# app/config/security.yml
security:
    encoders:
        Symfony\Component\Security\Core\User\User: plaintext

    role_hierarchy:
        ROLE_ADMIN: ROLE_USER

    providers:
        in_memory:
            memory:
                users:
                    admin: { password: adminpass, roles: [ 'ROLE_ADMIN' ] }

    firewalls:
        dev:
            pattern:  ^/(_(profiler|wdt)|css|images|js)/
            security: false

        secured_area:
            pattern: ^/hydra
            anonymous: ~
            http_basic:
                realm: "Virtual Identity Hydra"

    access_control:
        - { path: ^/hydra, roles: ROLE_ADMIN }
```

We provide the list of valid users with their passwords directly in the
configuration. If you wish a more sophisticated authentication method we
recommend using the FOSUserBundle.

### Step 4: Import the configuration in your config.yml

The TwitterBundle stores its configuration in a separate configuration file
because it is written by the administration interface and we dont want to
pollute the original symfony configuration. You have to perform the following
simple steps:

1) create the app/config/hydra.yml
2) import it in app/config/config.yml
3) include the TwitterBundle in the assetic configuration

#### Step 4.1: Create hydra.yml

``` bash
$ touch app/config/hydra.yml
```

#### Step 4.2: Import in config.yml

``` yaml
# app/config/config.yml
imports:
    - { resource: parameters.yml }
    - { resource: security.yml }
    # virtual identity social media configuration
    - { resource: hydra.yml }
```

#### Step 4.3: Assetic

Be sure the TwitterBundle is registered as assetic bundle so that joined
javascript and stylesheet files can be generated:

``` yaml
# app/config/config.yml
# Assetic Configuration
assetic:
    debug:          %kernel.debug%
    use_controller: false
    bundles:        ['VirtualIdentityTwitterBundle']
```

### Step 5: Import TwitterBundle routing configuration

Now that you have activated and secured the bundle and before we are able
to use the fancy configuration interface, we have to import the routing
configuration.

We will then have access to two pages: configuration and moderation

In YAML:

``` yaml
# app/config/routing.yml
virtual_identity_twitter:
    resource: "@VirtualIdentityTwitterBundle/Controller/"
    type:     annotation
    prefix:   /
```

### Step 6: Update your database schema

The TwitterBundle stores all fetched Tweets in your database if you call the
hydra:twitter:sync command from the command line interface or press the
sync-button in the admin area.

For doctrine run the following command.

``` bash
$ php app/console doctrine:schema:update --force
```

### Step 7: Configure the TwitterBundle via browser interface

Now you should have everything important set up correctly so you can now
enter the fancy administration area. Correct configuration can be a little
complicated, but we tried our best to make it as easy as possible. In fact
the process is straight forward:

1. Open the TwitterBundle configuration interface in your browser
2. Create a Twitter application with the correct urls set
3. Enter consumer key and consumer secret
4. Save the configuration
5. Sign in via Twitter to obtain the access token and secret
6. Sync your database

#### Step 7.1: Open TwitterBundle configuration

To start off we open a browser and navigate to http://example.com/hydra/twitter

You will notice that you have to enter your credentials set up in Step 3.
If you are not asked for credentials make sure you did Step 3 correctly.

In the administration interface you see the tweets that have already been
synchronized with your database. Normally there should be none by now.
Additionally you see a disabled "Sync"-Button on the right. The button
is disabled because your configuration is not set up correctly, TwitterBundle
cannot reach the Twitter API.

Click on "configuration" on the right.

Omg, the status says: "Credentials are either incorrectly configured or not
valid anymore"!!! We will fix this now :)

You will now notice that you have no Twitter Consumer Key nor Twitter Consumer
Secret. These are informations you can obtain quickly by creating a new
Twitter application. On the other hand, the token and the secret are not so
easy to obtain. But first things first.

#### Step 7.2: Create a Twitter application

Visit https://dev.twitter.com/apps and create your application. Make sure of
the following things:

1. As website enter https://example.com/hydra/twitter/
2. As callback url enter https://example.com/hydra/twitter/callbackurl
3. Make sure "Allow this application to be used to Sign in with Twitter" is
checked

Configure the rest of the settings as you wish (icon, description and so on).
By now the TwitterBundle only requires read-permissions, so you dont have to
change that.

#### Step 7.3: Enter Twitter application secrets in TwitterBundle configuration

Go back to your browser and navigate to http://example.com/hydra/twitter/configure
or simply click the "configuration" button on the right.

Copy&paste the "Consumer key" and the "Consumer secret" from the details page
of your Twitter application: https://dev.twitter.com/apps/[your_app_id]/show

#### Step 7.4: Save the configuration

Click "Save" on the bottom of the application.

The configuration is saved to app/config/hydra.yml.

#### Step 7.5: Obtain access token and secret

Now you should see a "Sign in with Twitter" button on the bottom of the page
besides the "Save" button.

Click it.

Authorize your application.

Tada! Token and secret should be filled out automatically.

Save your configuration by clicking "Save".

And again tada! Your status should be green now.

#### Step 7.6

If everything worked well until now, you should be able to synchronize your
database.

Click the "sync" menu point besides the "configuration" button and hopefully
get a success message.

You can now approve your tweets on the moderate page.

### Step 8: Create a cronjob for syncing your database

To sync the database from symfonys command line interface a hydra:twitter:sync
command exists:

``` bash
$ php app/console hydra:twitter:sync
```

To add it to your crontab open your crontab (linux/mac os x):

``` bash
$ EDITOR=nano crontab -e
```

Add the following line to sync your database every hour:

``` bash
0 * * * * php app/console hydra:twitter:sync
```

## Using the twitter service

The TwitterService exposes the following public methods:

* getters / modifiers
 * getFeed
 * getQueryBuilder
 * syncDatabase
 * getApi
 * setApproved($tweetId, true)
* twitter authentication and authorization methods
 * isAccessTokenValid
 * getAuthorizationParameters
 * getAccessToken
* setters
 * setSocialEntityClass('\YourNamespace\YourBundle\Entity\YourTwitterEntity')
 * setAutoApprove(true)
 * setHost('api.twitter.com')
 * setApiRequests(array('1.1/statuses/user_timeline.json'))
 * setAuthentication($consumerKey, $consumerSecret, $oauthToken, $oauthTokenSecret)

All those methods are used in one or the other way from the DefaultController
in the TwitterBundle. Take a look there if you look for examples of their
usage.

### Get the tweets

If the code where you want to receive the tweets is in a controller, the
following example shows how you can iterate over them.
``` php
    // src/YourNamespace/YourBundle/Controller/DefaultController.php

    /**
     * @Route("/yourapp/feed", name="your_namespace_feed")
     * @Template()
     */
    public function authorizeAction()
        $service = $this->get('virtual_identity_twitter');
        return array(
            'feed' => $service->getFeed()
        );
    }
```

If you want to filter specific tweets use the getQueryBuilder-method and
change the query according to your needs. You can find documentation for
the query builder here: http://docs.doctrine-project.org/en/latest/reference/query-builder.html

## Advanced

There are multiple possibilities to adopt the TwitterBundle to your needs.

### Using another TwitterEntity to store more fields

You can override the entity used by TwitterBundle setting the configuration
parameter virtual_identity_twitter.social_entity_class to the FQCN of your
implementation.

Make sure your Entity implements the
\VirtualIdentity\TwitterBundle\Interfaces\TwitterEntityInterface

The crazy thing is that the only thing you have to do to store more fields
from the response from the Twitter API is to implement correctly named setters.

Use the twitter api console https://dev.twitter.com/console to find out what
fields come back from twitter. If for example you would like to additionally
store the user name instead of the twitter handle your class could look like
this:

``` php
// src/YourNamespace/YourBundle/Entity/YourTwitterEntity.php
namespace YourNamespace\YourBundle\Entity;

use VirtualIdentity\TwitterBundle\Interfaces\TwitterEntityInterface;

class YourTwitterEntity implements TwitterEntityInterface
{
    /**
     * @var string
     *
     * @ORM\Column(name="userName", type="text", length="255")
     */
    protected $userName;

    /**
     * Get userName
     *
     * @return string
     */
    public function getUserName() {
        return $this->userName;
    }

    /**
     * Set userName
     *
     * @param string $userName
     * @return
     */
    public function setUserName($userName) {
        $this->userName = $userName;
    }

    // ... you can copy paste the rest from the original TwitterEntity.php
}
```

``` yaml
# app/config/config.yml
virtual_identity_twitter:
    social_entity_class: "\YourNamespace\YourBundle\Entity\YourTwitterEntity"
```

In fact this is all you have to do. Twitter returns the following response:

``` js
[
    {
    "created_at": "Wed Jul 31 12:49:32 +0000 2013",
    "id": 123456789012345678,
    "id_str": "123456789012345678",
    "text": "Some Tweet",
    "source": "<a href="http://twitter.com/download/iphone" rel="nofollow">Twitter for iPhone</a>",
    "truncated": false,
    "in_reply_to_status_id": null,
    "in_reply_to_status_id_str": null,
    "in_reply_to_user_id": null,
    "in_reply_to_user_id_str": null,
    "in_reply_to_screen_name": null,
    "user":  {
        "id": 12345678,
        "id_str": "12345678",
        "name": "Some user name",
        "screen_name": "twitterHandle",
        // ...
    }
    "entities":  {
        "hashtags":  [],
        "symbols":  [],
        "urls":  [],
        "user_mentions":  [
            {
                "screen_name": "twitterHandle",
                "name": "Some user name",
            }
        ]
    }
```

So if you want to store the field in_reply_to_status_id you have to implement
a setter called "setInReplyToStatusId".

If you want to store the first mention in the tweet you have to implement a
setter called "setEntitiesUserMentions0ScreenName".

The strategy used here is:

1. the response gets flattened to a 1-dimensional array preserving the keys
in a breadcrumb style
2. the setters of the entity are iterated and it is checked if a camelized
attribute exists that matches the setter.

### Disabling auto aproval of tweets

If you want to disable the default behaviour that tweets are automatically
approved you have to cange the following configuration parameter.

``` yaml
# app/config/config.yml
virtual_identity_twitter:
    auto_approve: false
```

### Events

#### post_approval_change
At the moment the only event fired is when you change the approval status in
the administration interface.

You can register to this event the same way the AggregatorBundle does:

``` yaml
# src/YourNamespace/YourBundle/Resources/config/services.yml

    your_namespace_your_subscriber:
        class: YourNamespace\YourBundle\EventSubscriber\YourSubscriber
        arguments: [@logger, @doctrine.orm.entity_manager]
        tags:
            - { name: kernel.event_listener, event: virtual_identity_twitter.post_approval_change, method: onApprovalChange }
```

Your class then receives a TweetChangedEvent:

``` php
// src/YourNamespace/YourBundle/Eventubscriber/YourSubscriber.php

namespace YourNamespace\YourBundle\Eventubscriber;

use VirtualIdentity\TwitterBundle\EventDispatcher\TweetChangedEvent;

class YourSubscriber
{
    public function onApprovalChange(TweetChangedEvent $event)
    {
        $tweet = $event->getTweet();
    }
}
```

## Troubleshooting

### Unicode

Most social media sites - as does twitter - support Unicode characters not
contained within the UTF-8 charset of MySQL<5.3.3. If you use Emoji or other
UTF8 4-byte characters you have to annotate your entities differently. We
decided to opt for simple utf8 to support older versions of MySQL too. If
you have mysql>=5.3.3 here is an example of how to correctly annotate your
entities:

``` php
// src/YourNamespace/YourBundle/Entity/TwitterEntity.php
namespace YourNamespace\YourBundle\Entity;

use VirtualIdentity\TwitterBundle\Interfaces\TwitterEntityInterface;

/**
 * TwitterEntity
 *
 * @ORM\Table(options={"charset"="utf8mb4","collate"="utf8mb4_unicode_ci"})
 * @ORM\Entity
 */
class YourTwitterEntity implements TwitterEntityInterface
{
    //...
}
```