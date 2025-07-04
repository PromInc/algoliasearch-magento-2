Algolia Search & Discovery extension for Magento 2
==================================================

![Latest version](https://img.shields.io/badge/latest-3.15.1-green)
![Magento 2](https://img.shields.io/badge/Magento-2.4.6+-orange)

![PHP](https://img.shields.io/badge/PHP-8.1%2C8.2%2C8.3-blue)

[![CircleCI](https://circleci.com/gh/algolia/algoliasearch-magento-2/tree/master.svg?style=svg)](https://circleci.com/gh/algolia/algoliasearch-magento-2/tree/master)

-------

## Features

The Algolia extension replaces the default search in Magento Open Source and Adobe Commerce with a robust autocomplete search menu and Instantsearch results page.

This extension replaces the default search of Magento with a typo-tolerant, fast & relevant search experience backed by Algolia. It's based on [algoliasearch-client-php](https://github.com/algolia/algoliasearch-client-php), [autocomplete.js](https://github.com/algolia/autocomplete.js) and [instantsearch.js](https://github.com/algolia/instantsearch.js).

- **Autocompletion menu:** Provide your entire catalog to End-Users instantly via the dropdown menu, regardless of the number of categories or attributes it contains.

- **Instantsearch results page:** Have your search results page, navigation and pagination updated in realtime, after each keystroke.

- **Recommend:** Algolia Recommend lets you display recommendations such as "Frequently Bought Together" and "Related Products" features on the product detail page.

Learn more at our [official website Adobe Commerce / Magento](https://www.algolia.com/search-solutions/adobe-commerce-magento/)

### Demo

Try the Autocomplete and the InstantSearch results page on our [live demo](https://flagship-magento.algolia.com).


## Magento 2.4 compatibility & extension versions End of Life

Magento 2.3 and earlier versions are no longer supported by the Algolia extension.

Version 3.x of our extension is compatible with Magento 2.4. Review the [Customisation](https://github.com/algolia/algoliasearch-magento-2#customisation) section to learn more about the differences between our extension versions.

| Extension Version | End of Life | Magento                      | PHP                                    |
|-------------------|-------------|------------------------------|----------------------------------------|
| v3.7.x            | 10/10/2023  | `~2.3.7\|\|~2.4.5\|\|~2.4.6` | `~7.3.0\|\|~7.4.0\|\|~8.1.0\|\|~8.2.0` |
| v3.8.x            | 3/8/2023    | `~2.4.5\|\|~2.4.6`           | `~7.4.0\|\|~8.1.0\|\|~8.2.0`           |
| v3.9.x            | 10/13/2023  | `~2.4.5\|\|~2.4.6`           | `~7.4.0\|\|~8.1.0\|\|~8.2.0`           |
| v3.10.x           | 12/12/2023  | `~2.4.6`                     | `~8.1.0\|\|~8.2.0`                     |
| v3.11.x           | 1/26/2024   | `~2.4.6`                     | `~8.1.0\|\|~8.2.0`                     |
| v3.12.x           | 8/2/2024    | `~2.4.6`                     | `~8.1.0\|\|~8.2.0`                     |
| v3.13.x           | N/A         | `~2.4.6`                     | `~8.1.0\|\|~8.2.0`                     |
| v3.14.x           | N/A         | `~2.4.6\|\|~2.4.7`           | `~8.1.0\|\|~8.2.0\|\|~8.3.0`           |
| v3.15.x           | N/A         | `~2.4.6\|\|~2.4.7`           | `~8.1.0\|\|~8.2.0\|\|~8.3.0`           |

## Documentation

- [Quick-Start and Installation](https://www.algolia.com/doc/integration/magento-2/getting-started/quick-start/)
- [General FAQs](https://www.algolia.com/doc/integration/magento-2/troubleshooting/general-faq/)
- [Technical Troubleshooting Guide](https://www.algolia.com/doc/integration/magento-2/troubleshooting/technical-troubleshooting/)
- [Indexing Queue](https://www.algolia.com/doc/integration/magento-2/how-it-works/indexing-queue/)
- [Frontend Custom Events](https://www.algolia.com/doc/integration/magento-2/customize/custom-front-end-events/)
- [Dispatched Backend Events](https://www.algolia.com/doc/integration/magento-2/customize/custom-back-end-events/)


### Installation

The easiest way to install the extension is to use [Composer](https://getcomposer.org/) and follow our [getting started guide](https://www.algolia.com/doc/integration/magento-2/getting-started/quick-start/).

If you would like to stay on a minor version, please upgrade your composer to only accept minor versions. The following example will keep you on the minor version and will update patches automatically.

`"algolia/algoliasearch-magento-2": "~3.15.0"`

### Customisation

The extension uses libraries to help assist with the frontend implementation for autocomplete, instantsearch, and insight features. It also uses the Algolia PHP client to leverage indexing and search methods from the backend. When you approach customisations for either, you have to understand that you are customising the implementation itself and not the components it is based on.

These libraries are here to help add to your customisation but because the extension has already initialised these components, you should use hooks into the area between the extension and the libraries.
Please check our [Custom Extension](https://github.com/algolia/algoliasearch-custom-algolia-magento-2) for refrence 

### Frontend JavaScript libraries

> As of v3.15.x the JavaScript bundle `algoliaBundle` has been removed as a hard dependency for the Magento extension and will be removed entirely in the next minor release. Libraries can now be swapped independently and loaded via RequireJS.  

Knowing the version of each Algolia JavaScript library will help you understand what is available for you to leverage in terms of customisation. This table will help you determine which documentation to reference when you start working on your customisation.

| Extension Version | 	autocomplete.js                                                   | instantsearch.js                                                                   | search-insights.js                                                   | recommend-js.js                                             |
|-----------------|--------------------------------------------------------------------|------------------------------------------------------------------------------------|----------------------------------------------------------------------|-------------------------------------------------------------|
| v3.x            | [0.38.0](https://github.com/algolia/autocomplete.js/tree/v0.38.0)* | [4.15.0](https://github.com/algolia/instantsearch.js/tree/v4.15.0)*                | [1.7.1](https://github.com/algolia/search-insights.js/tree/v1.7.1)   | NA                                                          |
| v3.9.1          | [1.6.3](https://github.com/algolia/autocomplete.js/tree/v1.6.3)*   | [4.41.0](https://github.com/algolia/instantsearch.js/tree/v4.41.0)*                | [1.7.1](https://github.com/algolia/search-insights.js/tree/v1.7.1)   | [1.5.0](https://github.com/algolia/recommend/tree/v1.5.0)   |
| v3.10.x         | [1.6.3](https://github.com/algolia/autocomplete.js/tree/v1.6.3)*   | [4.41.0](https://github.com/algolia/instantsearch.js/tree/v4.41.0)*                | [1.7.1](https://github.com/algolia/search-insights.js/tree/v1.7.1)   | [1.8.0](https://github.com/algolia/recommend/tree/v1.8.0)   |
| v3.11.0         | [1.6.3](https://github.com/algolia/autocomplete.js/tree/v1.6.3)*   | [4.41.0](https://github.com/algolia/instantsearch.js/tree/v4.41.0)*                | [2.6.0](https://github.com/algolia/search-insights.js/tree/v2.6.0)   | [1.8.0](https://github.com/algolia/recommend/tree/v1.8.0)   |
| v3.13.0         | [1.6.3](https://github.com/algolia/autocomplete.js/tree/v1.6.3)*   | [4.63.0](https://github.com/algolia/instantsearch/tree/instantsearch.js%404.63.0)* | [2.11.0](https://github.com/algolia/search-insights.js/tree/v2.11.0) | [1.8.0](https://github.com/algolia/recommend/tree/v1.8.0)   |
| v3.14.x         | [1.6.3](https://github.com/algolia/autocomplete.js/tree/v1.6.3)*   | [4.63.0](https://github.com/algolia/instantsearch/tree/instantsearch.js%404.63.0)* | [2.11.0](https://github.com/algolia/search-insights.js/tree/v2.11.0) | [1.15.0](https://github.com/algolia/recommend/tree/v1.15.0) |
| v3.15.x         | [1.17.9](https://github.com/algolia/autocomplete.js/tree/v1.17.9)  | [4.77.0](https://github.com/algolia/instantsearch/tree/instantsearch.js%404.77.0)  | [2.17.3](https://github.com/algolia/search-insights.js/tree/v2.17.3) | [1.16.0](https://github.com/algolia/recommend/tree/v1.16.0) |

&ast; In earlier versions of the extension, the Autocomplete and InstantSearch libraries were accessible via the `algoliaBundle` global. This bundle was a prepackaged JavaScript file that contained dependencies for the frontend experience. What was included in this bundle can be seen here: https://github.com/algolia/algoliasearch-extensions-bundle/blob/ISv4/package.json

Refer to these docs when customising your Algolia Magento extension frontend features:
 - [Autocomplete](https://www.algolia.com/doc/integration/magento-2/customize/autocomplete-menu/)
 - [Instantsearch](https://www.algolia.com/doc/integration/magento-2/customize/instant-search-page/)
 - [Frontend Custom Events](https://www.algolia.com/doc/integration/magento-2/customize/custom-front-end-events/)

### JavaScript bundling

Libraries should be fully compatible with [standard JavaScript bundling](https://developer.adobe.com/commerce/frontend-core/guide/themes/js-bundling/). If using ["advanced bundling"](https://experienceleague.adobe.com/en/docs/commerce-operations/performance-best-practices/performance-best-practices/advanced-js-bundling) via the [RequireJS optimizer](https://requirejs.org/docs/optimization.html) it will be necessary to first apply the Babel transpiler to the underlying source code.

A sample transpiler configuration (`.babelrc`) might look as follows:  

```json
{
    "presets": [
        [
            "@babel/preset-env",
            {
                "exclude": ["@babel/plugin-transform-template-literals"]
            }
        ],
        ["minify", { "builtIns": false, "mangle": false }]
    ],
    "comments": false
}
```


### The Algolia PHP API Client

The extension does most of the heavy lifting when it comes to gathering and preparing the data needed for indexing to Algolia. In terms of interacting with the Algolia Search API, the extension leverages the PHP API Client for backend methods including indexing, configuration, and search queries.

Depending on the extension version you are using, you could have a different PHP API client version powering the extension's backend functionality.

| Extension Version | API Client Version                                                      |
|-------------------|-------------------------------------------------------------------------|
| v3.x              | [2.5.1](https://github.com/algolia/algoliasearch-client-php/tree/2.5.1) |
| v3.6.x            | [3.2.0](https://github.com/algolia/algoliasearch-client-php/tree/3.2.0) |
| v3.11.0          | [3.3.2](https://github.com/algolia/algoliasearch-client-php/tree/3.3.2) |          
| >=v3.14.x         | [4.0.x](https://github.com/algolia/algoliasearch-client-php/tree/4.0.0-beta.12)                                                               |

Refer to these docs when customising your Algolia Magento extension backend:
- [Indexing](https://www.algolia.com/doc/integration/magento-2/how-it-works/indexing/)
- [Dispatched Backend Events](https://www.algolia.com/doc/integration/magento-2/customize/custom-back-end-events/)

## Support

For feedback, bug reporting, or unresolved issues with the extension, please visit our [Support Center](https://support.algolia.com/hc/en-us/) where you can search the knowldge base and contact the Support team. Please include your Magento version, extension version, application ID, and steps to reproducing your issue. Add additional information like screenshots, screencasts, and error messages to help our team better troubleshoot your issues.

## Contributing

To start contributing to the extension follow the [contributing guildelines](.github/CONTRIBUTING.md).
