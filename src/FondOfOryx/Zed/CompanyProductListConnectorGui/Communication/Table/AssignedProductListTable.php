<?php

namespace FondOfOryx\Zed\CompanyProductListConnectorGui\Communication\Table;

use FondOfOryx\Zed\CompanyProductListConnectorGui\Communication\Controller\EditController;
use Orm\Zed\ProductList\Persistence\Map\SpyProductListCompanyTableMap;
use Orm\Zed\ProductList\Persistence\Map\SpyProductListTableMap;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Spryker\Zed\Gui\Communication\Table\TableConfiguration;

class AssignedProductListTable extends AbstractProductListTable
{
    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return \Spryker\Zed\Gui\Communication\Table\TableConfiguration
     */
    protected function configure(TableConfiguration $config): TableConfiguration
    {
        $config = parent::configure($config);
        $config->setUrl(
            sprintf(
                'assigned-product-list-table?%s=%d',
                EditController::PARAM_ID_COMPANY,
                $this->companyTransfer->getIdCompany(),
            ),
        );

        return $config;
    }

    /**
     * @return \Propel\Runtime\ActiveQuery\ModelCriteria
     */
    protected function prepareQuery(): ModelCriteria
    {
        return $this->spyProductListQuery->clear()
            ->where(
                sprintf(
                    '%s IN (SELECT %s FROM %s WHERE %s = ?)',
                    SpyProductListTableMap::COL_ID_PRODUCT_LIST,
                    SpyProductListCompanyTableMap::COL_FK_PRODUCT_LIST,
                    SpyProductListCompanyTableMap::TABLE_NAME,
                    SpyProductListCompanyTableMap::COL_FK_COMPANY,
                ),
                $this->companyTransfer->getIdCompany(),
            )->withColumn(SpyProductListTableMap::COL_ID_PRODUCT_LIST, static::COL_ID)
            ->withColumn(SpyProductListTableMap::COL_TITLE, static::COL_NAME);
    }

    /**
     * @return string
     */
    protected function getCheckboxHeaderName(): string
    {
        return 'Assigned';
    }
}
