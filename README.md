# Price Aggregator

A web based tool for combining a number of predefined _price-lists_ into a single document with unique items and the best prices for them. All _price-lists_ are xlsx or xls files and have different format but all of them have list of _items_ with _article_, _title_ and _price_ attributes.

The articles don't overlap even for the same _items_ from different _price-lists_. The biggest chellenge here is to parse _title_ attribute in order to find all existing _analogue_ (items with the same propertyes but different _price_).

## Possible solutions

To solve this problem we can apply different approaches. Some of them:

1. Dictionaries
We have as many dictionaries as many attributes we want to find for each item. Simply scan each item title for values from each dictionary in order to determine a known value of the property.

2. Links
Besides the articles don't overlap they don't hardly ever are changed. We can use this in order to manually/automatically/semi-automatically link each item with all its analogues. Once the link is created we can process files easily.

3. ML model
Teach ML model in order to understand all those titles and be able to distince all required properties from it with high enough precision.

In a number of reasons we decided to kick off with solution No 1 (dictionaries). At least for POC.

## Milestones

1. Upload a desired number of price-lists, simply combine all their data into a _output document_ and let customers to download it.
2. Enrich the _output  document_ with metadata
    2.1 brand
    2.1 product type (perfume, soup, candle, etc)
    2.3 TBD
3. Group the products by _brand_ and leave the best price analogue only

## TODO

* UploadFilesRequest validator give more miningful errors description