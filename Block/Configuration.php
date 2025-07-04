<?php

namespace Algolia\AlgoliaSearch\Block;

use Algolia\AlgoliaSearch\Helper\ConfigHelper;
use Algolia\AlgoliaSearch\Helper\InsightsHelper;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Data\CollectionDataSourceInterface;
use Magento\Framework\DataObject;

class Configuration extends Algolia implements CollectionDataSourceInterface
{
    //Placeholder for future implementation (requires custom renderer for hierarchicalMenu widget)
    private const IS_CATEGORY_NAVIGATION_ENABLED = false;

    public function isSearchPage(): bool
    {
        if ($this->getConfigHelper()->isInstantEnabled()) {
            /** @var Http $request */
            $request = $this->getRequest();

            if ($request->getFullActionName() === 'catalogsearch_result_index' || $this->isLandingPage()) {
                return true;
            }

            if ($this->getConfigHelper()->replaceCategories() && $request->getControllerName() === 'category') {
                $category = $this->getCurrentCategory();
                if ($category && $category->getDisplayMode() !== 'PAGE') {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param \Magento\Catalog\Model\Category $cat
     * @return string
     */
    protected function initCategoryParentPath(\Magento\Catalog\Model\Category $cat): string {
        $path = '';
        foreach ($cat->getPathIds() as $treeCategoryId) {
            if ($path) {
                $path .= $this->getConfigHelper()->getCategorySeparator($this->getStoreId());
            }
            $path .= $this->getCategoryHelper()->getCategoryName($treeCategoryId, $this->getStoreId());
        }
        return $path;
    }

    /**
     * @param \Magento\Catalog\Model\Category $cat
     * @param string $parent
     * @param array $arr
     * @return array
     */
    protected function getChildCategoryUrls(\Magento\Catalog\Model\Category $cat, string $parent = '', array $arr = array()): array {
        if (!$parent) {
            $parent = $this->initCategoryParentPath($cat);
        }

        foreach ($cat->getChildrenCategories() as $child) {
            $key = $parent ? $parent . $this->getConfigHelper()->getCategorySeparator($this->getStoreId()) . $child->getName() : $child ->getName();
            $arr[$key]['url'] = $child->getUrl();
            $arr = array_merge($arr, $this->getChildCategoryUrls($child, $key, $arr));
        }
        return $arr;
    }

    public function getConfiguration()
    {
        $config = $this->getConfigHelper();

        $catalogSearchHelper = $this->getCatalogSearchHelper();

        $coreHelper = $this->getCoreHelper();

        $categoryHelper = $this->getCategoryHelper();

        $suggestionHelper = $this->getSuggestionHelper();

        $algoliaHelper = $this->getAlgoliaHelper();

        $persoHelper = $this->getPersonalizationHelper();

        $baseUrl = rtrim($this->getBaseUrl(), '/');

        $currencyCode = $this->getCurrencyCode();
        $currencySymbol = $this->getCurrencySymbol();
        $priceFormat = $this->getPriceFormat();

        $customerGroupId = $this->getGroupId();

        $priceKey = $this->getPriceKey();
        $priceGroup = null;
        if ($config->isCustomerGroupsEnabled()) {
            $pricegroupArray = explode('.', $priceKey);
            $priceGroup = $pricegroupArray[2];
        }

        $query = '';
        $refinementKey = '';
        $refinementValue = '';
        $path = '';
        $level = '';
        $categoryId = '';
        $parentCategoryName = '';
        $childCategories = [];

        $addToCartParams = $this->getAddToCartParams();

        /** @var Http $request */
        $request = $this->getRequest();

        /**
         * Handle category replacement
         */

        $isCategoryPage = false;
        if ($config->isInstantEnabled()
            && $config->replaceCategories()
            && $request->getControllerName() === 'category') {
            $category = $this->getCurrentCategory();

            if ($category && $category->getDisplayMode() !== 'PAGE') {
                $category->getUrlInstance()->setStore($this->getStoreId());
                if (self::IS_CATEGORY_NAVIGATION_ENABLED) {
                    $childCategories = $this->getChildCategoryUrls($category);
                }

                $categoryId = $category->getId();

                $level = -1;
                foreach ($category->getPathIds() as $treeCategoryId) {
                    if ($path !== '') {
                        $path .= $config->getCategorySeparator();
                    } else {
                        $parentCategoryName = $categoryHelper->getCategoryName($treeCategoryId, $this->getStoreId());
                    }

                    $path .= $categoryHelper->getCategoryName($treeCategoryId, $this->getStoreId());

                    if ($path) {
                        $level++;
                    }
                }

                $isCategoryPage = true;
            }
        }

        $productId = null;
        if ($config->isClickConversionAnalyticsEnabled() && $request->getFullActionName() === 'catalog_product_view') {
            $productId = $this->getCurrentProduct()->getId();
        }

        /**
         * Handle search
         */
        $facets = $config->getFacets();

        $areCategoriesInFacets = $this->areCategoriesInFacets($facets);

        if ($config->isInstantEnabled()) {
            $pageIdentifier = $request->getFullActionName();

            if ($pageIdentifier === 'catalogsearch_result_index') {
                $query = $this->getRequest()->getParam($catalogSearchHelper->getQueryParamName());

                if ($query === '__empty__') {
                    $query = '';
                }

                $refinementKey = $this->getRequest()->getParam('refinement_key');

                if ($refinementKey !== null) {
                    $refinementValue = $query;
                    $query = '';
                } else {
                    $refinementKey = '';
                }
            }
        }

        $attributesToFilter = $config->getAttributesToFilter($customerGroupId);
        $algoliaJsConfig = [
            'instant' => [
                'enabled' => $config->isInstantEnabled(),
                'selector' => $config->getInstantSelector(),
                'isAddToCartEnabled' => $config->isAddToCartEnable(),
                'addToCartParams' => $addToCartParams,
                'infiniteScrollEnabled' => $config->isInfiniteScrollEnabled(),
                'urlTrackedParameters' => $this->getUrlTrackedParameters(),
                'isSearchBoxEnabled' => $config->isInstantSearchBoxEnabled(),
                'isVisualMerchEnabled' => $config->isVisualMerchEnabled(),
                'categorySeparator' => $config->getCategorySeparator(),
                'categoryPageIdAttribute' => $config->getCategoryPageIdAttributeName(),
                'isCategoryNavigationEnabled' => self::IS_CATEGORY_NAVIGATION_ENABLED,
                'hidePagination' => $config->hidePaginationInInstantSearchPage()
            ],
            'autocomplete' => [
                'enabled' => $config->isAutoCompleteEnabled(),
                'selector' => $config->getAutocompleteSelector(),
                'sections' => $config->getAutocompleteSections(),
                'nbOfProductsSuggestions' => $config->getNumberOfProductsSuggestions(),
                'nbOfCategoriesSuggestions' => $config->getNumberOfCategoriesSuggestions(),
                'nbOfQueriesSuggestions' => $config->getNumberOfQueriesSuggestions(),
                'isDebugEnabled' => $config->isAutocompleteDebugEnabled(),
                'isNavigatorEnabled' => $config->isAutocompleteNavigatorEnabled(),
                'debounceMilliseconds' => $config->getAutocompleteDebounceMilliseconds(),
                'minimumCharacters' => $config->getAutocompleteMinimumCharacterLength()
            ],
            'landingPage' => [
                'query' => $this->getLandingPageQuery(),
                'configuration' => $this->getLandingPageConfiguration(),
            ],
            'recommend' => [
                'enabledFBT' => $config->isRecommendFrequentlyBroughtTogetherEnabled(),
                'enabledRelated' => $config->isRecommendRelatedProductsEnabled(),
                'enabledFBTInCart' => $config->isRecommendFrequentlyBroughtTogetherEnabledOnCartPage(),
                'enabledRelatedInCart' => $config->isRecommendRelatedProductsEnabledOnCartPage(),
                'enabledLookingSimilar' => $config->isRecommendLookingSimilarEnabled(),
                'limitFBTProducts' => $config->getNumberOfFrequentlyBoughtTogetherProducts(),
                'limitRelatedProducts' => $config->getNumberOfRelatedProducts(),
                'limitTrendingItems' => $config->getNumberOfTrendingItems(),
                'limitLookingSimilar' => $config->getNumberOfLookingSimilar(),
                'enabledTrendItems' => $config->isRecommendTrendingItemsEnabled(),
                'trendItemFacetName' => $config->getTrendingItemsFacetName(),
                'trendItemFacetValue' => $config->getTrendingItemsFacetValue(),
                'isTrendItemsEnabledInPDP' => $config->isTrendItemsEnabledInPDP(),
                'isTrendItemsEnabledInCartPage' => $config->isTrendItemsEnabledInShoppingCart(),
                'isAddToCartEnabledInFBT' => $config->isAddToCartEnabledInFrequentlyBoughtTogether(),
                'isAddToCartEnabledInRelatedProduct' => $config->isAddToCartEnabledInRelatedProducts(),
                'isAddToCartEnabledInTrendsItem' => $config->isAddToCartEnabledInTrendsItem(),
                'isAddToCartEnabledInLookingSimilar' => $config->isAddToCartEnabledInLookingSimilar(),
                'FBTTitle' => __($config->getFBTTitle()),
                'relatedProductsTitle' => __($config->getRelatedProductsTitle()),
                'trendingItemsTitle' => __($config->getTrendingItemsTitle()),
                'addToCartParams' => $addToCartParams,
                'isLookingSimilarEnabledInPDP' => $config->isLookingSimilarEnabledInPDP(),
                'isLookingSimilarEnabledInCartPage' => $config->isLookingSimilarEnabledInShoppingCart(),
                'lookingSimilarTitle' => __($config->getLookingSimilarTitle())
            ],
            'extensionVersion' => $config->getExtensionVersion(),
            'applicationId' => $config->getApplicationID(),
            'indexName' => $coreHelper->getBaseIndexName(),
            'apiKey' => $algoliaHelper->generateSearchSecuredApiKey(
                $config->getSearchOnlyAPIKey(),
                $attributesToFilter,
                $this->getStoreId()
            ),
            'attributeFilter' => $attributesToFilter,
            'facets' => $facets,
            'areCategoriesInFacets' => $areCategoriesInFacets,
            'hitsPerPage' => $config->getNumberOfProductResults(),
            'sortingIndices' => array_values($this->sortingTransformer->getSortingIndices(
                $this->getStoreId(),
                $customerGroupId
            )),
            'isSearchPage' => $this->isSearchPage(),
            'isCategoryPage' => $isCategoryPage,
            'isLandingPage' => $this->isLandingPage(),
            'removeBranding' => (bool) $config->isRemoveBranding(),
            'productId' => $productId,
            'priceKey' => $priceKey,
            'priceGroup' => $priceGroup,
            'origFormatedVar' => 'price' . $priceKey . '_original_formated',
            'tierFormatedVar' => 'price' . $priceKey . '_tier_formated',
            'currencyCode' => $currencyCode,
            'currencySymbol' => $currencySymbol,
            'priceFormat' => $priceFormat,
            'maxValuesPerFacet' => (int) $config->getMaxValuesPerFacet(),
            'autofocus' => true,
            'resultPageUrl' => $this->getCatalogSearchHelper()->getResultUrl(),
            'request' => [
                'query' =>  htmlspecialchars(html_entity_decode((string)$query)),
                'refinementKey' => $refinementKey,
                'refinementValue' => $refinementValue,
                'categoryId' => $categoryId,
                'landingPageId' => $this->getLandingPageId(),
                'path' => $path,
                'level' => $level,
                'parentCategory' => $parentCategoryName,
                'childCategories' => $childCategories,
                'url' => $this->getUrl('*/*/*', ['_use_rewrite' => true, '_forced_secure' => true])
            ],
            'showCatsNotIncludedInNavigation' => $config->showCatsNotIncludedInNavigation(),
            'showSuggestionsOnNoResultsPage' => $config->showSuggestionsOnNoResultsPage(),
            'baseUrl' => $baseUrl,
            'popularQueries' => $suggestionHelper->getPopularQueries($this->getStoreId()),
            'useAdaptiveImage' => $config->useAdaptiveImage(),
            'urls' => [
                'logo' => $this->getViewFileUrl('Algolia_AlgoliaSearch::js/images/algolia-logo-blue.svg'),
            ],
            'cookieConfiguration' => [
                'customerTokenCookie' => InsightsHelper::ALGOLIA_CUSTOMER_USER_TOKEN_COOKIE_NAME,
                'consentCookieName' => $config->getDefaultConsentCookieName(),
                'cookieAllowButtonSelector' => $config->getAllowCookieButtonSelector(),
                'cookieRestrictionModeEnabled' => $config->isCookieRestrictionModeEnabled(),
                'cookieDuration' =>$config->getAlgoliaCookieDuration()
            ],
            'ccAnalytics' => [
                'enabled' => $config->isClickConversionAnalyticsEnabled(),
                'ISSelector' => $config->getClickConversionAnalyticsISSelector(),
                'conversionAnalyticsMode' => $config->getConversionAnalyticsMode(),
                'addToCartSelector' => $config->getConversionAnalyticsAddToCartSelector(),
                'orderedProductIds' => $this->getOrderedProductIds($config, $request),
            ],
            'isPersonalizationEnabled' => $persoHelper->isPersoEnabled(),
            'personalization' => [
                'enabled' => $persoHelper->isPersoEnabled(),
                'viewedEvents' => [
                    'viewProduct' => [
                        'eventName' => __('Viewed Product'),
                        'enabled' => $persoHelper->isViewProductTracked(),
                        'method' => 'viewedObjectIDs',
                    ],
                ],
                'clickedEvents' => [
                    'productClicked' => [
                        'eventName' => __('Product Clicked'),
                        'enabled' => $persoHelper->isProductClickedTracked(),
                        'selector' => $persoHelper->getProductClickedSelector(),
                        'method' => 'clickedObjectIDs',
                    ],
                    'productRecommended' => [
                        'eventName' => __('Recommended Product Clicked'),
                        'enabled' => $persoHelper->isProductRecommendedTracked(),
                        'selector' => $persoHelper->getProductRecommendedSelector(),
                        'method' => 'clickedObjectIDs',
                    ],
                ],
                'filterClicked' => [
                    'eventName' => __('Filter Clicked'),
                    'enabled' => $persoHelper->isFilterClickedTracked(),
                    'method' => 'clickedFilters',
                ],
            ],
            'analytics' => $config->getAnalyticsConfig(),
            'now' => $this->getTimestamp(),
            'queue' => [
                'isEnabled' => $config->isQueueActive($this->getStoreId()),
                'nbOfJobsToRun' => $config->getNumberOfJobToRun($this->getStoreId()),
                'retryLimit' => $config->getRetryLimit($this->getStoreId()),
                'nbOfElementsPerIndexingJob' => $config->getNumberOfElementByPage($this->getStoreId()),
            ],
            'isPreventBackendRenderingEnabled' => $config->preventBackendRendering($this->getStoreId()),
            'translations' => [
                'to' => __('to'),
                'or' => __('or'),
                'go' => __('Go'),
                'popularQueries' => __('You can try one of the popular search queries'),
                'seeAll' => __('See all products'),
                'allDepartments' => __('All departments'),
                'seeIn' => __('See products in'),
                'orIn' => __('or in'),
                'noProducts' => __('No products for query'),
                'noResults' => __('No results'),
                'refine' => __('Refine'),
                'selectedFilters' => __('Selected Filters'),
                'clearAll' => __('Clear all'),
                'previousPage' => __('Previous page'),
                'nextPage' => __('Next page'),
                'searchFor' => __('Search for products'),
                'relevance' => __('Relevance'),
                'categories' => __('Categories'),
                'products' => __('Products'),
                'suggestions' => __('Suggestions'),
                'searchBy' => __('Search by'),
                'searchForFacetValuesPlaceholder' => __('Search for other ...'),
                'showMore' => __('Show more products'),
                'searchTitle' => __('Search results for'),
                'placeholder' => __('Search for products, categories, ...'),
                'addToCart' => __('Add to Cart'),
            ],
        ];

        $transport = new DataObject($algoliaJsConfig);
        $this->_eventManager->dispatch('algolia_after_create_configuration', ['configuration' => $transport]);
        return $transport->getData();
    }

    protected function areCategoriesInFacets($facets)
    {
        return in_array('categories', array_column($facets, 'attribute'));
    }

    protected function getUrlTrackedParameters()
    {
        $urlTrackedParameters = ['query', 'attribute:*', 'index'];

        if ($this->getConfigHelper()->isInfiniteScrollEnabled() === false) {
            $urlTrackedParameters[] = 'page';
        }

        return $urlTrackedParameters;
    }

    protected function getOrderedProductIds(ConfigHelper $configHelper, Http $request)
    {
        $ids = [];

        if ($configHelper->getConversionAnalyticsMode() === 'disabled'
            || $request->getFrontName() !== 'checkout'
            || $request->getActionName() !== 'success') {
            return $ids;
        }

        $lastOrder = $this->getLastOrder();
        if (!$lastOrder) {
            return $ids;
        }

        $items = $lastOrder->getItems();
        foreach ($items as $item) {
            $ids[] = $item->getProductId();
        }

        return $ids;
    }

    protected function isLandingPage(): bool
    {
        return $this->getRequest()->getFullActionName() === 'algolia_landingpage_view';
    }

    protected function getLandingPageId()
    {
        return $this->isLandingPage() ? $this->getCurrentLandingPage()->getId() : '';
    }

    protected function getLandingPageQuery()
    {
        return $this->isLandingPage() ? $this->getCurrentLandingPage()->getQuery() : '';
    }

    protected function getLandingPageConfiguration()
    {
        return $this->isLandingPage() ? $this->getCurrentLandingPage()->getConfiguration() : json_encode([]);
    }

    public function canLoadInstantSearch(): bool
    {
        return $this->getConfigHelper()->isInstantEnabled()
            && $this->isProductListingPage();
    }

    protected function isProductListingPage(): bool
    {
        return $this->isSearchPage() || $this->isLandingPage();
    }
}
