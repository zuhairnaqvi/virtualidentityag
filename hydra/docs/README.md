Documentation - Stub
====================

Adding a new service bundle
---------------------------

* create the bundle. Following constraints:
  * the main-service must implement a "getFeed" method
  * when the service changes the approval status it must dispatch a event
  * there must be a /hydra/socialService/configuration and a /hydra/socialService/moderation action
* change the aggreator to use the new bundle
  * in services.yml add the service to the setHarvestedServices-method
  * in services.yml add the event to the tags-attribute of the aggregator subscriber
  * add a new mapper-method in the AggregatorConverterService
  * call the new mapper-method in the syncDatabase-method of the AggregatorService