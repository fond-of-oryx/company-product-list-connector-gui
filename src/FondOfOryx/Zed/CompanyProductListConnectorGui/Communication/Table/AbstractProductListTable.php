<?php

namespace FondOfOryx\Zed\CompanyProductListConnectorGui\Communication\Table;

use FondOfOryx\Zed\CompanyProductListConnectorGui\Dependency\Service\CompanyProductListConnectorGuiToUtilEncodingServiceInterface;
use FondOfOryx\Zed\CompanyProductListConnectorGui\Dependency\Service\CompanyProductListConnectorGuiToUtilSanitizeServiceInterface;
use Generated\Shared\Transfer\CompanyTransfer;
use Orm\Zed\ProductList\Persistence\Base\SpyProductList;
use Orm\Zed\ProductList\Persistence\Base\SpyProductListQuery;
use Orm\Zed\ProductList\Persistence\Map\SpyProductListTableMap;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\Collection\ObjectCollection;
use Spryker\Zed\Gui\Communication\Table\AbstractTable;
use Spryker\Zed\Gui\Communication\Table\TableConfiguration;

abstract class AbstractProductListTable extends AbstractTable
{
    /**
     * @var string
     */
    public const COL_ID = 'id_product_list';

    /**
     * @var string
     */
    public const COL_NAME = 'name';

    /**
     * @var string
     */
    public const COL_CHECKBOX = 'checkbox';

    /**
     * @var bool
     */
    public const IS_CHECKBOX_SET_BY_DEFAULT = true;

    /**
     * @var \Orm\Zed\ProductList\Persistence\Base\SpyProductListQuery
     */
    protected $spyProductListQuery;

    /**
     * @var \Generated\Shared\Transfer\CompanyTransfer
     */
    protected $companyTransfer;

    /**
     * @var \FondOfOryx\Zed\CompanyProductListConnectorGui\Dependency\Service\CompanyProductListConnectorGuiToUtilSanitizeServiceInterface
     */
    protected $utilSanitizeService;

    /**
     * @var \FondOfOryx\Zed\CompanyProductListConnectorGui\Dependency\Service\CompanyProductListConnectorGuiToUtilEncodingServiceInterface
     */
    protected $utilEncoding;

    /**
     * @param \Orm\Zed\ProductList\Persistence\Base\SpyProductListQuery $spyProductListQuery
     * @param \Generated\Shared\Transfer\CompanyTransfer $companyTransfer
     * @param \FondOfOryx\Zed\CompanyProductListConnectorGui\Dependency\Service\CompanyProductListConnectorGuiToUtilSanitizeServiceInterface $utilSanitizeService
     * @param \FondOfOryx\Zed\CompanyProductListConnectorGui\Dependency\Service\CompanyProductListConnectorGuiToUtilEncodingServiceInterface $utilEncoding
     */
    public function __construct(
        SpyProductListQuery $spyProductListQuery,
        CompanyTransfer $companyTransfer,
        CompanyProductListConnectorGuiToUtilSanitizeServiceInterface $utilSanitizeService,
        CompanyProductListConnectorGuiToUtilEncodingServiceInterface $utilEncoding
    ) {
        $this->spyProductListQuery = $spyProductListQuery;
        $this->companyTransfer = $companyTransfer;
        $this->utilSanitizeService = $utilSanitizeService;
        $this->utilEncoding = $utilEncoding;
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return \Spryker\Zed\Gui\Communication\Table\TableConfiguration
     */
    protected function configure(TableConfiguration $config): TableConfiguration
    {
        $config->setHeader([
           static::COL_ID => 'ID',
           static::COL_NAME => 'Name',
           static::COL_CHECKBOX => $this->getCheckboxHeaderName(),
        ]);

        $config->setSortable([
             static::COL_ID,
             static::COL_NAME,
         ]);

        $config->setRawColumns([
           static::COL_CHECKBOX,
        ]);

        $config->setSearchable([
            SpyProductListTableMap::COL_TITLE,
        ]);

        return $config;
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return array
     */
    protected function prepareData(TableConfiguration $config): array
    {
        $query = $this->prepareQuery();

        /** @var \Propel\Runtime\Collection\ObjectCollection $customerCollection */
        $customerCollection = $this->runQuery($query, $config, true);

        return $this->buildResultData($customerCollection);
    }

    /**
     * @param \Propel\Runtime\Collection\ObjectCollection|\Orm\Zed\ProductList\Persistence\Base\SpyProductList[] $productListEntities
     *
     * @return array
     */
    protected function buildResultData(ObjectCollection $productListEntities): array
    {
        $tableRows = [];
        foreach ($productListEntities as $productListEntity) {
            $tableRows[] = $this->getRow($productListEntity);
        }

        return $tableRows;
    }

    /**
     * @param \Orm\Zed\ProductList\Persistence\Base\SpyProductList $productListEntity
     *
     * @return array
     */
    protected function getRow(SpyProductList $productListEntity): array
    {
        return [
            static::COL_ID => $productListEntity->getIdProductList(),
            static::COL_NAME => $productListEntity->getTitle(),
            static::COL_CHECKBOX => $this->getCheckboxColumn($productListEntity),
        ];
    }

    /**
     * @param \Orm\Zed\ProductList\Persistence\Base\SpyProductList $productListEntity
     *
     * @return string
     */
    protected function getCheckboxColumn(SpyProductList $productListEntity): string
    {
        return sprintf(
            '<input class="%s" type="checkbox" name="idProductList[]" value="%d" %s data-info="%s" />',
            'js-product-list-checkbox',
            $productListEntity->getIdProductList(),
            static::IS_CHECKBOX_SET_BY_DEFAULT ? 'checked' : '',
            $this->utilSanitizeService->escapeHtml($this->utilEncoding->encodeJson([
               'idProductList' => $productListEntity->getIdProductList(),
               'name' => $this->utilSanitizeService->escapeHtml($productListEntity->getTitle()),
            ])),
        );
    }

    /**
     * @return \Propel\Runtime\ActiveQuery\ModelCriteria
     */
    abstract protected function prepareQuery(): ModelCriteria;

    /**
     * @return string
     */
    abstract protected function getCheckboxHeaderName(): string;
}
